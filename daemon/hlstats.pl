#!/usr/bin/perl
#
# Original development:
# +
# + HLStats - Real-time player and clan rankings and statistics for Half-Life
# + http://sourceforge.net/projects/hlstats/
# +
# + Copyright (C) 2001  Simon Garner
# +
#
# Additional development:
# +
# + UA HLStats Team
# + http://www.unitedadmins.com
# + 2004 - 2007
# +
#
#
# Current development:
# +
# + Johannes 'Banana' Keßler
# + http://hlstats.sourceforge.net
# + 2007 - 2010
# +
#
# HLStats - Real-time player and clan rankings and statistics for Half-Life
# http://sourceforge.net/projects/hlstats/
#
# Copyright (C) 2001  Simon Garner
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#

##
## Settings
##

# $opt_configfile_name - Filename of configuration file.
$opt_configfile_name = "hlstats.conf.ini";

##
##
################################################################################
## No need to edit below this line
##

use strict;
no strict 'vars';
use POSIX;
use Getopt::Long;
use Time::Local;
use IO::Socket;
use DBI;
use Digest::MD5;
use File::Basename;

use Config::Tiny; ## new config syntax

$opt_libdir = dirname(__FILE__);
$opt_configfile = "$opt_libdir/$opt_configfile_name";

require "$opt_libdir/KKrcon.pm";
require "$opt_libdir/HLstats_Server.pm";
require "$opt_libdir/HLstats_Player.pm";
require "$opt_libdir/HLstats.plib";
require "$opt_libdir/HLstats_EventHandlers.plib";
require "$opt_libdir/HLstats_RatingSystem.pm";

$|=1;
Getopt::Long::Configure ("bundling");

##
## MAIN
##

## load config with config-tiny module
$Config = Config::Tiny->read("$opt_libdir/hlstats.conf.ini");
if($Config::Tiny::errstr ne '') {
	print "Config file not found !\n";
	print $Config::Tiny::errstr;
	print "\n";
	exit(0)
}
$db_name = $Config->{Database}->{DBName};
$db_host = $Config->{Database}->{DBHost};
$db_user = $Config->{Database}->{DBUsername};
$db_pass = $Config->{Database}->{DBPassword};
$db_prefix = $Config->{Database}->{DBPrefix};
$db_lowpriority = $Config->{Database}->{DBLowPriority};

# @todo: renames
$s_ip = $Config->{System}->{BindIP};
$s_port = $Config->{System}->{Port};
$g_mailto = $Config->{System}->{MailTo};
$g_mailpath = $Config->{System}->{MailPath};
$g_debug = $Config->{System}->{DebugLevel};
$g_stdin = $Config->{System}->{Stdin};
$g_server_ip = $Config->{System}->{ServerIP};
$g_server_port = $Config->{System}->{ServerPort};
$g_timestamp = $Config->{System}->{Timestamp};
$g_dns_resolveip = $Config->{System}->{DNSResolveIP};
$g_dns_timeout = $Config->{System}->{DNSTimeout};

$g_mode = $Config->{Options}->{Mode};
$g_deletedays = $Config->{Options}->{DeleteDays};
$g_rcon = $Config->{Rcon}->{Rcon};
$g_rcon_record = $Config->{Rcon}->{RconRecord};
$g_rcon_ignoreself = $Config->{Rcon}->{RconIgnoreSelf};
$g_minplayers = $Config->{Options}->{MinPlayers};
$g_skill_maxchange = $Config->{Options}->{SkillMaxChange};
$g_log_chat = $Config->{Options}->{LogChat};
$g_rcon_say = $Config->{Rcon}->{RconSay};
$g_ignore_bots = $Config->{Options}->{IgnoreBots};
$g_ingame_points = $Config->{Options}->{IngamePoints};
$g_rating_system = $Config->{Options}->{EloRating};
$g_rating_system_verbose = $Config->{Options}->{EloRatingVerbose};
$g_option_strip_tags = $Config->{Options}->{StripTags};

# Options
# default values

$opt_help = 0;
$opt_version = 0;
$g_lan_hack = 1;


# Usage message

