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

	if ($auth->userdata["acclevel"] < 100) die ("Access denied!");

	$edlist = new EditList("username", DB_PREFIX."_Users", "user", false);
	$edlist->columns[] = new EditListColumn("username", l("Username"), 15, true, "text", "", 16);
	$edlist->columns[] = new EditListColumn("password", l("Password"), 15, true, "password", "", 16);
	$edlist->columns[] = new EditListColumn("acclevel", l("Access Level"), 25, true, "select", "0/No Access;80/Restricted;100/Administrator");


	if ($_POST)
	{
		if ($edlist->update())
			message("success", l("Operation successful"));
		else
			message("warning", $edlist->error());
	}

?>
<p>
<?php echo ('Usernames and passwords can be set up for access to this HLStats Admin area. For most sites you will only want one admin user - yourself. Some sites may however need to give administration access to several people'); ?>
</p>
<p>
	<b><?php echo l('Note'); ?></b>
	<?php echo l("Passwords are encrypted in the database and so cannot be viewed. However, you can change a user's password by entering a new plain text value in the Password field"); ?>.
</p>
<p>
	<b><?php echo l('Access Levels'); ?></b><br>

&#149; <i><?php echo l('Restricted'); ?></i> <?php echo l('users only have access to the Host Groups, Clan Tag Patterns, Weapons, Teams, Awards and Actions configuration areas. This means these users cannot set Options or add new Games, Servers or Admin Users to HLStats, or use any of the admin Tools'); ?>.<br>
&#149; <i><?php echo l('Administrator'); ?></i> <?php echo l('users have full, unrestricted access'); ?>.
</p>

<?php

	$query = mysql_query("
		SELECT
			username,
			'(encrypted)' AS password,
			acclevel
		FROM
			".DB_PREFIX."_Users
		ORDER BY
			username
	");

	$edlist->draw($query);
?>

<table width="75%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" value="  <?php echo l('Apply'); ?>  " class="submit"></td>
</tr>
</table>
