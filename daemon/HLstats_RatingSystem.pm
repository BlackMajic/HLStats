package HLstats_RatingSystem;
#use strict;
#use warnings;
#use diagnostics;
use List::Util qw[min max];

# Public: constructor.
sub new
{
    my $class_name = shift;
    my %params = @_;

    my $self = {};
    bless($self, $class_name);

    $self->{players} = {};
    $self->{c2} = 0.0015; # How much the rd2 increases every second
    $self->{updatetimer} = 1; # Update ratings at fixed intervals?
    $self->{last_update} = 0; # When was the last update?
    $self->{was_updated} = 0; # So we can make sure that the ratings get updated...
    $self->{min_same_time} = 0; # Will get calculated later...
    return $self;
}

# Public: called when a player joins the game and/or a team.
sub player_joined
{
    my ($self, $player) = @_;
    my $playerId = $player->get("playerid");
    my $team = $player->get("team");
    my $timestamp = $main::ev_unixtime;

    #print $playerId, " joined ", $team, "\n";

    # If this is a new player - add it to our records!
    if(!defined($self->{players}{$playerId}))
    {
        #print "Adding element for $playerId\n";
        $self->{players}{$playerId} = {player => $player,
                                       team => $team,
                                       active => 1,
                                       other_players => {}};
    }
    else
    {
        $self->{players}{$playerId}{active} = 1;
        if($team eq $self->{players}{$playerId}{team})
        {
            return;
        }
        $self->{players}{$playerId}{team} = $team;
    }

    # Update info about team mates
    my $pl;
    for $pl (keys %{$self->{players}})
    {
        # Check if this is a team mate
        if($self->same_team($playerId, $pl) && $playerId != $pl)
        {
            # Add this player to the other player
            my $otherpl = $self->{players}->{$pl}->{other_players};
            if(!defined($otherpl->{$playerId})) {
                $otherpl->{$playerId} = {same_time => 0,
                                         score => 0};
            }
            $otherpl->{$playerId}{same_team} = 1;
            $otherpl->{$playerId}{last_update} = $timestamp;
            # Add the other player to this player
            $otherpl = $self->{players}->{$playerId}->{other_players};
            if(!defined($otherpl->{$pl})) {
                $otherpl->{$pl} = {same_time => 0,
                                   score => 0};
            }
            $otherpl->{$pl}{same_team} = 1;
            $otherpl->{$pl}{last_update} = $timestamp;
        }
        else
        {
            # Not on the same team! Update info about the players...
            my $otherpl = $self->{players}->{$pl}->{other_players};
            if(defined($otherpl->{$playerId}) && $otherpl->{$playerId}{same_team}) {
                $otherpl->{$playerId}{same_team} = 0;
                my $timedelta = $timestamp - $otherpl->{$playerId}{last_update};
                $otherpl->{$playerId}{same_time} += $timedelta;
                $otherpl->{$playerId}{last_update} = $timestamp;
            }
            $otherpl = $self->{players}->{$playerId}->{other_players};
            if(defined($otherpl->{$pl}) && $otherpl->{$pl}{same_team}) {
                $otherpl->{$pl}{same_team} = 0;
                my $timedelta = $timestamp - $otherpl->{$pl}{last_update};
                $otherpl->{$pl}{same_time} += $timedelta;
                $otherpl->{$pl}{last_update} = $timestamp;
            }
        }
    }
}

# Public: called when a player receives a score.
sub player_scored
{
    my ($self, $player, $score) = @_;
    my $playerId = $player->get("playerid");
    $self->player_joined($player);

    #print $player->get("name"), " got $score points.\n";
    my $userid = $player->get("userid");
    my $playername = $player->get("name");
	if($g_rating_system_verbose > 0) {
	    &::rcon("$userid \"You got $score points.\"",1);
	}
    my $pl;
    for $pl (keys %{$self->{players}->{$playerId}->{other_players}})
    {
        my $otherplayer = $self->{players}->{$playerId}->{other_players}{$pl};
        if(defined($self->{players}->{$pl}) &&
           defined($self->{players}->{$pl}->{other_players}{$playerId}) &&
           $otherplayer->{same_team})
        {
            $otherplayer->{score} += $score;
            my $thisplayer = $self->{players}->{$pl}->{other_players}{$playerId};
            $thisplayer->{score} -= $score;
            #print $player->get("name"), " ", $otherplayer->{score}, "\n";
            my $userid2 = $self->{players}->{$pl}->{player}->get("userid");
            my $playername2 = $self->{players}->{$pl}->{player}->get("name");
            my $scorediff = sprintf("%+.1f", $otherplayer->{score});
            my $scorediff2 = sprintf("%+.1f", $thisplayer->{score});
			if($g_rating_system_verbose > 1) {
	            &::rcon("$userid \"You vs $playername2: $scorediff\"",1); # Quite verbose score info!
    	        &::rcon("$userid2 \"$playername got $score points ($scorediff2).\"",1);
			}
        }
    }
}

