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
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo "Edit Clan #$id"; ?></b><p>


<?php
	$proppage = new PropertyPage(DB_PREFIX."_Clans", "clanId", $id, array(
		new PropertyPage_Group("Profile", array(
			new PropertyPage_Property("name", "Clan Name", "text"),
			new PropertyPage_Property("homepage", "Homepage URL", "text")
		))
	));


	if ($_POST) {
		$proppage->update();
		message("success", "Profile updated successfully.");
	}



	$query = mysql_query("SELECT * FROM ".DB_PREFIX."_Clans WHERE clanId='$id'");
	if (mysql_num_rows($query) < 1) die("No clan exists with ID #$id");

	$data = mysql_fetch_assoc($query);

	echo $g_options["font_title"];
	echo $data["tag"];
	echo $g_options["fontend_title"];

	echo $g_options["font_normal"];
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
		. "<a href=\"" . $g_options["scripturl"] . "?mode=claninfo&clan=$id&" . strip_tags(SID) . "\">"
		. "(View Clan Details)</a>";
	echo $g_options["fontend_normal"];
?><p>
<form method="POST" action="<?php echo $g_options["scripturl"] . "?mode=admin&task=$selTask&id=$id&" . strip_tags(SID); ?>">
<table width="60%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td><?php
		echo $g_options["font_normal"];
		$proppage->draw($data);
		echo $g_options["fontend_normal"];
?>
	<center><input type="submit" value="  Apply  " class="submit"></center></td>
</tr>
</table>
</form>