$usage = <<EOT
Usage: hlstats.pl [OPTION]...
Collect statistics from one or more Half-Life servers for insertion into
a MySQL database.

  -h, --help                      display this help and exit
  -v, --version                   output version information and exit
  -d, --debug                     enable debugging output (-dd for more)
  -m, --mode=MODE                 player tracking mode (Normal, LAN or NameTrack)  [$g_mode]
      --db-host=HOST              database ip or ip:port  [$db_host]
      --db-name=DATABASE          database name  [$db_name]
      --db-password=PASSWORD      database password (WARNING: specifying the
                                    password on the command line is insecure.
                                    Use the configuration file instead.)
      --db-username=USERNAME      database username
      --dns-resolveip             resolve player IP addresses to hostnames
                                    (requires working DNS)
      --nodns-resolveip           disables above
      --dns-timeout=SEC           timeout DNS queries after SEC seconds  [$g_dns_timeout]
  -i, --ip=IP                     set IP address to listen on for UDP log data
  -p, --port=PORT                 set port to listen on for UDP log data  [$s_port]
  -r, --rcon                      enables rcon command exec support (the default)
      --norcon                    disables rcon command exec support
  -s, --stdin                     read log data from standard input, instead of
                                    from UDP socket. Must specify --server-ip
                                    and --server-port to indicate the generator
                                    of the inputted log data
      --nostdin                   disables above
      --server-ip                 specify data source IP address for --stdin
      --server-port               specify data source port for --stdin  [$g_server_port]
  -t, --timestamp                 tells HLStats to use the timestamp in the log
                                    data, instead of the current time on the
                                    database server, when recording events
      --notimestamp               disables above

Long options can be abbreviated, where such abbreviation is not ambiguous.
Default values for options are indicated in square brackets [...].

Most options can be specified in the configuration file:
  $opt_configfile
Note: Options set on the command line take precedence over options set in the
configuration file. The configuration file name is set at the top of hlstats.pl.

HLStats: http://www.hlstats-community.org
EOT
;

# Read Command Line Arguments

GetOptions(
	"help|h"			=> \$opt_help,
	"version|v"			=> \$opt_version,
	"debug|d+"			=> \$g_debug,
#	"nodebug|n+"		=> \$g_nodebug,
	"mode|m=s"			=> \$g_mode,
	"db-host=s"			=> \$db_host,
	"db-name=s"			=> \$db_name,
	"db-password=s"		=> \$db_pass,
	"db-username=s"		=> \$db_user,
	"dns-resolveip!"	=> \$g_dns_resolveip,
	"dns-timeout=i"		=> \$g_dns_timeout,
	"ip|i=s"			=> \$s_ip,
	"port|p=i"			=> \$s_port,
	"rcon!"				=> \$g_rcon,
	"r"					=> \$g_rcon,
	"stdin!"			=> \$g_stdin,
	"s"					=> \$g_stdin,
	"server-ip=s"		=> \$g_server_ip,
	"server-port=i"		=> \$g_server_port,
	"timestamp!"		=> \$g_timestamp,
	"t"					=> \$g_timestamp
) or die($usage);

if ($opt_help) {
	print $usage;
	exit(0);
}

if ($opt_version) {
	print "hlstats.pl (HLStats) $g_version\n"
		. "Real-time player and clan rankings and statistics for Half-Life\n"
		. "Copyright (C) 2001  Simon Garner\n\n";

	print "Using ConfigReaderSimple module version $ConfigReaderSimple::VERSION\n";
	if ($g_rcon) {
		print "Using KKrcon module version $KKrcon::VERSION\n";
	}

	print "\nThis is free software; see the source for copying conditions.  There is NO\n"
		. "warranty; not even for MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n";

	exit(0);
}

# Startup

print "++ HLStats $g_version starting...\n\n";


# Create the UDP socket

if ($g_stdin) {
	print "-- UDP listen socket disabled, reading log data from STDIN.\n";

	if (!$g_server_ip || !$g_server_port)
	{
		print "-> ERROR: Must specify source of STDIN data using --server-ip and --server-port\n";
		print "-> Example: ./hlstats.pl --stdin --server-ip 12.34.56.78 --server-port 27015\n\n";
		exit(255);
	}
	else
	{
		print "-> All data from STDIN will be allocated to server '$g_server_ip:$g_server_port'.\n";
		$s_peerhost = $g_server_ip;
		$s_peerport = $g_server_port;
	}
}
else {
	if ($s_ip) { $ip = $s_ip . ":"; } else { $ip = "port "; }
	print "-- Opening UDP listen socket on $ip$s_port ... ";

	$s_socket = IO::Socket::INET->new(
		Proto=>"udp",
		LocalAddr=>"$s_ip",
		LocalPort=>"$s_port"
	) or die ("\nCan't setup UDP socket on $ip$s_port: $!\n");

	print "opened OK\n";
}

# Connect to the database

