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
# + 2007 - 2008
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
my $opt_configfile_name = "hlstats.conf.ini";

##
##
################################################################################
## No need to edit below this line
##

use strict;
use warnings; #DEBUG
use DBI;
use File::Basename;
use Config::Tiny;
use Getopt::Long;
use Time::Local;


my $opt_libdir = dirname(__FILE__);
my $opt_configfile = "$opt_libdir/$opt_configfile_name";

require "$opt_libdir/HLstats.plib";

$|=1;
Getopt::Long::Configure ("bundling");

## load config with config-tiny module
my $Config = Config::Tiny->read("$opt_libdir/hlstats.conf.ini");
if($Config::Tiny::errstr ne '') {
	print "Config file not found !\n";
	print $Config::Tiny::errstr;
	print "\n";
	exit(0)
}

my $opt_help = 0;
my $opt_version = 0;
my $opt_numdays = 1;

my $db_name = $Config->{Database}->{DBName};
my $db_host = $Config->{Database}->{DBHost};
my $db_user = $Config->{Database}->{DBUsername};
my $db_pass = $Config->{Database}->{DBPassword};
my $db_prefix = $Config->{Database}->{DBPrefix};

my $conf_timeFrame = $Config->{playerActivity}->{timeFrame};

# Usage message

my $usage = <<EOT
Usage: player-activity.pl [OPTION]...
Update player activity from Half-Life server statistics.

  -h, --help                      display this help and exit
  -v, --version                   output version information and exit
      --period                    number of days in period for player-activity
      --db-host=HOST              database ip:port
      --db-name=DATABASE          database name
      --db-password=PASSWORD      database password (WARNING: specifying the
                                    password on the command line is insecure.
                                    Use the configuration file instead.)
      --db-username=USERNAME      database username

Long options can be abbreviated, where such abbreviation is not ambiguous.

Most options can be specified in the configuration file:
  $opt_configfile
Note: Options set on the command line take precedence over options set in the
configuration file.

HLStats: http://www.hlstats-community.org
EOT
;


GetOptions(
	"help|h"			=> \$opt_help,
	"version|v"			=> \$opt_version,
	"period=i"			=> \$conf_timeFrame,
	"db-host=s"			=> \$db_host,
	"db-name=s"			=> \$db_name,
	"db-password=s"		=> \$db_pass,
	"db-username=s"		=> \$db_user
) or die($usage);

if ($opt_help) {
	print $usage;
	exit(0);
}

if ($opt_version) {
	print "player-activity.pl (HLStats): $main::g_version\n"
		. "Real-time player and clan rankings and statistics for Half-Life\n\n"
		. "http://www.hlstats-community.org\n"
		. "This is free software; see the source for copying conditions.  There is NO\n"
		. "warranty; not even for MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n";
	exit(0);
}

# startup
print "++ HLStats $main::g_version starting...\n\n";

# Connect to the database
print "-- Connecting to MySQL database '$db_name' on '$db_host' as user '$db_user' ... ";

$main::db_conn = DBI->connect(
	"DBI:mysql:$db_name:$db_host",
	$db_user, $db_pass, { RaiseError => 1, "mysql_enable_utf8" => 1, 'mysql_auto_reconnect' => 1,
				'ShowErrorStatement' => 1 }
) or die ("\nCan't connect to MySQL database '$db_name' on '$db_host'\n" .
	"Server error: $DBI::errstr\n");

&doQuery("SET character set utf8");
&doQuery("SET NAMES utf8");

print "\n\nConnected OK\n";

## main process
my $frame = time() - ($conf_timeFrame*86400); # time in seconds

&doQuery("UPDATE ${db_prefix}_Players
									SET `active` = '0'
									WHERE `skillchangeDate` < '".$frame."'
										AND `skillchangeDate` IS NOT NULL
										AND `active` = '1'");

print "\n++ Player activity updated successfully.\n";
exit(0);
