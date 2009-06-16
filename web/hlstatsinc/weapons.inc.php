<?php
/**
 * $Id: weapons.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/weapons.inc.php $
 *
 * Original development:
 * +
 * + HLstats - Real-time player and clan rankings and statistics for Half-Life
 * + http://sourceforge.net/projects/hlstats/
 * +
 * + Copyright (C) 2001  Simon Garner
 * +
 *
 * Additional development:
 * +
 * + UA HLstats Team
 * + http://www.unitedadmins.com
 * + 2004 - 2007
 * +
 *
 *
 * Current development:
 * +
 * + Johannes 'Banana' Keßler
 * + http://hlstats.sourceforge.net
 * + 2007 - 2008
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



	// Weapon Statistics

	$game = sanitize($_GET['game']);

	$db->query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='$game'");
	if ($db->num_rows() < 1) error("No such game '$game'.");

	list($gamename) = $db->fetch_row();
	$db->free_result();

	pageHeader(
		array($gamename, "Weapon Statistics"),
		array($gamename=>"%s?game=$game", "Weapon Statistics"=>"")
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

	$db->query("
		SELECT
			COUNT(*)
		FROM
			".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
		WHERE
			".DB_PREFIX."_Players.game = '$game'
	");

	list($totalkills) = $db->fetch_row();

	$result = $db->query("
		SELECT
			".DB_PREFIX."_Events_Frags.weapon,
			IFNULL(".DB_PREFIX."_Weapons.modifier, 1.00) AS modifier,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) AS kills,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) / $totalkills * 100 AS percent
		FROM
			".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Weapons ON
			".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Frags.weapon
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
		WHERE
			".DB_PREFIX."_Players.game='$game'
			AND (".DB_PREFIX."_Weapons.game='$game' OR ".DB_PREFIX."_Weapons.weaponId IS NULL)
			AND ".DB_PREFIX."_Players.hideranking = 0
		GROUP BY
			".DB_PREFIX."_Events_Frags.weapon
		ORDER BY
			$tblWeapons->sort $tblWeapons->sortorder,
			$tblWeapons->sort2 $tblWeapons->sortorder
	");
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="50%"><?php echo $g_options["font_normal"]; ?>From a total of <b><?php echo $totalkills; ?></b> kills (Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"]; ?></td>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?>Back to <a href="<?php echo $g_options["scripturl"] . "?game=$game"; ?>"><?php echo $gamename; ?></a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table><p>
<?php $tblWeapons->draw($result, $db->num_rows($result), 90);
?>
