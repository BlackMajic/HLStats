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

	$edlist = new EditList("weaponId", DB_PREFIX."_Weapons", "gun");
	$edlist->columns[] = new EditListColumn("game", "Game", 0, true, "hidden", $gamecode);
	$edlist->columns[] = new EditListColumn("code", "Weapon Code", 15, true, "text", "", 32);
	$edlist->columns[] = new EditListColumn("name", "Weapon Name", 25, true, "text", "", 64);
	$edlist->columns[] = new EditListColumn("modifier", "Points Modifier", 10, true, "text", "1.00");


	if ($_POST)
	{
		if ($edlist->update())
			message("success", l("Operation successful"));
		else
			message("warning", $edlist->error());
	}
?>

<?php echo l('You can give each weapon a "points modifier", amultiplier which determines how many points will be gained or lost for killing with or being killed by that weapon'); ?>.
<?php echo l('(Refer to'), ' <a href="index.php?mode=help#points">', l('Help'),'</a>' ?>
<?php echo l("for a full description of how points ratings are calculated.) The baseline points modifier for weapons is 1.00. A points modifier of 0.00 will cause kills with that weapon to have no effect on players' points"); ?>.<p>

<?php


	$query = mysql_query("
		SELECT
			weaponId,
			code,
			name,
			modifier
		FROM
			".DB_PREFIX."_Weapons
		WHERE
			game='".mysql_escape_string($gamecode)."'
		ORDER BY
			code ASC
	");

	$edlist->draw($query);
?>

<table width="75%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" value=" <?php echo l('Apply'); ?> " class="submit"></td>
</tr>
</table>