print "-- Connecting to MySQL database '$db_name' on '$db_host' as user '$db_user' ... ";

$db_conn = DBI->connect(
	"DBI:mysql:$db_name:$db_host",
	$db_user, $db_pass, { RaiseError => 1, "mysql_enable_utf8" => 1, 'mysql_auto_reconnect' => 1,
				'ShowErrorStatement' => 1 }
) or die ("\nCan't connect to MySQL database '$db_name' on '$db_host'\n" .
	"Server error: $DBI::errstr\n");

&doQuery("SET character set utf8");
&doQuery("SET NAMES utf8");


print "connected OK\n";

%g_servers = ();
%g_players = ();

%g_eventTables = (
	"TeamBonuses",
		["playerId", "actionId", "bonus"],
	"ChangeRole",
		["playerId", "role"],
	"ChangeName",
		["playerId", "oldName", "newName"],
	"ChangeTeam",
		["playerId", "team"],
	"Connects",
		["playerId", "ipAddress", "hostname", "hostgroup"],
	"Disconnects",
		["playerId"],
	"Entries",
		["playerId"],
	"Frags",
		["killerId", "victimId", "weapon"],
	"PlayerActions",
		["playerId", "actionId", "bonus"],
	"PlayerPlayerActions",
		["playerId", "victimId", "actionId", "bonus"],
	"Suicides",
		["playerId", "weapon"],
	"Teamkills",
		["killerId", "victimId", "weapon"],
	"Rcon",
		["type", "remoteIp", "password", "command"],
	"Admin",
		["type", "message", "playerName"],
	"Statsme",
		["playerId", "weapon", "shots", "hits", "headshots", "damage", "kills", "deaths"],
	"Statsme2",
		["playerId", "weapon", "head", "chest", "stomach", "leftarm", "rightarm", "leftleg", "rightleg"],
	"StatsmeLatency",
		["playerId", "ping"],
	"StatsmeTime",
		["playerId", "time"],
	"Chat",
		["playerId","type","message"]
);

# Finding all tables for auto optimisation
$result = &doQuery("SHOW TABLES");
while ( ($row) = $result->fetchrow_array ) {
	push(@g_allTables, $row);
}
$result->finish;

print "\n++ HLStats is now running ($g_mode mode";
if ($g_debug > 0) { print ", debug level $g_debug"; }
print ").\n\n";

# Init rating system
if($g_rating_system eq "1" || $g_rating_system eq "2") {
	$ratingsys = HLstats_RatingSystem->new();
}

