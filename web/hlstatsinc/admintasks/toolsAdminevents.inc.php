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

	if ($auth->userdata["acclevel"] < 80) die ("Access denied!");
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo $task->title; ?></b> (Last <?php echo DELETEDAYS; ?> Days)<p>

<?php
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

	mysql_query("DROP TABLE IF EXISTS ".DB_PREFIX."_AdminEventHistory");
	mysql_query("
		CREATE TEMPORARY TABLE ".DB_PREFIX."_AdminEventHistory
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
				".DB_PREFIX."_AdminEventHistory
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

	insertEvents("Rcon", "
		SELECT
			CONCAT(<table>.type, ' Rcon'),
			<table>.eventTime,
			CONCAT('\"', command, '\"\nFrom: ', remoteIp, ', password: \"', password, '\"'),
			".DB_PREFIX."_Servers.name,
			<table>.map
		FROM
			<table>
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId = <table>.serverId
	");

	insertEvents("Admin", "
		SELECT
			<table>.type,
			<table>.eventTime,
			IF(playerName != '',
				CONCAT('\"', playerName, '\": ', message),
				message
			),
			".DB_PREFIX."_Servers.name,
			<table>.map
		FROM
			<table>
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId = <table>.serverId
	");

	$type = $_GET['type'];
	$where = "";
	if (!empty($type)) {
		$where = "WHERE eventType='".mysql_escape_string($type)."'";
	}

	$query = mysql_query("
		SELECT
			eventTime,
			eventType,
			eventDesc,
			serverName,
			map
		FROM
			".DB_PREFIX."_AdminEventHistory
		$where
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder
		LIMIT
			$table->startitem,$table->numperpage
	");

	$resultCount = mysql_query("SELECT COUNT(*) AS ac FROM ".DB_PREFIX."_AdminEventHistory $where");
	$result = mysql_fetch_assoc($resultCount);
	$numitems = $result['ac'];
?>
<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
<input type="hidden" name="mode" value="admin">
<input type="hidden" name="task" value="<?php if(!empty($code)) echo $code; ?>">
<input type="hidden" name="sort" value="<?php if(!empty($sort)) echo $sort; ?>">
<input type="hidden" name="sortorder" value="<?php if(!empty($sortorder)) echo $sortorder; ?>">

<b>&#149;</b> <?php echo l('Show only events of type'); ?>: <?php
	$resultTypes = mysql_query("
		SELECT
			DISTINCT eventType
		FROM
			".DB_PREFIX."_AdminEventHistory
		ORDER BY
			eventType ASC
	");

	$types[""] = "All";

	while ($result = mysql_fetch_assoc($resultTypes))
	{
		$types[$result['eventType']] = $result['eventType'];
	}

	echo getSelect("type", $types, $type,false);
?> <input type="submit" value="Filter" class="smallsubmit"><p>
</form>
<?php
	$table->draw($query, $numitems, 100, "");
?>
