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
 * + 2007 - 2010
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
$pl_name = '';

if(!empty($_GET["player"])) {
	if(validateInput($_GET["player"],'digit') === true) {
		$player = $_GET["player"];
	}
	else {
		error("No player ID specified.");
	}
}

// load the player
require('class/player.class.php');
$playerObj = new Player($player,false);
if($playerObj === false) {
	die('No such player');
}

if (isset($_GET["page"])) {
	$check = validateInput($_GET['page'],'digit');
	if($check === true) {
		$playerObj->setOption("page",$_GET['page']);
	}
}
if (isset($_GET["sort"])) {
	$check = validateInput($_GET['sort'],'nospace');
	if($check === true) {
		$playerObj->setOption("sort",$_GET['sort']);
	}
}
else {
	$playerObj->setOption("sort",'eventTime');
}
$newSort = "ASC";
if (isset($_GET["sortorder"])) {
	$check = validateInput($_GET['sortorder'],'nospace');
	if($check === true) {
		$playerObj->setOption("sortorder",$_GET['sortorder']);
	}

	if($_GET["sortorder"] == "ASC") {
		$newSort = "DESC";
	}
}
else {
	$playerObj->setOption("sortorder",'DESC');
}



$gamename = getGameName($playerObj->getParam("game"));
$pl_name = makeSavePlayerName($playerObj->getParam('name'));
pageHeader(
	array($gamename, l("Event History"), $pl_name),
	array(
		$gamename => "index.php?game=".$playerObj->getParam("game"),
		l("Player Rankings") => "index.php?mode=players&amp;game=".$playerObj->getParam("game"),
		l("Player Details") => "index.php?mode=playerinfo&amp;player=$player",
		l("Event History")=>""
	),
	$pl_name
);





/*
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

//$surl = 'index.php';


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
	) DEFAULT CHARSET=utf8
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
*/
/*
insertEvents("TeamBonuses", "
	SELECT
		'".l('Team Bonus')."',
		<table>.eventTime,
		CONCAT('".l('My team received a points bonus of')," ', bonus, ' ".l('for triggering')." \"', ".DB_PREFIX."_Actions.description, '\"'),
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
	$uqIdStr = l("IP Address");
}
else {
	$uqIdStr = "Unique ID";
}

insertEvents("Connects", "
	SELECT
		'".l('Connect'),"',
		<table>.eventTime,
		CONCAT('".l('I connected to the server')."'),
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
		'".l('Disconnect')."',
		<table>.eventTime,
		'".l('I left the game')."',
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
		'".l('I entered the game')."',
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
		'".l('Kill')."',
		<table>.eventTime,
		CONCAT('".l('I killed')." %A%$surl?mode=playerinfo&player=', victimId, '%', ".DB_PREFIX."_Players.lastName, '%/A%', ' ".l('with')." ', weapon),
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
		'".l('Death')."',
		<table>.eventTime,
		CONCAT('%A%$surl?mode=playerinfo&player=', killerId, '%', ".DB_PREFIX."_Players.lastName, '%/A%', ' ".l('killed me with')." ', weapon),
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
		'".l('Team Kill')."',
		<table>.eventTime,
		CONCAT('".l('I killed teammate')." %A%$surl?mode=playerinfo&player=', victimId, '%', ".DB_PREFIX."_Players.lastName, '%/A%', ' ".l('with')." ', weapon),
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
		'".l('Friendly Fire')."',
		<table>.eventTime,
		CONCAT('".l('My teammate')." %A%$surl?mode=playerinfo&player=', killerId, '%', ".DB_PREFIX."_Players.lastName, '%/A%', ' ".l('killed me with')." ', weapon),
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
		'".l('Role')."',
		<table>.eventTime,
		CONCAT('".l("I changed role to")." ', role),
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
		'".l('Name')."',
		<table>.eventTime,
		CONCAT('".l('I changed my name from')." \"', oldName, '\" ".l('to')." \"', newName, '\"'),
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
		'".l('Action')."',
		<table>.eventTime,
		CONCAT('".l('I received a points bonus of')." ', bonus, ' ".l('for triggering')." \"', ".DB_PREFIX."_Actions.description, '\"'),
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
		'".l('Action')."',
		<table>.eventTime,
		CONCAT('".l('I received a points bonus of')." ', bonus, ' ".l('for triggering')." \"', ".DB_PREFIX."_Actions.description, '\" ".l('against')." %A%$surl?mode=playerinfo&player=', victimId, '%', ".DB_PREFIX."_Players.lastName, '%/A%'),
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
		'"-l('Action')."',
		<table>.eventTime,
		CONCAT('%A%$surl?mode=playerinfo&player=', <table>.playerId, '%', ".DB_PREFIX."_Players.lastName, '%/A% ".l('triggered')." \"', ".DB_PREFIX."_Actions.description, '\" ".l('against me')."'),
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
		'".l('Suicide')."',
		<table>.eventTime,
		CONCAT('".l('I committed suicide with')." \"', weapon, '\"'),
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
		'".l('Team')."',
		<table>.eventTime,
		IF(".DB_PREFIX."_Teams.name IS NULL,
			CONCAT('".l('I joined team')." \"', team, '\"'),
			CONCAT('".l('I joined team')." \"', team, '\" (', ".DB_PREFIX."_Teams.name, ')')
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
*/
?>

