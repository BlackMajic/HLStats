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

// Map Details

$map = false;
if(!empty($_GET["map"])) {
	if(validateInput($_GET["map"],'nospace') === true) {
		$map = $_GET["map"];
	}
	else {
		error("No map specified.");
	}
}
pageHeader(
	array($gamename, l("Map Details"), $map),
	array(
		$gamename=>$g_options["scripturl"] . "?game=$game",
		l("Map Statistics")=>$g_options["scripturl"] . "?mode=maps&amp;game=$game",
		l("Map Details")=>""
	),
	$map
);



$table = new Table(
	array(
		new TableColumn(
			"killerName",
			"Player",
			"width=60&align=left&icon=player&link=" . urlencode("mode=playerinfo&amp;player=%k")
		),
		new TableColumn(
			"frags",
			l("Kills on")."&nbsp;".$map,
			"width=35&align=right",
			false
		)
	),
	"killerId", // keycol
	"frags", // sort_default
	"killerName", // sort_default2
	true, // showranking
	50 // numperpage
);

$query = mysql_query("
	SELECT
		".DB_PREFIX."_Events_Frags.killerId,
		".DB_PREFIX."_Players.lastName AS killerName,
		COUNT(".DB_PREFIX."_Events_Frags.map) AS frags,
		".DB_PREFIX."_Players.active
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Events_Frags.map='".mysql_escape_string($map)."'
		AND ".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0
	GROUP BY
		".DB_PREFIX."_Events_Frags.killerId
	ORDER BY
		".$table->sort." ".$table->sortorder.",
		".$table->sort2." ".$table->sortorder."
	LIMIT ".$table->startitem.",".$table->numperpage."");

$queryCount = mysql_query("
	SELECT
		COUNT(DISTINCT ".DB_PREFIX."_Events_Frags.killerId) AS cc,
		SUM(".DB_PREFIX."_Events_Frags.map='".mysql_escape_string($map)."') AS tc
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Events_Frags.map='".mysql_escape_string($map)."'
		AND ".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0
");
$result = mysql_fetch_assoc($queryCount);
$numitems = $result['cc'];
$totalkills = $result['tc'];
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%"><?php echo $g_options["font_normal"]; ?><?php echo l('From a total of'); ?> <b><?php echo intval($totalkills); ?></b> <?php echo l('kills'); ?> (<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?>)<?php echo $g_options["fontend_normal"]; ?></td>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?><?php echo l('Back to'); ?> <a href="<?php echo $g_options["scripturl"] . "?mode=maps&amp;game=$game"; ?>"><?php echo l('Map Statistics'); ?></a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table><p>
<?php // figure out URL and absolute path of image
	$imgurl = $g_options["imgdir"] . "/maps/$game/". "$map";
	$image = getImage("/maps/$game/$map");

	if ($image || $g_options["map_dlurl"]) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td width="90%"><?php
		$table->draw($query, $numitems, 100, "center");
?></td>
	<td width=10>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td width="10%" align="right" nowrap><?php
		echo $g_options["font_normal"];

		if ($image) {
			echo "<img src=\"" . $image["url"] . "\" " . $image["size"] . " border=1 alt=\"$map\">";
		}
		if ($g_options["map_dlurl"]) {
			$map_dlurl = str_replace("%MAP%", $map, $g_options["map_dlurl"]);
			$map_dlurl = str_replace("%GAME%", $game, $map_dlurl);
			echo "<p><a href=\"$map_dlurl\">Download this map...</a></p>";
		}
		echo $g_options["fontend_normal"];
?></td>
</tr>

</table>
<?php
	}
	else {
		$table->draw($query, $numitems, 90, "center");
	}
?>