# Public: Called by the main loop, every iteration.
sub update {
    my ($self) = @_;
    my $timestamp = $main::ev_unixtime;
    if($self->{updatetimer}) {
        if($self->{last_update} == 0) {
            $self->{last_update} = $timestamp;
        }
        elsif($timestamp - $self->{last_update} >= 600) {
            $self->update_ratings();
        }
    }
}

# Public: Called when a new round is detected.
sub round_start
{
    my ($self) = @_;
    if($self->{updatetimer}) {
        $self->{updatetimer} = 0; # Disable time based updates.
    }
    elsif($self->{was_updated} == 0) {
        # The ratings haven't been updated since last time. We'd better do it here (round_end doesn't seem to work).
        $self->update_ratings();
    }
    $self->{was_updated} = 0; # We'll check next time to see if the ratings have been updated...
}

# Public: Called when the end of a round is detected.
sub round_end
{
    my ($self) = @_;
    $self->update_ratings();
    $self->{updatetimer} = 0; # Disable time based updates (just to be sure).
    $self->{was_updated} = 1; # Make sure round_start knows we have updated the ratings.
}

# Private: update ratings!
sub update_ratings
{
    my ($self) = @_;
    my $timestamp = $main::ev_unixtime;

    # Two players need to have been on the same team 50 % of the time.
    $self->{min_same_time} = ($self->{last_update} - $timestamp)/2;
    #print "\n## Updating ratings! ",$main::ev_timestamp,"\n";
    $self->update_players();
    $self->calc_old_ratings();
    $self->calc_new_ratings();
    $self->clean_players();
    # Also update the rd2 of other players (but only if it's more than 1 day old)
    my $now = "FROM_UNIXTIME($timestamp)";
    my $one_day_ago = $timestamp-60*60*24;
    my $c2 = $self->{c2};
    &main::doQuery("
        UPDATE ".$main::db_prefix."_Players
        SET rd2 = IF(rd2+$c2*TIMESTAMPDIFF(SECOND,rating_last,$now) < 122500,
                     rd2+$c2*TIMESTAMPDIFF(SECOND,rating_last,$now), 122500),
            rating_last = $now
        WHERE rd2 < 122500 AND rating_last < FROM_UNIXTIME($one_day_ago)
    ");
    $self->{last_update} = $timestamp;
}

sub update_rd2
{
    my ($self, $player) = @_;
    my $timestamp = $main::ev_unixtime;

    my $last_update = $player->get("rating_last");
    my $rd2 = $player->get("rd2");

    if($last_update > 1 && $last_update < $timestamp)
    {
        my $c2 = $self->{c2};
        my $t = $timestamp - $last_update;
        my $rd2 = min($rd2 + $c2*$t, 122500.0);
        $player->set("rd2", $rd2);
    }
    $player->set("rating_last", $timestamp);
    $player->updateDB();
}

sub calc_old_ratings
{
    my ($self) = @_;
    my $playerId;
    for $playerId (keys %{$self->{players}})
    {
        my $player = $self->{players}{$playerId};
        $self->update_rd2($player->{player});
        $player->{rating} = $player->{player}->get("rating");
        $player->{rd2} = $player->{player}->get("rd2");
    }
}

sub g
{
    my ($rd2) = @_;
    return 1.0/sqrt(1.0 + $rd2*1.0072398601963979e-05);
}

sub E
{
    my ($rating, $opponent_rating, $opponent_rd2) = @_;
    my $rating_diff = $opponent_rating - $rating;
    my $g = g($opponent_rd2);
    return 1.0/(1.0+10.0**($g*$rating_diff/400.0));
}

sub d2
{
    my ($self, $rating, $opponents) = @_;
    my $sum = 0.0;
    my $playerId;
    for $playerId (keys %{$opponents})
    {
        if($opponents->{$playerId}->{score} != 0 &&
            $opponents->{$playerId}->{same_time} > $self->{min_same_time} &&
            defined($self->{players}{$playerId}))
        {
            my $player = $self->{players}{$playerId};
            my $g = g($player->{rd2})**2;
            my $E = E($rating, $player->{rating}, $player->{rd2});
            $sum += $g*$E*(1.0 - $E);
        }
    }
    if($sum == 0)
    {
        return -1;
    }
    $sum *= 3.3136863190489995e-05;
    return 1.0/$sum;
}

sub calc_rating
{
    my ($self, $rating, $rd2, $opponents, $d2, $thisplayer) = @_;
    my $sum = 0.0;
    my $playerId;
    my $betterthan = 0;
    my $worsethan = 0;
    my $totalother = 0;
    for $playerId (keys %{$opponents})
    {
        if($opponents->{$playerId}->{same_time} > $self->{min_same_time} &&
            defined($self->{players}{$playerId}))
        {
            $totalother += 1;
            if($opponents->{$playerId}->{score} != 0)
            {
                #print $opponents->{$playerId}->{score}, " ", $self->{players}{$playerId}{other_players}{$thisplayer}{score}, " ",$opponents->{$playerId}->{same_time}, "\n";
                my $s = 0.0;
                if($opponents->{$playerId}->{score} > 0.0)
                {
                    $s = 1.0;
                    $betterthan += 1;
                }
                else
                {
                    $worsethan += 1;
                }
                my $player = $self->{players}{$playerId};
                my $g = g($player->{rd2})**2;
                my $E = E($rating, $player->{rating}, $player->{rd2});
                $sum += $g*($s - $E);
            }
        }
    }
    $sum *= 0.0057564627324851146/(1.0/$rd2 + 1.0/$d2);
    #print "$sum\n";
    my $userid = $self->{players}->{$thisplayer}->{player}->get("userid");
	if($g_rating_system_verbose > 2) {
	    &::rcon("$userid \"New rating period. Comparing to $totalother other players.\"");
	    &::rcon("$userid \"You scored higher than $betterthan and lower than $worsethan.\"",1);
	}
    return $rating + $sum;
}

sub calc_rd2
{
    my ($self, $rd2, $d2) = @_;
    my $new_rd2 = 1.0/(1.0/$rd2 + 1.0/$d2);
    return max($new_rd2, 900);
}

sub calc_new_ratings
{
    my ($self) = @_;
    my $playerId;
    my $q = 0.0057564627324851146;
    my $sum = 0.0;
    for $playerId (keys %{$self->{players}})
    {
        my $player = $self->{players}{$playerId};
        my $d2 = $self->d2($player->{rating}, $player->{other_players});
        if($d2 > 0) {
            my $rating = $self->calc_rating($player->{rating}, $player->{rd2}, $player->{other_players}, $d2, $playerId);
            my $rd2 = $self->calc_rd2($player->{rd2}, $d2);
            $sum += $rating-$player->{rating};
            $player->{player}->set("rating", $rating);
            $player->{player}->set("rd2", $rd2);
            $player->{player}->updateDB();
            #print "$rating ",sqrt($rd2), " ",$rating-$player->{rating},"\n\n";
            my $ratingstr = sprintf("%d", $rating);
            my $ratingdiff = sprintf("%+d", $rating - $player->{rating});
            my $new_rd = sqrt($rd2);
            my $rd = sprintf("%.0f", $new_rd);
            my $rd_diff = sprintf("%+.0f", $new_rd - sqrt($player->{rd2}));
            my $userid = $self->{players}->{$playerId}->{player}->get("userid");
			if($g_rating_system_verbose > 2) {
	            &::rcon("$userid \"New rating: $ratingstr \($ratingdiff\)  RD: $rd \($rd_diff\)\"",1);
			}
        }
        else {
            my $userid = $self->{players}->{$playerId}->{player}->get("userid");
            my $ratingstr = sprintf("%d", $player->{rating});
            my $rd = sprintf("%.0f", sqrt($player->{rd2}));
			if($g_rating_system_verbose > 2) {
            	&::rcon("$userid \"New rating period. Comparing to 0 other players.\"",1);
	            &::rcon("$userid \"Current rating: $ratingstr  RD: $rd\"",1);
			}
        }
    }
    #print $sum;
}

sub same_team
{
    my ($self, $playerId1, $playerId2) = @_;
    my $pl1 = $self->{players}{$playerId1};
    my $pl2 = $self->{players}{$playerId2};
    if($pl1->{player}->get("server") eq $pl2->{player}->get("server") &&
       $pl1->{player}->get("team") eq $pl2->{player}->get("team"))
    {
        my $team = $pl1->{player}->get("team");
        return 1;
    }
    return 0;
}

sub update_players
{
    my ($self) = @_;
    my $timestamp = $main::ev_unixtime;
    my $playerId;
    for $playerId (keys %{$self->{players}})
    {
        my $player = $self->{players}{$playerId};
        my $pl;
        for $pl (keys %{$player->{other_players}})
        {
            my $otherplayer = $player->{other_players}{$pl};
            if($otherplayer->{same_team})
            {
                my $timedelta = $timestamp - $otherplayer->{last_update};
                $otherplayer->{same_time} += $timedelta;
                $otherplayer->{last_update} = $timestamp;
            }
        }
    }
}

sub clean_players
{
    my ($self) = @_;
    my $timestamp = $main::ev_unixtime;
    my $playerId;
    for $playerId (keys %{$self->{players}})
    {
        my $player = $self->{players}{$playerId};
        my $sum = 0;
        my $pl;
        for $pl (keys %{$player->{other_players}})
        {
            my $otherplayer = $player->{other_players}{$pl};
            if(!defined($self->{players}{$pl}) || !$self->{players}{$pl}{active} || !$otherplayer->{same_team})
            {
                delete($player->{other_players}{$pl});
            }
            else
            {
                $player->{other_players}{$pl}{score} = 0;
                $player->{other_players}{$pl}{same_time} = 0;
                $player->{other_players}{$pl}{last_update} = $timestamp;
                $sum += 1;
            }
        }
        #print $self->{players}{$playerId}{player}{name}, " ", $sum, "\n";
        if(!$player->{active})
        {
            #print "# Deleting $playerId ", $self->{players}{$playerId}{player}{name}, "\n";
            delete($self->{players}{$playerId});
        }
        else
        {
            $self->{players}{$playerId}{active} = 0;
        }
    }
}

1;