<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/player.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="player.gif"><?php echo('Back to Player page'); ?></a>
			</li>
			<li>
				<a href="index.php?mode=playerhistory&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/history.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="history.gif"><?php echo('Event History'); ?></a>
			</li>
			<li>
				<a href="index.php?mode=playerchathistory&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/history.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="history.gif"><?php echo l('Chat History'); ?></a>
			</li>
			<li>
				<a href="index.php?mode=search&st=player&q=<?php echo $pl_urlname; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/search.gif" width="16" height="16" hspace="3" border="0" align="middle" alt="search.gif"><?php echo l('Find other players with the same name'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1>
		<?php echo l('Player Event History'); ?>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
<?php
	$history = $playerObj->getEventHistory();
	$rcol = "row-dark";
	if(!empty($history)) {
?>
	<table cellpadding="0" cellspacing="0" border="1" width="100%">
		<tr>
			<th class="<?php echo toggleRowClass($rcol); ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'eventTime','sortorder'=>$newSort)); ?>">
					<?php echo l('Date'); ?>
				</a>
				<?php if($playerObj->getOption('sort') == "eventTime") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $playerObj->getOption('sortorder'); ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo toggleRowClass($rcol); ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'eventType','sortorder'=>$newSort)); ?>">
					<?php echo l('Type'); ?>
				</a>
				<?php if($playerObj->getOption('sort') == "eventType") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $playerObj->getOption('sortorder'); ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo toggleRowClass($rcol); ?>"><?php echo l('Description'); ?></th>
			<th class="<?php echo toggleRowClass($rcol); ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'serverName','sortorder'=>$newSort)); ?>">
					<?php echo l('Server'); ?>
				</a>
				<?php if($playerObj->getOption('sort') == "serverName") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $playerObj->getOption('sortorder'); ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo toggleRowClass($rcol); ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'map','sortorder'=>$newSort)); ?>">
					<?php echo l('Map'); ?>
				</a>
				<?php if($playerObj->getOption('sort') == "map") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $playerObj->getOption('sortorder'); ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
		</tr>
<?php
	foreach($history as $entry) {
		$rcol = "row-dark";
		echo '<tr>';
		echo '<td class="',toggleRowClass($rcol),'">',$entry['eventTime'],'</td>';
		echo '<td class="',toggleRowClass($rcol),'">',$entry['eventType'],'</td>';
		echo '<td class="',toggleRowClass($rcol),'">',$entry['eventDesc'],'</td>';
		echo '<td class="',toggleRowClass($rcol),'">',$entry['serverName'],'</td>';
		echo '<td class="',toggleRowClass($rcol),'">',$entry['map'],'</td>';
		echo '</tr>';
	}
?>
	</table>
<?php
	}
	else {
		echo l('No Data');
	}
?>
</div>
