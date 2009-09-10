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



// Player Chat History
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
	array($gamename, l("Event Chat History"), $pl_name),
	array(
		$gamename=>$g_options["scripturl"] . "?game=$game",
		l("Player Rankings")=>$g_options["scripturl"] . "?mode=players&amp;game=$game",
		l("Player Details")=>$g_options["scripturl"] . "?mode=playerinfo&amp;player=$player",
		l("Event Chat History")=>""
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

mysql_query("DROP TABLE IF EXISTS ".DB_PREFIX."_EventHistoryChat");
mysql_query("
	CREATE TEMPORARY TABLE ".DB_PREFIX."_EventHistoryChat
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
			".DB_PREFIX."_EventHistoryChat
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


if (MODE == "LAN") {
	$uqIdStr = "IP Address:";
}
else {
	$uqIdStr = "Unique ID:";
}

insertEvents("Chat", "
	SELECT
	 	'Say',
	 	<table>.eventTime,
	 	CONCAT('I said \"', message, '\"'),
	 	".DB_PREFIX."_Servers.name,
	 	<table>.map
	FROM
	 	<table>
	LEFT JOIN ".DB_PREFIX."_Servers ON
	 	".DB_PREFIX."_Servers.serverId = <table>.serverId
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
		".DB_PREFIX."_EventHistoryChat
	ORDER BY
		".$table->sort." ".$table->sortorder.",
		".$table->sort2." ".$table->sortorder."
	LIMIT
		".$table->startitem.",".$table->numperpage."");

$resultCount = mysql_query("SELECT COUNT(*) as hc FROM ".DB_PREFIX."_EventHistoryChat");
$result = mysql_fetch_assoc($resultCount);
$numitems = $result['hc'];
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%"><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Player Event History'); ?></b> (<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?><p>

	<?php
		$table->draw($query, $numitems, 100);
	?></td>
</tr>
</table>
