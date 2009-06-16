package KKrcon;
#
# KKrcon Perl Module - execute commands on a remote Half-Life server using Rcon.
# http://kkrcon.sourceforge.net
#
# Synopsis:
#
#   use KKrcon;
#   $rcon = new KKrcon(Password=>PASSWORD, [Host=>HOST], [Port=>PORT], [Type=>"new"|"old"]);
#   $result  = $rcon->execute(COMMAND);
#   %players = $rcon->getPlayers();
#   $player  = $rcon->getPlayer(USERID);
# 
# Copyright (C) 2000, 2001  Rod May
# Enhanced in 2005 by Tobi (Tobi@gameme.de)
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

use strict;
use sigtrap;
use Socket;
use Sys::Hostname;

# Release version number
my $VERSION = "2.11s";

##
## Constants for source engine
##
my $SERVERDATA_EXECCOMMAND = 0x2;
my $SERVERDATA_AUTH = 0x03;
my $SERVERDATA_RESPONSE_VALUE = 0x0;
my $SERVERDATA_AUTH_RESPONSE = 0x2;

##
## Main
##

#
# Constructor
#
sub new
{
	my $class_name = shift;
	my %params = @_;
	my $self = {};
	bless($self, $class_name);
	
	my %server_types = (new=>1, old=>2, source=>3);
	
	# Check parameters
	$params{"Host"} = "127.0.0.1"  unless($params{"Host"});
	$params{"Port"} = 27015        unless($params{"Port"});
	$params{"Type"} = "new"        unless($params{"Type"});
	
	# Initialise properties
	$self->{"rcon_password"} = $params{"Password"} or die("KKrcon: a Password is required\n");
	$self->{"server_host"}   = $params{"Host"};
	$self->{"server_port"}   = int($params{"Port"}) or die("KKrcon: invalid Port \"" . $params{"Port"} . "\"\n");
	$self->{"server_type"}   = ($server_types{$params{"Type"}} || 1);
	
	$self->{"socket"};
	$self->{"error"} = "";
	
	# Set up socket parameters
	$self->{"_proto"}  = ($self->{"server_type"} != 3) ? getprotobyname("udp") : getprotobyname("tcp");
	$self->{"_ipaddr"} = gethostbyname($self->{"server_host"}) or die("KKrcon: could not resolve Host \"" . $self->{"server_host"} . "\"\n");
	
	$self->s_init_socket() if($self->{"server_type"} == 3);
	return $self;
}

#
# Execute an Rcon command and return the response
#
sub execute
{
	my ($self, $command) = @_;
	my $msg;
	my $ans;
	
	if ($self->{"server_type"} == 1)
	{
		# version x.1.0.6+ HL server
		$msg = "\xFF\xFF\xFF\xFFchallenge rcon\n\0";
		$ans = $self->_sendrecv($msg);
		
		if ($ans =~ /challenge +rcon +(\d+)/)
		{
			$msg = "\xFF\xFF\xFF\xFFrcon $1 \"" . $self->{"rcon_password"} . "\" $command\0";
			$ans = $self->_sendrecv($msg);
		}
		elsif (!$self->error())
		{
			$ans = "";
			$self->{"error"} = "No challenge response";
		}
	}
	elsif ($self->{"server_type"} == 2)
	{
		# QW/Q2/Q3 or old HL server
		$msg = "\xFF\xFF\xFF\xFFrcon " . $self->{"rcon_password"} . " $command\n\0";
		$ans = $self->_sendrecv($msg);
	}
	elsif ($self->{"server_type"} == 3)
	{
		# Source Engine
		$ans = $self->s_sendrecv($command);
	}
	
	if ($ans =~ /bad rcon_password/i)
	{
		$self->{"error"} = "Bad Password";
	}
	return $ans;
}

sub _sendrecv
{
	my ($self, $msg) = @_;
	my $host   = $self->{"server_host"};
	my $port   = $self->{"server_port"};
	my $ipaddr = $self->{"_ipaddr"};
	my $proto  = $self->{"_proto"};
	
	# Open socket
	socket($self->{"socket"}, PF_INET, SOCK_DGRAM, $proto) or die("KKrcon(141): socket: $!\n");
	my $hispaddr = sockaddr_in($port, $ipaddr);
	
	die("KKrcon: send $ipaddr:$port : $!") unless(defined(send($self->{"socket"}, $msg, 0, $hispaddr)));

	my $rin = "";
	vec($rin, fileno($self->{"socket"}), 1) = 1;
	my $ans = "TIMEOUT";
	if (select($rin, undef, undef, 0.5))
	{
		$ans = "";
		$hispaddr = recv($self->{"socket"}, $ans, 8192, 0);
		$ans =~ s/\x00+$//;					# trailing crap
		$ans =~ s/^\xFF\xFF\xFF\xFFl//;		# HL response
		$ans =~ s/^\xFF\xFF\xFF\xFFn//;		# QW response
		$ans =~ s/^\xFF\xFF\xFF\xFF//;		# Q2/Q3 response
		$ans =~ s/^\xFE\xFF\xFF\xFF.....//;	# old HL bug/feature
	}
	# Close socket
	close($self->{"socket"});
	
	if ($ans eq "TIMEOUT")
	{
		$ans = "";
		$self->{"error"} = "Rcon timeout";
	}
	return $ans;
}

#
# Source engine rcon functions
#
sub s_init_socket
{
	my ($self) = @_;
	my $host = $self->{"server_host"};
	my $port = $self->{"server_port"};
	my $ipaddr = $self->{"_ipaddr"};
	my $proto  = $self->{"_proto"};

	if(!socket($self->{"socket"}, PF_INET, SOCK_STREAM, $proto))
	{
		$self->{error} = "Unable to open socket to $ipaddr:$port : $!";
		return;
	}
	
	my $paddr = sockaddr_in($port, $ipaddr);
	if(!connect($self->{"socket"}, $paddr))
	{
		$self->{error} = "Unable to connect to host : $!";
		return;
	}
	return 1;
}

