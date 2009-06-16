<?php
/**
 * $Id: tools_news.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/admintasks/tools_news.inc.php $
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

	if(isset($_POST['saveNews'])) {
		if ($_POST["subject"] == "") {
			echo "<b>Error: Please provide a subject.</b><br><br>";
		}
		elseif ($_POST["message"] == "") {
			echo "<b>Error: Please provide a Message.</b><br><br>";
		}
		else {
			$newsdate = date("Y-m-d H:i:s");
			$result = $db->query("INSERT INTO ".DB_PREFIX."_News
								VALUES ('',
										'".$newsdate."',
										'".$auth->userdata["username"]."',
										'".$_POST["email"]."',
										'".$_POST["subject"]."',
										'".$_POST["message"]."')
								");
			echo "<b>News has been saved.</b><br><br>";
		}
	}

	if(isset($_POST['editNews'])) {
		if(isset($_POST['newsDelete']) && $_POST['newsDelete'] == "1") {
			$result = $db->query("DELETE FROM ".DB_PREFIX."_News
										WHERE id = '".$_GET['saveEdit']."'
									");
			echo "<b>News has been deleted.</b><br><br>";
		}
		else {
			if ($_POST["subject"] == "") {
				echo "<b>Error: Please provide a subject.</b><br><br>";
			}
			elseif ($_POST["message"] == "") {
				echo "<b>Error: Please provide a Message.</b><br><br>";
			}
			else {
				$newsdate = date("Y-m-d H:i:s");
				$result = $db->query("UPDATE ".DB_PREFIX."_News
										SET date = '".$newsdate."',
											user = '".$auth->userdata["username"]."',
											email = '".$_POST["email"]."',
											subject = '".$_POST["subject"]."',
											message = '".$_POST["message"]."'
										WHERE id = '".$_GET['saveEdit']."'
									");
				echo "<b>News has been saved.</b><br><br>";
			}
		}
	}
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif">
	<b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=tools_news"><?php echo $task->title; ?></a></b>

<p>Here you can write and edit the news which are displayed at the front page</p>

<?php
if($_GET['editpost'] != "") {

	$postnr = $_GET['editpost'];
	$result = $db->query("SELECT * FROM ".DB_PREFIX."_News WHERE id = $postnr");
	$post = mysql_fetch_array($result);
?>

<form method="post" action="<?php echo $g_options["scripturl"]; ?>?mode=admin&amp;task=tools_news&amp;saveEdit=<?php echo $postnr; ?>">
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr valign="top">
		<td width="100%">
			<form method="post" action="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=tools_news">
				<table border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b>Author:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" disabled="disabled" name="author" value="<?php echo $post['user'];?>" /></td>
					</tr>
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b>E-Mail:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" name="email" value="<?php echo $post['email'];?>" /></td>
					</tr>
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b>Subject:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" name="subject" value="<?php echo $post['subject'];?>" /></td>
					</tr>
					<tr>
						<td width="100px" valign="top"><?php echo $g_options["font_normal"]; ?><b>Message:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><textarea name="message" cols="70" rows="6" /><?php echo $post['message'];?></textarea></td>
					</tr>
					<tr>
						<td width="100px">&nbsp;</td>
						<td>
							<?php echo $g_options["font_normal"]; ?>
								<input type="submit" name="editNews" value="  Edit  " />&nbsp;
								<input type="checkbox" name="newsDelete" value="1" />&nbsp;<b>DELETE NEWS</b>
							<?php echo $g_options["fontend_normal"]; ?>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
</form>

<?php
}
else {
?>

<form method="post" action="<?php echo $g_options["scripturl"]; ?>?mode=admin&amp;task=tools_news">
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr valign="top">
		<td width="100%">
			<form method="post" action="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=tools_news">
				<table border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b>Author:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" disabled="disabled" name="author" value="<?php echo $auth->userdata["username"];?>" /></td>
					</tr>
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b>E-Mail:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" name="email" /></td>
					</tr>
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b>Subject:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" name="subject" /></td>
					</tr>
					<tr>
						<td width="100px" valign="top"><?php echo $g_options["font_normal"]; ?><b>Message:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><textarea name="message" cols="70" rows="6" /></textarea></td>
					</tr>
					<tr>
						<td width="100px">&nbsp;</td>
						<td><input type="submit" name="saveNews" value="  Save  " /></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
</form>

<?php
}
	$table = new Table(
		array(
			new TableColumn(
				"date",
				"Date",
				"width=25"
			),
			new TableColumn(
				"subject",
				"Subject",
				"width=50&embedlink=yes&link=" . urlencode("mode=admin&task=tools_news&editpost=%k")
			),
			new TableColumn(
				"user",
				"Author",
				"width=25"
			)
		),
		"id",
		"date",
		"user",
		false,
		50,
		"page",
		"sort",
		"sortorder"
	);

$result = $db->query("SELECT * FROM ".DB_PREFIX."_News ORDER BY $table->sort $table->sortorder, $table->sort2 $table->sortorder LIMIT $table->startitem,$table->numperpage");
$resultCount = $db->query("SELECT COUNT(*) FROM ".DB_PREFIX."_News");
list($numitems) = $db->fetch_row($resultCount);
$table->draw($result, $numitems, 100, "");
?>
