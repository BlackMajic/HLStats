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
	$id = sanitize($_GET['id']);
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=toolsEditdetails">Edit Player or Clan Details</a></b><br>

<img src="<?php echo $g_options["imgdir"]; ?>/spacer.gif" width="1" height="8" border="0"><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo "Edit Player #$id"; ?></b><p>

<?php

	$proppage = new PropertyPage(DB_PREFIX."_Players", "playerId", $id, array(
		new PropertyPage_Group("Profile", array(
			new PropertyPage_Property("fullName", "Real Name", "text"),
			new PropertyPage_Property("email", "E-mail Address", "text"),
			new PropertyPage_Property("homepage", "Homepage URL", "text"),
			new PropertyPage_Property("icq", "ICQ Number", "text"),
			new PropertyPage_Property("hideranking", "Hide Ranking", "select", "0/No;1/Yes"),
			new PropertyPage_Property("clan", "Delete From Clan", "checkbox"),
			new PropertyPage_Property("reset", "Reset player stats", "checkbox"),
		))
	));


	if (isset($_POST['resetPlayerSubmit'])) {
		if(isset($_POST['reset'])) {
			// reset the players stats
			$query = "UPDATE ".DB_PREFIX."_Players
						SET kills = 0,
							deaths = 0,
							suicides = 0,
							skill = 1000
						WHERE `playerId` = '".$id."'";
			mysql_query($query);

		}
		unset($_POST['reset']);
		$proppage->update();
		message("success", "Profile updated successfully.");
	}


	$query = mysql_query("SELECT * FROM ".DB_PREFIX."_Players WHERE playerId='$id'");
	if (mysql_num_rows($query) < 1) die("No player exists with ID #$id");

	$data = mysql_fetch_assoc($query);
	$data['reset'] = '';

	echo $g_options["font_title"];
	echo $data["lastName"];
	echo $g_options["fontend_title"];

	echo $g_options["font_normal"];
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
		. "<a href=\"" . $g_options["scripturl"] . "?mode=playerinfo&player=$id\">"
		. "(View Player Details)</a>";
	echo $g_options["fontend_normal"];
?><p>

<form method="post" action="">
<table width="60%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td><?php
		echo $g_options["font_normal"];
		$proppage->draw($data);
		echo $g_options["fontend_normal"];
?>
	<center><input type="submit" value="  Apply  " name="resetPlayerSubmit" class="submit"></center>
	</td>
</tr>
</table>
</form>