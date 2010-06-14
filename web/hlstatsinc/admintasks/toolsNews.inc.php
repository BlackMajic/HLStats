<?php
/**
 * news administration for front page
 * @package HLStats
 * @author Johannes 'Banana' Keßler
 * @copyright Johannes 'Banana' Keßler
 */

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
 * + 2007 - 2010
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

$post = false;
if(!empty($_GET['editpost'])) {

	$postnr = 0;
	if(!empty($_GET['editpost'])) {
		$postnr = sanitize($_GET['editpost']);
	}
	$result = mysql_query("SELECT * FROM ".DB_PREFIX."_News
					WHERE id = '".mysql_escape_string($postnr)."'");
	$post = mysql_fetch_array($result);
	mysql_free_result($query);
}

if(isset($_POST['saveNews'])) {
	if ($_POST["subject"] == "") {
		echo "<b>",l('Error: Please provide a subject'),".</b><br><br>";
	}
	elseif ($_POST["message"] == "") {
		echo "<b>".l('Error: Please provide a Message'),".</b><br><br>";
	}
	else {
		$newsdate = date("Y-m-d H:i:s");
		$result = mysql_query("INSERT INTO ".DB_PREFIX."_News
							VALUES ('',
									'".$newsdate."',
									'".$auth->userdata["username"]."',
									'".$_POST["email"]."',
									'".$_POST["subject"]."',
									'".$_POST["message"]."')
							");
		echo "<b>".l('News has been saved'),".</b><br><br>";
	}
}

if(isset($_POST['editNews'])) {
	if(isset($_POST['newsDelete']) && $_POST['newsDelete'] == "1") {
		$result = mysql_query("DELETE FROM ".DB_PREFIX."_News
									WHERE id = '".$_GET['saveEdit']."'
								");
		echo "<b>".l('News has been deleted'),".</b><br><br>";
	}
	else {
		if ($_POST["subject"] == "") {
			echo "<b>",l('Error: Please provide a subject'),".</b><br><br>";
		}
		elseif ($_POST["message"] == "") {
			echo "<b>".l('Error: Please provide a Message'),".</b><br><br>";
		}
		else {
			$newsdate = date("Y-m-d H:i:s");
			$result = mysql_query("UPDATE ".DB_PREFIX."_News
									SET date = '".$newsdate."',
										user = '".$auth->userdata["username"]."',
										email = '".$_POST["email"]."',
										subject = '".$_POST["subject"]."',
										message = '".$_POST["message"]."'
									WHERE id = '".$_GET['saveEdit']."'
								");
			echo "<b>".l('News has been saved'),".</b><br><br>";
		}
	}
}

pageHeader(array(l("Admin"),l('News at Front page')), array(l("Admin")=>"index.php?mode=admin",l('News at Front page')=>''));
?>

<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?mode=admin"; ?>"><?php echo l('Back to admin overview'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l('News at Front page'); ?></h1>
	<p><?php echo l('Here you can write and edit the news which are displayed at the front page'); ?></p>
	<?php
	if(!empty($post)) {
	?>
	<?php } else { ?>
	<form method="post" action="index.php?mode=admin&task=toolsNews">
		<table border="0" cellpadding="2" cellspacing="0">
			<tr>
				<th width="100px">
					<?php echo l('Author'); ?>:
				</th>
				<td>
					<input type="text" disabled="disabled" name="author"
						value="<?php echo $adminObj->getUsername();?>" />
				</td>
			</tr>
			<tr>
				<th width="100px">
					<?php echo l('E-Mail'); ?>:
				</th>
				<td>
					<input type="text" name="email" value="" />
				</td>
			</tr>
			<tr>
				<th width="100px">
					<?php echo l('Subject'); ?>:
				</th>
				<td>
					<input type="text" name="subject" value="" />
				</td>
			</tr>
			<tr>
				<th width="100px" valign="top">
					<?php echo l('Message'); ?>:
				</th>
				<td>
					<textarea name="message" cols="70" rows="6" /></textarea>
				</td>
			</tr>
			<tr>
				<td width="100px">&nbsp;</td>
				<td><input type="submit" name="saveNews" value=" <?php echo l('Save'); ?> " /></td>
			</tr>
		</table>
	</form>
	<?php }	?>
</div>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif">
	<b>&nbsp;<a href="index.php?mode=admin&task=toolsNews"><?php echo $task->title; ?></a></b>

<p><?php echo l('Here you can write and edit the news which are displayed at the front page'); ?></p>

<?php
if(!empty($_GET['editpost'])) {

	$postnr = 0;
	if(!empty($_GET['editpost'])) {
		$postnr = sanitize($_GET['editpost']);
	}
	$result = mysql_query("SELECT * FROM ".DB_PREFIX."_News WHERE id = $postnr");
	$post = mysql_fetch_array($result);
?>

<form method="post" action="index.php?mode=admin&amp;task=toolsNews&amp;saveEdit=<?php echo $postnr; ?>">
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr valign="top">
		<td width="100%">
			<form method="post" action="index.php?mode=admin&task=toolsNews">
				<table border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b><?php echo l('Author'); ?>:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" disabled="disabled" name="author" value="<?php echo $post['user'];?>" /></td>
					</tr>
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b><?php echo l('E-Mail'); ?>:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" name="email" value="<?php echo $post['email'];?>" /></td>
					</tr>
					<tr>
						<td width="100px"><?php echo $g_options["font_normal"]; ?><b><?php echo l('Subject'); ?>:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><input type="text" name="subject" value="<?php echo $post['subject'];?>" /></td>
					</tr>
					<tr>
						<td width="100px" valign="top"><?php echo $g_options["font_normal"]; ?><b><?php echo l('Message'); ?>:</b><?php echo $g_options["fontend_normal"]; ?></td>
						<td><textarea name="message" cols="70" rows="6" /><?php echo $post['message'];?></textarea></td>
					</tr>
					<tr>
						<td width="100px">&nbsp;</td>
						<td>
							<?php echo $g_options["font_normal"]; ?>
								<input type="submit" name="editNews" value=" <?php echo l('Edit'); ?>  " />&nbsp;
								<input type="checkbox" name="newsDelete" value="1" />&nbsp;<b><?php echo l('DELETE NEWS'); ?></b>
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
				"width=50&embedlink=yes&link=" . urlencode("mode=admin&task=toolsNews&editpost=%k")
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

$query = mysql_query("SELECT * FROM ".DB_PREFIX."_News
	ORDER BY ".$table->sort." ".$table->sortorder.", ".$table->sort2." ".$table->sortorder."
	LIMIT ".$table->startitem.",".$table->numperpage."");
$resultCount = mysql_query("SELECT COUNT(*) AS nc FROM ".DB_PREFIX."_News");
$numitems = $resultCount['nc'];
$table->draw($query, $numitems, 100, "");
?>
