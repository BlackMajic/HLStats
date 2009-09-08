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

	$edlist = new EditList("roleId", DB_PREFIX."_Roles", "role");
	$edlist->columns[] = new EditListColumn("game", "Game", 0, true, "hidden", $gamecode);
	$edlist->columns[] = new EditListColumn("code", "Role Code", 20, true, "text", "", 32);
	$edlist->columns[] = new EditListColumn("name", "Role Name", 20, true, "text", "", 64);
	$edlist->columns[] = new EditListColumn("hidden", "Hide Role", 0, false, "checkbox");


	if ($_POST)
	{
		if ($edlist->update())
			message("success", l("Operation successful"));
		else
			message("warning", $edlist->error());
	}
?>

<?php echo l("You can specify descriptive names for each game's role codes"); ?>.<p>

<?php $result = mysql_query("
		SELECT
			roleId,
			code,
			name,
			hidden
		FROM
			".DB_PREFIX."_Roles
		WHERE
			game='$gamecode'
		ORDER BY
			code ASC
	");

	$edlist->draw($result);
?>

<table width="75%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" value=" <?php echo l('Apply'); ?> " class="submit"></td>
</tr>
</table>
