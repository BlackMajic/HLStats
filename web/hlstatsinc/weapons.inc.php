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
pageHeader(
	array($gamename, l("Weapon Statistics")),
	array($gamename=>"%s?game=$game", l("Weapon Statistics")=>"")
);


$tblWeapons = new Table(
	array(
		new TableColumn(
			"weapon",
			"Weapon",
			"width=21&type=weaponimg&align=center&link=" . urlencode("mode=weaponinfo&amp;weapon=%k&amp;game=$game")
		),
		new TableColumn(
			"modifier",
			"Points Modifier",
			"width=10&align=right"
		),
		new TableColumn(
			"kills",
			"Kills",
			"width=12&align=right"
		),
		new TableColumn(
			"percent",
			"Percentage of Kills",
			"width=40&sort=no&type=bargraph"
		),
		new TableColumn(
			"percent",
			"%",
			"width=12&sort=no&align=right&append=" . urlencode("%")
		)
	),
	"weapon",
	"kills",
	"weapon",
	true,
	9999,
	"weap_page",
	"weap_sort",
	"weap_sortorder"
);

$killCount = mysql_query("
	SELECT
		COUNT(".DB_PREFIX."_Players.playerId) kc
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Players.game = '".mysql_escape_string($game)."'");
$result = mysql_fetch_assoc($killCount);
$totalkills = $result['kc'];
mysql_free_result($killCount);

$result = mysql_query("
	SELECT
		".DB_PREFIX."_Events_Frags.weapon,
		".DB_PREFIX."_Weapons.modifier AS modifier,
		COUNT(".DB_PREFIX."_Events_Frags.weapon) AS kills,
		COUNT(".DB_PREFIX."_Events_Frags.weapon) / ".mysql_escape_string($totalkills)." * 100 AS percent
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Weapons ON
		".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Frags.weapon
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Weapons.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0
	GROUP BY
		".DB_PREFIX."_Events_Frags.weapon
	ORDER BY
		".$tblWeapons->sort." ".$tblWeapons->sortorder.",
		".$tblWeapons->sort2." ".$tblWeapons->sortorder."");
?>
<p>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%"><?php echo $g_options["font_normal"]; ?><?php echo l('From a total of'); ?> <b><?php echo $totalkills; ?></b> <?php echo l('kills'); ?> (<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?>)<?php echo $g_options["fontend_normal"]; ?></td>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?><?php echo l('Back to'); ?> <a href="<?php echo $g_options["scripturl"] . "?game=$game"; ?>"><?php echo $gamename; ?></a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table>
</p>
<?php $tblWeapons->draw($result, mysql_num_rows($result), 90); ?>
