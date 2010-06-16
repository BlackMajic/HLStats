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

$return['status'] = false;
$return['msg'] = false;

// new one
if(isset($_POST['saveNews'])) {
	$subject = trim($_POST["subject"]);
	$subjectCheck = validateInput($subject,'text');

	$message = trim($_POST["message"]);
	$messageCheck = validateInput($message,'text');

	if(empty($messageCheck) || empty($subjectCheck)) {
		$return['msg'] = l('Please provide a subject and message');
		$return['status'] = "1";
	}
	else {
		$newsdate = date("Y-m-d H:i:s");
		$result = mysql_query("INSERT INTO ".DB_PREFIX."_News
							VALUES ('',
									'".$newsdate."',
									'".mysql_escape_string($adminObj-getUsername())."',
									'".mysql_escape_string($_POST["email"])."',
									'".mysql_escape_string($subject)."',
									'".mysql_escape_string($message)."')
							");
		$return['msg'] = l('News has been saved');
		$return['status'] = "2";
	}
}

// edit load
$post = false;
if(!empty($_GET['editpost'])) {
	$postnr = 0;
	if(!empty($_GET['editpost'])) {
		$postnr = sanitize($_GET['editpost']);
	}
	$check = validateInput($postnr,'digit');
	if(!empty($postnr) && $check === true) {
		$query = mysql_query("SELECT * FROM ".DB_PREFIX."_News
						WHERE `id` = '".mysql_escape_string($postnr)."'");
		$post = mysql_fetch_array($query);
		mysql_free_result($query);
	}
}

// edit save
if(isset($_POST['editNews']) && !empty($_GET['editpost'])) {
	if(isset($_POST['newsDelete']) && $_POST['newsDelete'] == "1") {
		$result = mysql_query("DELETE FROM ".DB_PREFIX."_News
									WHERE `id` = '".mysql_escape_string($_GET['saveEdit'])."'
								");
		echo "<b>".l('News has been deleted'),".</b><br><br>";
	}
	else {
		$newsID = $_GET['editpost'];

		$subject = trim($_POST["subject"]);
		$subjectCheck = validateInput($subject,'text');

		$message = trim($_POST["message"]);
		$messageCheck = validateInput($message,'text');

		if(empty($messageCheck) || empty($subjectCheck)) {
			$return['msg'] = l('Please provide a subject and message');
			$return['status'] = "1";
		}
		else {
			$newsdate = date("Y-m-d H:i:s");
			$result = mysql_query("UPDATE ".DB_PREFIX."_News
									SET `date` = '".$newsdate."',
										`user` = '".mysql_escape_string($adminObj->getUsername())."',
										`email` = '".mysql_escape_string($_POST["email"])."',
										`subject` = '".mysql_escape_string($_POST["subject"])."',
										`message` = '".mysql_escape_string($_POST["message"])."'
									WHERE `id` = '".mysql_escape_string($newsID)."'
								");
			$return['msg'] = l('News has been saved');
			$return['status'] = "2";
		}
	}
}

// load existing news
$newsArray = false;
$query = mysql_query("SELECT * FROM ".DB_PREFIX."_News ORDER BY `date` DESC");
if(mysql_num_rows($query) > 0) {
	while($result = mysql_fetch_assoc($query)) {
		$newsArray[] = $result;
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
	if(!empty($return)) {
		if($return['status'] === "1") {
			echo '<div class="error">',$return['msg'],'</div>';
		}
		elseif($return['status'] === "2") {
			echo '<div class="success">',$return['msg'],'</div>';
		}
	}
	if(!empty($post)) {
	?>
	<form method="post" action="">
		<table border="0" cellpadding="2" cellspacing="0">
			<tr>
				<th width="100px">
					<?php echo l('Author'); ?>:
				</th>
				<td>
					<input type="text" disabled="disabled" name="author"
						value="<?php echo $adminObj->getUsername(); ?>" />
				</td>
			</tr>
			<tr>
				<th width="100px">
					<?php echo l('E-Mail'); ?>:
				</th>
				<td>
					<input type="text" name="email" value="<?php echo $post['email']; ?>" />
				</td>
			</tr>
			<tr>
				<th width="100px">
					<?php echo l('Subject'); ?>:
				</th>
				<td>
					<input type="text" name="subject" value="<?php echo $post['subject']; ?>" />
				</td>
			</tr>
			<tr>
				<th width="100px" valign="top">
					<?php echo l('Message'); ?>:
				</th>
				<td>
					<textarea name="message" cols="70" rows="6" /><?php echo $post['message']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td width="100px">&nbsp;</td>
				<td><input type="submit" name="editNews" value=" <?php echo l('Save'); ?> " /></td>
			</tr>
		</table>
	</form>
	<?php } else { ?>
	<form method="post" action="">
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
	<?php
	}
	if(!empty($newsArray)) { ?>
		<table cellpadding="2" cellspacing="0" border="1" width="100%">
			<tr>
				<th><?php echo l('Date'); ?></th>
				<th><?php echo l('Subject'); ?></th>
				<th><?php echo l('Author'); ?></th>
			</tr>
			<?php
			foreach($newsArray as $entry) {
				echo '<tr>';

				echo '<td>',$entry['date'],'</td>';
				echo '<td><a href="index.php?mode=admin&amp;task=toolsNews&amp;editpost=',$entry['id'],'">',$entry['subject'],'</a></td>';
				echo '<td>',$entry['user'],'</td>';

				echo '</tr>';
			}
			?>
		</table>
	<?php }	?>
</div>