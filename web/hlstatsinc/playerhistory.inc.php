<?php
/**
 *
 * Original development:
 * +
 * + HLStats - Real-time player and clan rankings and statistics for Half-Life
 * + http://sourceforge.net/projects/hlstats/
 * +
 * + Copyright (C) 2001  Simon Garner
 * +
 *
 * Additional development:
 * +
 * + UA HLStats Team
 * + http://www.unitedadmins.com
 * + 2004 - 2007
 * +
 *
 *
 * Current development:
 * +
 * + Johannes 'Banana' Keßler
 * + http://hlstats.sourceforge.net
 * + 2007 - 2009
 * +
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

$player = '';
if(!empty($_GET["player"])) {
	if(validateInput($_GET["player"],'digit') === true) {
		$player = $_GET["player"];
	}
	else {
		error("No player ID specified.");
	}
}

$query = mysql_query("
	SELECT
		".DB_PREFIX."_Players.lastName,
		".DB_PREFIX."_Players.game
	FROM
		".DB_PREFIX."_Players
	WHERE
		playerId=".mysql_escape_string($player)."
");
if (mysql_num_rows($query) != 1)
	error("No such player '$player'.");

$playerdata = mysql_fetch_assoc($query);
mysql_free_result($query);

$pl_name = $playerdata["lastName"];
if (strlen($pl_name) > 10) {
	$pl_shortname = substr($pl_name, 0, 8) . "...";
}
else {
	$pl_shortname = $pl_name;
}
$pl_name = ereg_replace(" ", "&nbsp;", htmlspecialchars($pl_name));
$pl_shortname = ereg_replace(" ", "&nbsp;", htmlspecialchars($pl_shortname));


$game = $playerdata["game"];
$query = mysql_query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='".mysql_escape_string($game)."'");
if (mysql_num_rows($query) != 1)
	$gamename = ucfirst($game);
else {
	$result = mysql_fetch_assoc($query);
	$gamename = $result['name'];
}


pageHeader(
	array($gamename, "Event History", $pl_name),
	array(
		$gamename=>$g_options["scripturl"] . "?game=$game",
		"Player Rankings"=>$g_options["scripturl"] . "?mode=players&amp;game=$game",
		"Player Details"=>$g_options["scripturl"] . "?mode=playerinfo&amp;player=$player",
		"Event History"=>""
	),
	$pl_name
);

flush();


$table = new Table(
	array(
		new TableColumn(
			"eventTime",
			"Date",
			"width=20"
		),
		new TableColumn(
			"eventType",
			"Type",
			"width=10&align=center"
		),
		new TableColumn(
			"eventDesc",
			"Description",
			"width=40&sort=no&append=.&embedlink=yes"
		),
		new TableColumn(
			"serverName",
			"Server",
			"width=20"
		),
		new TableColumn(
			"map",
			"Map",
			"width=10"
		)
	),
	"eventTime",
	"eventTime",
	"eventType",
	false,
	50,
	"page",
	"sort",
	"sortorder"
);

$surl = $g_options["scripturl"];


// This would be better done with a UNION query, I think, but MySQL doesn't
// support them yet. (NOTE you need MySQL 3.23 for temporary table support.)

mysql_query("DROP TABLE IF EXISTS ".DB_PREFIX."_EventHistory");
mysql_query("
	CREATE TEMPORARY TABLE ".DB_PREFIX."_EventHistory
	(
		eventType VARCHAR(32) NOT NULL,
		eventTime DATETIME NOT NULL,
		eventDesc VARCHAR(255) NOT NULL,
		serverName VARCHAR(32) NOT NULL,
		map VARCHAR(32) NOT NULL
	)
");

function insertEvents ($table, $select) {
	$select = str_replace("<table>", "".DB_PREFIX."_Events_$table", $select);
	mysql_query("
		INSERT INTO
			".DB_PREFIX."_EventHistory
			(
				eventType,
				eventTime,
				eventDesc,
				serverName,
				map
			)
		$select
	");
}

insertEvents("TeamBonuses", "
	SELECT
		'Team Bonus',
		<table>.eventTime,
		CONCAT('My team received a points bonus of ', bonus, ' for triggering \"', ".DB_PREFIX."_Actions.description, '\"'),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Actions ON
		<table>.actionId = ".DB_PREFIX."_Actions.id
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

if (MODE == "LAN") {
	$uqIdStr = "IP Address:";
}
else {
	$uqIdStr = "Unique ID:";
}

insertEvents("Connects", "
	SELECT
		'Connect',
		<table>.eventTime,
		CONCAT('I connected to the server'),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

insertEvents("Disconnects", "
	SELECT
		'Disconnect',
		<table>.eventTime,
		'I left the game',
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

insertEvents("Entries", "
	SELECT
		'Entry',
		<table>.eventTime,
		'I entered the game',
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

insertEvents("Frags", "
	SELECT
		'Kill',
		<table>.eventTime,
		CONCAT('I killed %A%$surl?mode=playerinfo&player=', victimId, '%', ".DB_PREFIX."_Players.lastName, '%/A%', ' with ', weapon),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = <table>.victimId
	WHERE
		<table>.killerId=".mysql_escape_string($player)."");

insertEvents("Frags", "
	SELECT
		'Death',
		<table>.eventTime,
		CONCAT('%A%$surl?mode=playerinfo&player=', killerId, '%', ".DB_PREFIX."_Players.lastName, '%/A%', ' killed me with ', weapon),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	LEFT JOIN ".DB_PREFIX."_Players On
		".DB_PREFIX."_Players.playerId = <table>.killerId
	WHERE
		<table>.victimId=".mysql_escape_string($player)."");

insertEvents("Teamkills", "
	SELECT
		'Team Kill',
		<table>.eventTime,
		CONCAT('I killed teammate %A%$surl?mode=playerinfo&player=', victimId, '%', ".DB_PREFIX."_Players.lastName, '%/A%', ' with ', weapon),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	LEFT JOIN ".DB_PREFIX."_Players On
		".DB_PREFIX."_Players.playerId = <table>.victimId
	WHERE
		<table>.killerId=".mysql_escape_string($player)."");

insertEvents("Teamkills", "
	SELECT
		'Friendly Fire',
		<table>.eventTime,
		CONCAT('My teammate %A%$surl?mode=playerinfo&player=', killerId, '%', ".DB_PREFIX."_Players.lastName, '%/A%', ' killed me with ', weapon),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	LEFT JOIN ".DB_PREFIX."_Players On
		".DB_PREFIX."_Players.playerId = <table>.killerId
	WHERE
		<table>.victimId=".mysql_escape_string($player)."");

insertEvents("ChangeRole", "
	SELECT
		'Role',
		<table>.eventTime,
		CONCAT('I changed role to ', role),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

insertEvents("ChangeName", "
	SELECT
		'Name',
		<table>.eventTime,
		CONCAT('I changed my name from \"', oldName, '\" to \"', newName, '\"'),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

insertEvents("PlayerActions", "
	SELECT
		'Action',
		<table>.eventTime,
		CONCAT('I received a points bonus of ', bonus, ' for triggering \"', ".DB_PREFIX."_Actions.description, '\"'),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	LEFT JOIN ".DB_PREFIX."_Actions ON
		".DB_PREFIX."_Actions.id = <table>.actionId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

insertEvents("PlayerPlayerActions", "
	SELECT
		'Action',
		<table>.eventTime,
		CONCAT('I received a points bonus of ', bonus, ' for triggering \"', ".DB_PREFIX."_Actions.description, '\" against %A%$surl?mode=playerinfo&player=', victimId, '%', ".DB_PREFIX."_Players.lastName, '%/A%'),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	LEFT JOIN ".DB_PREFIX."_Actions ON
		".DB_PREFIX."_Actions.id = <table>.actionId
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = <table>.victimId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

insertEvents("PlayerPlayerActions", "
	SELECT
		'Action',
		<table>.eventTime,
		CONCAT('%A%$surl?mode=playerinfo&player=', <table>.playerId, '%', ".DB_PREFIX."_Players.lastName, '%/A% triggered \"', ".DB_PREFIX."_Actions.description, '\" against me'),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	LEFT JOIN ".DB_PREFIX."_Actions ON
		".DB_PREFIX."_Actions.id = <table>.actionId
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = <table>.playerId
	WHERE
		<table>.victimId=".mysql_escape_string($player)."");

insertEvents("Suicides", "
	SELECT
		'Suicide',
		<table>.eventTime,
		CONCAT('I committed suicide with \"', weapon, '\"'),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

insertEvents("ChangeTeam", "
	SELECT
		'Team',
		<table>.eventTime,
		IF(".DB_PREFIX."_Teams.name IS NULL,
			CONCAT('I joined team \"', team, '\"'),
			CONCAT('I joined team \"', team, '\" (', ".DB_PREFIX."_Teams.name, ')')
		),
		".DB_PREFIX."_Servers.name,
		<table>.map
	FROM
		<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
		".DB_PREFIX."_Servers.serverId = <table>.serverId
	LEFT JOIN ".DB_PREFIX."_Teams ON
		".DB_PREFIX."_Teams.code = <table>.team
	WHERE
		<table>.playerId=".mysql_escape_string($player)."");

$query = mysql_query("
	SELECT
		eventTime,
		eventType,
		eventDesc,
		serverName,
		map
	FROM
		".DB_PREFIX."_EventHistory
	ORDER BY
		".$table->sort." ".$table->sortorder.",
		".$table->sort2." ".$table->sortorder."
	LIMIT
		".$table->startitem.",".$table->numperpage."");

$queryCount = mysql_query("SELECT COUNT(*) AS ec FROM ".DB_PREFIX."_EventHistory");
$result = mysql_fetch_assoc($queryCount);
$numitems = $result['ec'];
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%">
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Player Event History</b> (Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?><p>
		<?php
			$table->draw($query, $numitems, 100);
		?>
		</td>
	</tr>
</table>