sub s_close_socket
{
	my ($self) = @_;
	# Close socket (if tcp)
	close($self->{"socket"}) if ($self->{"server_type"} == 3);
}


sub s_sendrecv
{
	my ($self, $msg) = @_;
	my $auth = 0;
	my $response = "";
	my ($id, $command);
	$self->s_init_socket() if (!$self->{"socket"});
	
	$self->{error} = "";
	# Sending password
	if($self->send_rcon(20,$SERVERDATA_AUTH, $self->{"rcon_password"},""))
	{
		$self->{error} = "Couldn't send password";
		return;
	}
	
	while( $auth == 0)
	{
		my ($id, $command, $response) = $self->recieve_rcon();
		if($command == $SERVERDATA_AUTH_RESPONSE)
		{
			if($id == 20)
			{
				$auth = 1;
			}
			elsif( $id == -1)
			{
				$self->{error} = "Rcon password refused";
				return;
			}
			else
			{
				$self->{error} = "Bad password response id=$id";
				return;
			}
		}
		elsif($command == -1)
		{
			if ($self->{"server_type"} == 3)
			{
				$self->s_close_socket();
				$self->s_init_socket();
			}
			$self->{error} = "Server timed out!";
			return;
		}
	}
	
	if($self->send_rcon(20,$SERVERDATA_EXECCOMMAND, $msg))
	{
		return;
	}
	$command = 0;
	while($command != -1)
	{
		# Read until timeout, to get all response packages
		my $tmp;
		($id, $command, $tmp) = $self->recieve_rcon();
		$response .= $tmp if($tmp != -1);
	}
	return $response;
}

#
# Send a package
#
sub send_rcon
{
	my ($self, $id, $command, $string1, $string2) = @_;
	my $tmp = pack("VVZ*Z*",$id,$command,$string1,$string2);
	my $size = length($tmp);
	if($size > 4096)
	{
		$self->{error} = "Command to long to send!";
		return 1;
	}
	my $tmp = pack("V", $size) .$tmp;
	unless(defined(send($self->{"socket"},$tmp,0)))
	{
		die("KKrcon: send $!");
	}
	return 0;
}

#
#  Recieve a package
#
sub recieve_rcon
{
	my $self = shift;
	my ($size, $id, $command, $msg);
	my $rin = "";
	my $tmp = "";
	
	vec($rin, fileno($self->{"socket"}), 1) = 1;
	if(select($rin, undef, undef, 0.5))
	{
		while(length($size) < 4)
		{
			$tmp = "";
			recv($self->{"socket"}, $tmp, (4-length($size)), 0);
			$size .= $tmp;
		}
		$size = unpack("V", $size);
		if($size < 10 || $size > 8192)
		{
			close($self->{"socket"});
			$self->{error} = "illegal size $size ";
			return (-1, -1, -1);
		}
		
		while(length($id)<4)
		{
			$tmp = "";
			recv($self->{"socket"}, $tmp, (4-length($id)), 0);
			$id .= $tmp;
		}
		$id = unpack("V", $id);
		$size = $size - 4;
		while(length($command)<4)
		{
			$tmp ="";
			recv($self->{"socket"}, $tmp, (4-length($command)),0);
			$command.=$tmp;
		}
		$command = unpack("V", $command);
		$size = $size - 4;
		my $msg = "";
		while($size >= 1)
		{
			$tmp = "";
			recv($self->{"socket"}, $tmp, $size, 0);
			$size -= length($tmp);
			$msg .= $tmp;
		}
		my ($string1,$string2) = unpack("Z*Z*",$msg);
		$msg = $string1.$string2;
		return ($id, $command, $msg);
	}
	else
	{
		return (-1, -1, -1);
	}
}

#
# Get error message
#
sub error
{
	my ($self) = @_;
	return $self->{"error"};
}


#
# Parse "status" command output into player information
#
sub getPlayers
{
	my ($self) = @_;
	my $status = $self->execute("status");
	my @lines = split(/[\r\n]+/, $status);
	
	my %players;
	
	foreach my $line (@lines)
	{
		if ($line =~ /^\#\s*
			(\d+)\s+		# userid
			"(.+)"\s+		# name
			(.+)\s+		    # uniqueid
			([\d:]+)\s+		# time
			(\d+)\s+		# ping
			(\d+)\s+		# loss
			(.+)\s+			# state
			([^:]+):    	# addr
			(\S+)           # port
		   $/x)
		{
			my $userid   = $1;
			my $name     = $2;
			my $uniqueid = $3;
			my $time     = $4;
			my $ping     = $5;
			my $loss     = $6;
			my $state    = $7;
			my $address  = $8;
			my $port     = $9;
			
			$players{$uniqueid} = {
				"Name"       => $name,
				"UserID"     => $userid,
				"UniqueID"   => $uniqueid,
				"Time"       => $time,
				"Ping"       => $ping,
				"Loss"       => $loss,
				"State"      => $state,
				"Address"    => $address,
				"ClientPort" => $port
			};
		}
	}
	return %players;
}


#
# Get information about a player by userID
#

sub getPlayer
{
	my ($self, $uniqueid) = @_;
	my %players = $self->getPlayers();
	
	if (defined($players{$uniqueid}))
	{
		return $players{$uniqueid};
	}
	else
	{
		$self->{"error"} = "No such player # $uniqueid";
		return 0;
	}
}


1;
# end
