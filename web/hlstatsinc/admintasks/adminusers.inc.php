<?php
/**
 * $Id: adminusers.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstatsinc/admintasks/adminusers.inc.php $
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

	if ($auth->userdata["acclevel"] < 100) die ("Access denied!");

	$edlist = new EditList("username", DB_PREFIX."_Users", "user", false);
	$edlist->columns[] = new EditListColumn("username", "Username", 15, true, "text", "", 16);
	$edlist->columns[] = new EditListColumn("password", "Password", 15, true, "password", "", 16);
	$edlist->columns[] = new EditListColumn("acclevel", "Access Level", 25, true, "select", "0/No Access;80/Restricted;100/Administrator");


	if ($_POST)
	{
		if ($edlist->update())
			message("success", "Operation successful.");
		else
			message("warning", $edlist->error());
	}

?>

Usernames and passwords can be set up for access to this HLstats Admin area. For most sites you will only want one admin user - yourself. Some sites may however need to give administration access to several people.<p>

<b>Note</b> Passwords are encrypted in the database and so cannot be viewed. However, you can change a user's password by entering a new plain text value in the Password field.<p>

<b>Access Levels</b><br>

&#149; <i>Restricted</i> users only have access to the Host Groups, Clan Tag Patterns, Weapons, Teams, Awards and Actions configuration areas. This means these users cannot set Options or add new Games, Servers or Admin Users to HLstats, or use any of the admin Tools.<br>
&#149; <i>Administrator</i> users have full, unrestricted access.<p>

<?php

	$result = $db->query("
		SELECT
			username,
			'(encrypted)' AS password,
			acclevel
		FROM
			".DB_PREFIX."_Users
		ORDER BY
			username
	");

	$edlist->draw($result);
?>

<table width="75%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" value="  Apply  " class="submit"></td>
</tr>
</table>

