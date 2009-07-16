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

$weapon = false;
if(!empty($_GET["weapon"])) {
	if(validateInput($_GET["weapon"],'nospace') === true) {
		$weapon = $_GET["weapon"];
	}
	else {
		error("No weapon specified.");
	}
}

$query = mysql_query("SELECT name FROM ".DB_PREFIX."_Weapons
				WHERE code='".mysql_escape_string($weapon)."'
				AND game='".mysql_escape_string($game)."'");
if (mysql_num_rows($query) != 1) {
	$wep_name = ucfirst($weapon);
}
else {
	$result = mysql_fetch_assoc($query);
	$wep_name = $result["name"];
}
mysql_free_result($query);

pageHeader(
	array($gamename, "Weapon Details", htmlspecialchars($wep_name)),
	array(
		$gamename=>$g_options["scripturl"] . "?game=$game",
		"Weapon Statistics"=>$g_options["scripturl"] . "?mode=weapons&amp;game=$game",
		"Weapon Details"=>""
	),
	$wep_name
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
			ucfirst($weapon) . " kills",
			"width=35&align=right"
		),
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
		COUNT(".DB_PREFIX."_Events_Frags.weapon) AS frags
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Events_Frags.weapon='".mysql_escape_string($weapon)."'
		AND ".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking<>'1'
	GROUP BY
		".DB_PREFIX."_Events_Frags.killerId
	ORDER BY
		".$table->sort." ".$table->sortorder.",
		".$table->sort2." ".$table->sortorder."
	LIMIT ".$table->startitem.",".$table->numperpage."");

$queryCount = mysql_query("
	SELECT
		COUNT(DISTINCT ".DB_PREFIX."_Events_Frags.killerId) AS wc,
		SUM(".DB_PREFIX."_Events_Frags.weapon='$weapon') AS tc
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Events_Frags.weapon='$weapon'
		AND ".DB_PREFIX."_Players.game='$game'
");
$result = mysql_fetch_assoc($queryCount);
$numitems = $result['wc'];
$totalkills = $result['tc'];
mysql_free_result($queryCount);
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td colspan="2">
		<img src="<?php echo $g_options["imgdir"]; ?>/weapons/<?php echo $game; ?>/<?php echo $weapon; ?>.png" border="0" />
	</td>
</tr>
<tr>
	<td colspan="2" height="10px">&nbsp;</td>
</tr>
<tr>
	<td width="50%"><?php echo $g_options["font_normal"]; ?>From a total of <b><?php echo intval($totalkills); ?></b> kills (Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"]; ?></td>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?>Back to <a href="<?php echo $g_options["scripturl"] . "?mode=weapons&amp;game=$game"; ?>">Weapon Statistics</a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table><p>
<?php $table->draw($query, $numitems, 70, "center");
?>