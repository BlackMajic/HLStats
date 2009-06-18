<?php
/**
 * $Id: teams.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstatsinc/admintasks/teams.inc.php $
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

	if ($auth->userdata["acclevel"] < 80) die ("Access denied!");

	$edlist = new EditList("teamId", DB_PREFIX."_Teams", "team");
	$edlist->columns[] = new EditListColumn("game", "Game", 0, true, "hidden", $gamecode);
	$edlist->columns[] = new EditListColumn("code", "Team Code", 20, true, "text", "", 32);
	$edlist->columns[] = new EditListColumn("name", "Team Name", 20, true, "text", "", 64);
	$edlist->columns[] = new EditListColumn("hidden", "<center>Hide Team</center>", 0, false, "checkbox");


	if ($_POST)
	{
		if ($edlist->update())
			message("success", "Operation successful.");
		else
			message("warning", $edlist->error());
	}
?>

You can specify descriptive names for each game's team codes.<p>

<?php $result = $db->query("
		SELECT
			teamId,
			code,
			name,
			hidden
		FROM
			".DB_PREFIX."_Teams
		WHERE
			game='$gamecode'
		ORDER BY
			code ASC
	");

	$edlist->draw($result);
?>

<table width="75%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" value="  Apply  " class="submit"></td>
</tr>
</table>

