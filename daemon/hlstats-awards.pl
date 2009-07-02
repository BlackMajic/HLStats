#!/usr/bin/perl
#
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
#


##
## Settings
##

# $opt_configfile_name - Filename of configuration file.
$opt_configfile_name = "hlstats.conf";

# $opt_libdir - Directory to look in for local required files
#               (our *.plib, *.pm files).
# not needed anymore
# replaced by dirname funtion below
#$opt_libdir = "./";


##
##
################################################################################
## No need to edit below this line
##


use Getopt::Long;
use DBI;

use File::Basename;

$opt_libdir = dirname(__FILE__);
$opt_configfile = "$opt_libdir/$opt_configfile_name";

require "$opt_libdir/ConfigReaderSimple.pm";
do "$opt_libdir/HLstats.plib";

$|=1;
Getopt::Long::Configure ("bundling");



##
## MAIN
##

# Options

$opt_help = 0;
$opt_version = 0;
$opt_numdays = 1;

$db_host = "localhost";
$db_user = "";
$db_pass = "";
$db_name = "hlstats";
$db_prefix = "hlstats";

# Usage message

$usage = <<EOT
Usage: hlstats-awards.pl [OPTION]...
Generate awards from Half-Life server statistics.

  -h, --help                      display this help and exit
  -v, --version                   output version information and exit
      --numdays                   number of days in period for awards
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

# Read Config File

if (-r $opt_configfile)
{
	$conf = ConfigReaderSimple->new($opt_configfile);
	$conf->parse();

	%directives = (
		"DBHost",			"db_host",
		"DBUsername",		"db_user",
		"DBPassword",		"db_pass",
		"DBName",			"db_name",
		"DBPrefix",			"db_prefix"
	);

	&doConf($conf, %directives);
}
else
{
	print "-- Warning: unable to open configuration file $opt_configfile\n";
}

# Read Command Line Arguments

GetOptions(
	"help|h"			=> \$opt_help,
	"version|v"			=> \$opt_version,
	"numdays=i"			=> \$opt_numdays,
	"db-host=s"			=> \$db_host,
	"db-name=s"			=> \$db_name,
	"db-password=s"		=> \$db_pass,
	"db-username=s"		=> \$db_user
) or die($usage);

if ($opt_help)
{
	print $usage;
	exit(0);
}

if ($opt_version)
{
	print "hlstats-awards.pl (HLStats) $g_version\n"
		. "Real-time player and clan rankings and statistics for Half-Life\n\n"
		. "http://hlstats-community.org\n"
		. "This is free software; see the source for copying conditions.  There is NO\n"
		. "warranty; not even for MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n";
	exit(0);
}


# Startup

print "++ HLStats Awards $g_version starting...\n\n";

# Connect to the database

print "-- Connecting to MySQL database '$db_name' on '$db_host' as user '$db_user' ... ";

$db_conn = DBI->connect(
	"DBI:mysql:$db_name:$db_host",
	$db_user, $db_pass
) or die ("Can't connect to MySQL database '$db_name' on '$db_host'\n" .
	"$DBI::errstr\n");

print "connected OK\n";


# Main data routine

$resultAwards = &doQuery("
	SELECT
		${db_prefix}_Awards.awardId,
		${db_prefix}_Awards.game,
		${db_prefix}_Awards.awardType,
		${db_prefix}_Awards.code
	FROM
		${db_prefix}_Awards
	LEFT JOIN ${db_prefix}_Games ON
		${db_prefix}_Games.code = ${db_prefix}_Awards.game
	WHERE
		${db_prefix}_Games.hidden='0'
	ORDER BY
		${db_prefix}_Awards.game,
		${db_prefix}_Awards.awardType
");

$result = &doQuery("
	SELECT
		value,
		DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)
	FROM
		${db_prefix}_Options
	WHERE
		keyname='awards_d_date'
");

if ($result->rows > 0)
{
	($awards_d_date, $awards_d_date_new) = $result->fetchrow_array;

	&doQuery("
		UPDATE
			${db_prefix}_Options
		SET
			value='$awards_d_date_new'
		WHERE
			keyname='awards_d_date'
	");

	print "\n++ Generating awards for $awards_d_date_new (previous: $awards_d_date)...\n\n";
}
else
{
	&doQuery("
		INSERT INTO
			${db_prefix}_Options
			(
				keyname,
				value
			)
		VALUES
		(
			'awards_d_date',
			DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)
		)
	");
}

&doQuery("
	REPLACE INTO
		${db_prefix}_Options
		(
			keyname,
			value
		)
	VALUES
	(
		'awards_numdays',
		$opt_numdays
	)
");


while( ($awardId, $game, $awardType, $code) = $resultAwards->fetchrow_array )
{
	print "$game ($awardType) $code";

	if ($awardType eq "O")
	{
		$table = "${db_prefix}_Events_PlayerActions";
		$join  = "LEFT JOIN ${db_prefix}_Actions ON ${db_prefix}_Actions.id = $table.actionId";
		$matchfield = "${db_prefix}_Actions.code";
		$playerfield = "$table.playerId";
	}
	elsif ($awardType eq "W")
	{
		$table = "${db_prefix}_Events_Frags";
		$join  = "";
		$matchfield = "$table.weapon";
		$playerfield = "$table.killerId";
	}

	$result = &doQuery("
		SELECT
			$playerfield,
			COUNT($matchfield) AS awardcount
		FROM
			$table
		LEFT JOIN ${db_prefix}_Players ON
			${db_prefix}_Players.playerId = $playerfield
		$join
		WHERE
			$table.eventTime < CURRENT_DATE()
			AND $table.eventTime > DATE_SUB(CURRENT_DATE(), INTERVAL $opt_numdays DAY)
			AND ${db_prefix}_Players.game='$game'
			AND ${db_prefix}_Players.hideranking='0'
			AND $matchfield='$code'
		GROUP BY
			$playerfield
		ORDER BY
			awardcount DESC,
			${db_prefix}_Players.skill DESC
		LIMIT 1
	");

	($d_winner_id, $d_winner_count) = $result->fetchrow_array;

	if (!$d_winner_id || $d_winner_count < 1)
	{
		$d_winner_id = "NULL";
		$d_winner_count = "NULL";
	}

	print "  - $d_winner_id ($d_winner_count)\n";

	&doQuery("
		UPDATE
			${db_prefix}_Awards
		SET
			d_winner_id=$d_winner_id,
			d_winner_count=$d_winner_count
		WHERE
			awardId=$awardId
	");
}

print "\n++ Awards generated successfully.\n";
exit(0);
