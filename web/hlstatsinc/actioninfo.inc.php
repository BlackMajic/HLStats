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
$action = false;
if(!empty($_GET["action"])) {
	if(validateInput($_GET["action"],'nospace') === true) {
		$action = $_GET["action"];
	}
	else {
		error("No action specified.");
	}
}

$query = mysql_query("SELECT description FROM ".DB_PREFIX."_Actions
					WHERE code='".mysql_escape_string($action)."'
						AND game='".mysql_escape_string($game)."'");
if (mysql_num_rows($query) != 1) {
	$act_name = ucfirst($action);
}
else {
	$result = mysql_fetch_assoc($query);
	$act_name = $result["description"];
}
mysql_free_result($query);

$query = mysql_query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='".mysql_escape_string($game)."'");
if (mysql_num_rows($query) != 1) {
	error("Invalid or no game specified.");
}
else {
	$result = mysql_fetch_assoc($query);
	$gamename = $result['name'];
}

pageHeader(
	array($gamename, "Action Details", $act_name),
	array(
		$gamename=>$g_options["scripturl"] . "?game=$game",
		"Action Statistics"=>$g_options["scripturl"] . "?mode=actions&amp;game=$game",
		"Action Details"=>""
	),
	$act_name
);



$table = new Table(
	array(
		new TableColumn(
			"playerName",
			"Player",
			"width=45&align=left&icon=player&link=" . urlencode("mode=playerinfo&amp;player=%k")
		),
		new TableColumn(
			"obj_count",
			"Achieved",
			"width=25&align=right"
		),
		new TableColumn(
			"obj_bonus",
			"Skill Bonus Total",
			"width=25&align=right&sort=no"
		)
	),
	"playerId",
	"obj_count",
	"playerName",
	true,
	50
);

$query = mysql_query("
	SELECT
		".DB_PREFIX."_Events_PlayerActions.playerId,
		".DB_PREFIX."_Players.lastName AS playerName,
		COUNT(".DB_PREFIX."_Events_PlayerActions.id) AS obj_count,
		COUNT(".DB_PREFIX."_Events_PlayerActions.id) * ".DB_PREFIX."_Actions.reward_player AS obj_bonus
	FROM
		".DB_PREFIX."_Events_PlayerActions, ".DB_PREFIX."_Players, ".DB_PREFIX."_Actions
	WHERE
		".DB_PREFIX."_Actions.code = '".mysql_escape_string($action)."' AND
		".DB_PREFIX."_Players.game = '".mysql_escape_string($game)."' AND
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_PlayerActions.playerId AND
		".DB_PREFIX."_Events_PlayerActions.actionId = ".DB_PREFIX."_Actions.id AND
		".DB_PREFIX."_Players.hideranking<>'1'
	GROUP BY
		".DB_PREFIX."_Events_PlayerActions.playerId
	ORDER BY
		".$table->sort." ".$table->sortorder.",
		".$table->sort2." ".$table->sortorder."
	LIMIT ".$table->startitem.",".$table->numperpage."
");

$queryCount = mysql_query("
	SELECT
		COUNT(DISTINCT ".DB_PREFIX."_Events_PlayerActions.playerId) AS ac,
		COUNT(".DB_PREFIX."_Events_PlayerActions.Id) AS tc
	FROM
		".DB_PREFIX."_Events_PlayerActions, ".DB_PREFIX."_Players, ".DB_PREFIX."_Actions
	WHERE
		".DB_PREFIX."_Actions.code = '$action' AND
		".DB_PREFIX."_Players.game = '$game' AND
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_PlayerActions.playerId AND
		".DB_PREFIX."_Events_PlayerActions.actionId = ".DB_PREFIX."_Actions.id
");
$result = mysql_fetch_assoc($queryCount);
$numitems = $result['ac'];
$totalact = $result['tc'];
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%"><?php echo $g_options["font_normal"]; ?>From a total of <b><?php echo intval($totalact); ?></b> achievements (Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"]; ?></td>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?>Back to <a href="<?php echo $g_options["scripturl"] . "?mode=actions&amp;game=$game"; ?>">Action Statistics</a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table><p>
<?php
	$table->draw($query, $numitems, 90, "center");
?>