#
# Main data loop
#
$c = 0;
while ($loop = &getLine()) {

	if ($g_stdin){
		$s_output = $loop;
	}
	else {
		$s_socket->recv($s_output, 1024);
		$s_peerhost = $s_socket->peerhost;
		$s_peerport = $s_socket->peerport;
	}

	$s_addr = "$s_peerhost:$s_peerport";

	## unwanted chars
	$s_output =~ s/[\r\n\0]//g;	# remove naughty characters

	if($g_option_strip_tags) {
		$s_output =~ s/\[No.C-D\]//g;	# remove [No C-D] tag
		$s_output =~ s/\[OLD.C-D\]//g;	# remove [OLD C-D] tag
		$s_output =~ s/\[NOCL\]//g;	# remove [NOCL] tag
		$s_output =~ s/\([0-9]\)//g;	# strip (1) and (2) from player names
	}

	# Get the server info, if we know the server, otherwise ignore the data
	if (!$g_servers{$s_addr}) {
		$g_servers{$s_addr} = &getServer($s_peerhost, $s_peerport);

		if (!$g_servers{$s_addr}) {
			&printEvent(997, "UNRECOGNISED SERVER: " . $s_output);
			next;
		}
	}

	# Get the datestamp (or complain)
	# otherwise ignore the data and proceed to the next loop
	if ($s_output =~ s/^.*L (\d\d)\/(\d\d)\/(\d{4}) - (\d\d):(\d\d):(\d\d):\s*//) {
		$ev_month = $1;
		$ev_day   = $2;
		$ev_year  = $3;
		$ev_hour  = $4;
		$ev_min   = $5;
		$ev_sec   = $6;

		$ev_time  = "$ev_hour:$ev_min:$ev_sec";

		if ($g_timestamp)
		{
			$ev_timestamp = "$ev_year-$ev_month-$ev_day $ev_time";
			$ev_datetime  = "'$ev_timestamp'";
			$ev_unixtime  = timelocal($ev_sec,$ev_min,$ev_hour,$ev_day,$ev_month-1,$ev_year);
		}
		else
		{
			my ($sec,$min,$hour,$mday,$mon,$year) = localtime(time());
			$ev_timestamp = sprintf("%04d-%02d-%02d %02d:%02d:%02d",
				$year+1900, $mon+1, $mday, $hour, $min, $sec);
			$ev_datetime  = "NOW()";
			$ev_unixtime  = time();
		}
	}
	else {
		&printEvent(998, "MALFORMED DATA: " . $s_output);
		next;
	}

	# Now we parse the events.

	my $ev_type   = 0;
	my $ev_status = "";
	my $ev_team   = "";
	my $ev_player = 0;
	my $ev_verb   = "";
	my $ev_obj_a  = "";
	my $ev_obj_b  = "";
	my $ev_obj_c  = "";
	my $ev_properties = "";
	my %ev_properties = ();
	my %ev_player = ();

	if ($s_output =~ /^"([^"]+)" ([^"\(]+) "([^"]+)" [^"\(]+ "([^"]+)"(.*)$/) {

		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M1\n";
		}

		# Prototype: "player" verb "obj_a" ?... "obj_b"[properties]
		# Matches:
		#  8. Kills
		#  9. Injuring
		# 10. Player-Player Actions
		# 11. Player Objectives/Actions

		$ev_player = $1;
		$ev_verb   = $2; # killed; attacked; triggered
		$ev_obj_a  = $3; # victim; action
		$ev_obj_b  = $4; # weapon; victim
		$ev_properties = $5;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "killed")) {
			my $killerinfo = &getPlayerInfo($ev_player);
			my $victiminfo = &getPlayerInfo($ev_obj_a);

			$ev_type = 8;

			if ($killerinfo && $victiminfo)
			{
				$ev_status = &doEvent_Frag(
					$killerinfo->{"userid"},
					$victiminfo->{"userid"},
					$ev_obj_b
				);
			}
		}
		elsif (like($ev_verb, "attacked")) {
			$ev_type = 9;
			$ev_status = "(IGNORED) $s_output";
		}
		elsif (like($ev_verb, "triggered"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);
			my $victiminfo = &getPlayerInfo($ev_obj_b);

			$ev_type = 10;

			if ($playerinfo && $victiminfo)
			{
				$ev_status = &doEvent_PlayerPlayerAction(
					$playerinfo->{"userid"},
					$victiminfo->{"userid"},
					$ev_obj_a
				);
			}
		}
		elsif (like($ev_verb, "triggered a"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 11;

			if ($playerinfo)
			{
				$ev_status = &doEvent_PlayerAction(
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
	}
	elsif ( $s_output =~ /^(?:\[STATSME\] )?"([^"]+)" triggered "(weaponstats\d{0,1})"(.*)$/ )
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M2\n";
		}

		# Prototype: [STATSME] "player" triggered "weaponstats?"[properties]
		# Matches:
		# 501. Statsme weaponstats
		# 502. Statsme weaponstats2

		$ev_player = $1;
		$ev_verb   = $2; # weaponstats; weaponstats2
		$ev_properties = $3;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "weaponstats"))
		{
			$ev_type = 501;

			my $playerinfo = &getPlayerInfo($ev_player);

			if ($playerinfo)
			{
				if ($ev_properties{"weapon"} eq "hegrenade") {
					$ev_properties{"weapon"} = "grenade";
				}

				$ev_status = &doEvent_Statsme(
					$playerinfo->{"userid"},
					$ev_properties{"weapon"},
					$ev_properties{"shots"},
					$ev_properties{"hits"},
					$ev_properties{"headshots"},
					$ev_properties{"damage"},
					$ev_properties{"kills"},
					$ev_properties{"deaths"}
				);
			}
		}
		elsif (like($ev_verb, "weaponstats2"))
		{
			$ev_type = 502;

			my $playerinfo = &getPlayerInfo($ev_player);

			if ($playerinfo)
				{
				if ($ev_properties{"weapon"} eq "hegrenade") {
					$ev_properties{"weapon"} = "grenade";
				}

				$ev_status = &doEvent_Statsme2(
					$playerinfo->{"userid"},
					$ev_properties{"weapon"},
					$ev_properties{"head"},
					$ev_properties{"chest"},
					$ev_properties{"stomach"},
					$ev_properties{"leftarm"},
					$ev_properties{"rightarm"},
					$ev_properties{"leftleg"},
					$ev_properties{"rightleg"}
				);
			}
		}
	}
	elsif ( $s_output =~ /^(?:\[STATSME\] )?"([^"]+)" triggered "(latency|time)"(.*)$/ )
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M3\n";
		}

		# Prototype: [STATSME] "player" triggered "latency|time"[properties]
		# Matches:
		# 503. Statsme latency
		# 504. Statsme time

		$ev_player = $1;
		$ev_verb   = $2; # latency; time
		$ev_properties = $3;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "latency"))
		{
			$ev_type = 503;

			my $playerinfo = &getPlayerInfo($ev_player);

			if ($playerinfo)
			{
				$ev_status = &doEvent_Statsme_Latency(
					$playerinfo->{"userid"},
					$ev_properties{"ping"}
				);
			}
		}
		elsif (like($ev_verb, "time"))
		{
			$ev_type = 504;

			my $playerinfo = &getPlayerInfo($ev_player);

			if ($playerinfo)
			{
				my ($min, $sec) = split(/:/, $ev_properties{"time"});

				my $hour = sprintf("%d", $min / 60);

				if ($hour) {
					$min = $min % 60;
				}

				$ev_status = &doEvent_Statsme_Time(
					$playerinfo->{"userid"},
					"$hour:$min:$sec"
				);
			}
		}
	}
	elsif ($s_output =~ /^"([^"]+)" ([^"\(]+) "([^"]+)"(.*)$/)
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M4\n";
		}

		# Prototype: "player" verb "obj_a"[properties]
		# Matches:
		#  1. Connection
		#  4. Suicides
		#  5. Team Selection
		#  6. Role Selection
		#  7. Change Name
		# 11. Player Objectives/Actions
		# 14. a) Chat; b) Team Chat

		$ev_player = $1;
		$ev_verb   = $2;
		$ev_obj_a  = $3;
		$ev_properties = $4;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "connected, address"))
		{
			my $ipAddr = $ev_obj_a;
			my $playerinfo;

			if ($ipAddr =~ /([\d.]+):(\d+)/) {
				$ipAddr = $1;
			}

			if ($g_mode eq "LAN") {
				if($ipAddr eq '127.0.0.1' || $ipAddr eq 'loopback') {
					$ipAddr = $s_peerhost;
				}
				$playerinfo = &getPlayerInfo($ev_player, $ipAddr);
			}
			else {
				$playerinfo = &getPlayerInfo($ev_player);
			}

			$ev_type = 1;

			if ($playerinfo) {
				if (($playerinfo->{"uniqueid"} =~ /PENDING/)
					|| ($playerinfo->{"uniqueid"} =~ /VALVE_ID_LAN/)
					|| ($playerinfo->{"uniqueid"} =~ /STEAM_ID_LAN/)
					)
				{
					$ev_status = "(DELAYING CONNECTION): $s_output";

					$g_preconnect->{$playerinfo->{"userid"}} = {
						ipaddress => $ipAddr,
						name => $playerinfo->{"name"},
						server => $s_addr
					};
				}
				else {
					$ev_status = &doEvent_Connect(
						$playerinfo->{"userid"},
						$ipAddr
					);
				}
			}
		}
		elsif (like($ev_verb, "committed suicide with"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 4;

			if ($playerinfo)
			{
				$ev_status = &doEvent_Suicide(
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
		elsif (like($ev_verb, "joined team"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 5;

			if ($playerinfo)
			{
				$ev_status = &doEvent_TeamSelection(
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
		elsif (like($ev_verb, "changed role to"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 6;

			if ($playerinfo)
			{
				$ev_status = &doEvent_RoleSelection(
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
		elsif (like($ev_verb, "changed name to"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 7;

			if ($playerinfo)
			{
				$ev_status = &doEvent_ChangeName(
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
		elsif (like($ev_verb, "triggered"))
		{
			# tfc2 matches also buildobject
			# and killedobject
			# # but without additional object data
			#
			# also flagevents are matched here.
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 11;

			if ($playerinfo) {
				$ev_status = &doEvent_PlayerAction(
					$playerinfo->{"userid"},
					$ev_obj_a,
					%ev_properties
				);
			}
		}
		elsif (like($ev_verb, "triggered a"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 11;

			if ($playerinfo)
			{
				$ev_status = &doEvent_PlayerAction(
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
		elsif (like($ev_verb, "say"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 14;

			if ($playerinfo)
			{
				$ev_status = &doEvent_Chat(
					"say",
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
		elsif (like($ev_verb, "say_team"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 14;

			if ($playerinfo)
			{
				$ev_status = &doEvent_Chat(
					"say_team",
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
	}
	elsif ($s_output =~ /^"([^"]+)" ([^\(]+)(.*)$/)
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M5\n";
		}

		# Prototype: "player" verb[properties]
		# Matches:
		#  2. Enter Game
		#  3. Disconnection
		#  l4d addition 46. spawned aka. the role/model change

		$ev_player = $1;
		$ev_verb   = $2;
		$ev_properties = $3;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "entered the game"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			if ($playerinfo)
			{
				$ev_type = 2;

				$ev_status = &doEvent_EnterGame(
					$playerinfo->{"userid"},
					$ev_obj_a
				);
			}
		}
		elsif (like($ev_verb, "disconnected"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			if ($playerinfo)
			{
				$ev_type = 3;

				$userid = $playerinfo->{"userid"};

				if ($g_lan_hack && defined($g_players{"$s_addr/$userid"})
					&& $g_players{"$s_addr/$userid"}->get("uniqueid") !~ /^BOT:/)
				{
					$g_lan_noplayerinfo_hack->{"$userid"} = {
						ipaddress => $g_players{"$s_addr/$userid"}->get("uniqueid"),
						name => $playerinfo->{"name"},
						server => $s_addr
					};
				}

				$ev_status = &doEvent_Disconnect(
					$playerinfo->{"userid"}
				);

				$g_servers{$s_addr}->{numplayers}-- if ($playerinfo->{"uniqueid"} !~ /PENDING/);
				&printNotice("NumPlayers ($s_addr): $g_servers{$s_addr}->{numplayers} (Disconnect)");
			}
		}
		elsif (like($ev_verb, "STEAM USERID validated") || like($ev_verb, "VALVE USERID validated"))
		{
			my $playerinfo = &getPlayerInfo($ev_player);

			if ($playerinfo)
			{
				$ev_type = 1;

				if ( ($g_preconnect->{$playerinfo->{"userid"}}->{"name"} eq $playerinfo->{"name"})
					&& ($g_preconnect->{$playerinfo->{"userid"}}->{"server"} eq $s_addr) )
				{
					$ev_status = &doEvent_Connect(
						$playerinfo->{"userid"},
						$g_preconnect->{$playerinfo->{"userid"}}->{"ipaddress"}
					);
				}
			}
		}
		elsif(like($ev_verb,"spawned")) {
			#
			# L4D role/model change support
			# the role attribute is custom for l4d
			#
			my $playerinfo = &getPlayerInfo($ev_player);

			$ev_type = 46;

			if ($playerinfo)
			{
				$ev_status = &doEvent_RoleSelection(
					$playerinfo->{"userid"},
					$playerinfo->{"role"}
				);
			}

		}
	}
	elsif ($s_output =~ /^Team "([^"]+)" ([^"\(]+) "([^"]+)" [^"\(]+ "([^"]+)" [^"\(]+(.*)$/)
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M6\n";
		}

		# Prototype: Team "team" verb "obj_a" ?... "obj_b" ?...[properties]
		# Matches:
	    # 16. Round-End Team Score Report

		$ev_team   = $1;
		$ev_verb   = $2;
		$ev_obj_a  = $3;
		$ev_obj_b  = $4;
		$ev_properties = $5;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "scored"))
		{
			$ev_type = 16;
			$ev_status = &doEvent_TeamScoreReport(
				$ev_team,
				$ev_obj_a,
				$ev_obj_b
			);
		}
	}
	elsif ($s_output =~ /^Team "([^"]+)" ([^"\(]+) "([^"]+)"(.*)$/)
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M7\n";
		}

		# Prototype: Team "team" verb "obj_a"[properties]
		# Matches:
	    # 12. Team Objectives/Actions
		# 15. Team Alliances

		$ev_team   = $1;
		$ev_verb   = $2;
		$ev_obj_a  = $3;
		$ev_properties = $4;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "triggered"))
		{
			$ev_type = 12;
			$ev_status = &doEvent_TeamAction(
				$ev_team,
				$ev_obj_a,
				$ev_properties
			);
		}
		elsif (like($ev_verb, "triggered a"))
		{
			$ev_type = 12;
			$ev_status = &doEvent_TeamAction(
				$ev_team,
				$ev_obj_a
			);
		}
		elsif (like($ev_verb, "formed alliance with team"))
		{
			$ev_type = 15;
			$ev_status = &doEvent_TeamAlliance(
				$ev_team,
				$ev_obj_a
			);
		}
	}
	elsif ($s_output =~ /^([^"\(]+) "([^"]+)" = "([^"]*)"(.*)$/)
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M8\n";
		}

		# Prototype: verb "obj_a" = "obj_b"[properties]
		# Matches:
	    # 17. b) Server cvar "var" = "value"

		$ev_verb   = $1;
		$ev_obj_a  = $2;
		$ev_obj_b  = $3;
		$ev_properties = $4;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "Server cvar"))
		{
			$ev_type = 17;
			$ev_status = &doEvent_ServerCvar(
				"var",
				$ev_obj_a,
				$ev_obj_b
			);
		}
	}
	elsif ($s_output =~ /^(Rcon|Bad Rcon): "rcon [^"]+"([^"]*)"\s+(.+)" from "([^"]+)"(.*)$/)
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M9\n";
		}

		# Prototype: verb: "rcon ?..."obj_a" obj_b" from "obj_c"[properties]
		# Matches:
	    # 20. a) Rcon; b) Bad Rcon

		$ev_verb   = $1;
		$ev_obj_a  = $2; # password
		$ev_obj_b  = $3; # command
		$ev_obj_c  = $4; # ip:port
		$ev_properties = $5;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "Rcon"))
		{
			$ev_type = 20;
			$ev_status = &doEvent_Rcon(
				"OK",
				$ev_obj_b,
				$ev_obj_a,
				$ev_obj_c
			);
		}
		elsif (like($ev_verb, "Bad Rcon"))
		{
			$ev_type = 20;
			$ev_status = &doEvent_Rcon(
				"BAD",
				$ev_obj_b,
				$ev_obj_a,
				$ev_obj_c
			);
		}
	}
	elsif ($s_output =~ /^([^"\(]+) "([^"]+)"(.*)$/)
	{
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M10\n";
		}

		# Prototype: verb "obj_a"[properties]
		# Matches:
		# 13. World Objectives/Actions
		# 19. a) Loading map; b) Started map
		# 21. Server Name

		$ev_verb   = $1;
		$ev_obj_a  = $2;
		$ev_properties = $3;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "World triggered"))
		{
			$ev_type = 13;
			$ev_status = &doEvent_WorldAction(
				$ev_obj_a,
				%ev_properties
			);
		}
		elsif (like($ev_verb, "Loading map"))
		{
			$ev_type = 19;
			$ev_status = &doEvent_ChangeMap(
				"loading",
				$ev_obj_a
			);
		}
		elsif (like($ev_verb, "Started map"))
		{
			$ev_type = 19;
			$ev_status = &doEvent_ChangeMap(
				"started",
				$ev_obj_a
			);
		}
		elsif (like($ev_verb, "Server name is"))
		{
			$ev_type = 21;
			$ev_status = &doEvent_ServerName(
				$ev_obj_a
			);
		}
	}
	elsif ($s_output =~ /^((?:Server cvars|Log file)[^\(]+)(.*)$/) {
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M11\n";
		}

		# Prototype: verb[properties]
		# Matches:
	    # 17. a) Server cvars start; c) Server cvars end
		# 18. a) Log file started; b) Log file closed

		$ev_verb   = $1;
		$ev_properties = $2;

		%ev_properties = &getProperties($ev_properties);

		if (like($ev_verb, "Server cvars start")) {
			$ev_type = 17;
			$ev_status = &doEvent_ServerCvar(
				"start"
			);
		}
		elsif (like($ev_verb, "Server cvars end")) {
			$ev_type = 17;
			$ev_status = &doEvent_ServerCvar(
				"end"
			);
		}
		elsif (like($ev_verb, "Log file started")) {
			$ev_type = 18;
			$ev_status = &doEvent_LogFile(
				"start"
			);
		}
		elsif (like($ev_verb, "Log file closed")) {
			$ev_type = 18;
			$ev_status = &doEvent_LogFile(
				"end"
			);
		}
	}
	elsif ($s_output =~ /^\[ADMIN:?\]\s*(.+)$/) {
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M12\n";
		}

		# Prototype: [ADMIN] obj_a
		# Matches:
	    # Admin Mod messages

		$ev_obj_a  = $1;

		$ev_type = 500;
		$ev_status = &doEvent_Admin(
			"Admin Mod",
			$ev_obj_a
		);
	}
	elsif ($s_output =~ /^\[ADMIN:(.+)\] ADMIN Command: \1 used command (.+)$/) {
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M13\n";
		}

		# Prototype: [ADMIN] obj_a
		# Matches:
	    # Admin Mod messages

		$ev_obj_a  = $1;
		$ev_obj_b  = $2;

		$ev_type = 500;
		$ev_status = &doEvent_Admin(
			"Admin Mod",
			$ev_obj_b,
			$ev_obj_a
		);
	}
	elsif ($s_output =~ /^\(DEATH\)"([^"]+)" ([^"\(]+) "([^"]+)" [^"\(]+ "([^"]+)"/) {
		if ($g_debug > 2) {
			print "## DEBUG : ".$s_output."\n";
			print "## Match M14\n";
		}

		#
		# l4d support for the kills
		# this has a non standart log format
		#

		$ev_killer = $1;
		$ev_verb   = $2; # killed etc.
		$ev_victim = $3;
		$ev_weapon = $4;

		if(like($ev_verb, "killed")) {
			my $killerinfo = &getPlayerInfo($ev_killer);
			my $victiminfo = &getPlayerInfo($ev_victim);

			$ev_type = 48;

			if ($killerinfo && $victiminfo) {
				$ev_status = &doEvent_Frag(
					$killerinfo->{"userid"},
					$victiminfo->{"userid"},
					$ev_weapon
				);
			}

		}
	}


	if ($ev_type) {
		# Update the rating system.
		if($g_rating_system eq "1" || $g_rating_system eq "2") {
			$ratingsys->update();
		}

		if ($g_debug > 2) {
			print <<EOT
type   = "$ev_type"
team   = "$ev_team"
player = "$ev_player"
verb   = "$ev_verb"
obj_a  = "$ev_obj_a"
obj_b  = "$ev_obj_b"
obj_c  = "$ev_obj_c"
properties = "$ev_properties"
EOT
;
			while (my($key, $value) = each(%ev_properties)) {
				print "property: \"$key\" = \"$value\"\n";
			}

			while (my($key, $value) = each(%ev_player)) {
				print "player $key = \"$value\"\n";
			}
		}

		if ($ev_status ne "") {
			&printEvent($ev_type, $ev_status);
		}
		else {
			&printEvent($ev_type, "BAD DATA: $s_output");
		}
	}
	else {
		# Unrecognised event
		&printEvent(999, "UNRECOGNISED: " . $s_output);
	}



	# Clean up
	while ( my($pl, $player) = each(%g_players) ) {
		if ( ($ev_unixtime - $player->{timestamp}) > 600 ) {
			# we delete any player who is inactive for over 10 mins (600 sec)
			# - they probably disconnected silently somehow.

			&printEvent(400, "Auto-disconnecting " . $player->getInfoString() .
				" for idling (" . ($ev_unixtime - $player->get("timestamp")) . " sec)");

			my($server) = split(/\//, $pl);
			$g_servers{$server}->{numplayers}-- if ($player->get("uniqueid") !~ /PENDING/);
			&printNotice("NumPlayers ($server): $g_servers{$server}->{numplayers} (Auto-Disconnect)");

			$player->updateDB();
			delete($g_players{$pl});
		}
	}


	# Delete events over $g_deletedays days old, at every 500th iteration of the main loop

	if ($c % 5000 == 0 && $g_deletedays != 0) {
		if ($g_debug > 0) {
			print "\n-- Cleaning up database: deleting events older than $g_deletedays days ...\n";
		}

		my $deleteType = "";
		$deleteType = " LOW_PRIORITY" if ($db_lowpriority);

		foreach $eventTable (keys(%g_eventTables)) {
			if ($g_debug > 0) {
				print "-> ${db_prefix}_Events_$eventTable ... "
			}

			&doQuery("
				DELETE$deleteType FROM
					${db_prefix}_Events_$eventTable
				WHERE
					eventTime < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL $g_deletedays DAY)
			");

			if ($g_debug > 0) {
				print "OK\n";
			}
		}

		if ($g_debug > 0) {
			print "-- Database cleanup complete.\n\n";
		}
	}

	if ($c % 500000 == 0) {
		if ($g_debug > 0) {
			print "\n-- Optimizing database: Optimizing tables...\n";
		}

		foreach $table (@g_allTables) {
			if ($g_debug > 0) {
				print "-> $table ... "
			}

			&doQuery("
				OPTIMIZE TABLE $table
			");

			if ($g_debug > 0) {
				print "OK\n";
			}
		}

		if ($g_debug > 0) {
			print "-- Database optimization complete.\n\n";
		}
	}
	$c++;
	$c = 1 if ($c > 500000);
}
