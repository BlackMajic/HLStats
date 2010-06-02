<?php
/**
 * admin edit player or clan details
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

$return = false;
$editMode = false;
$playerObj = false;

require('class/player.class.php');

if(!empty($_GET["playerId"])) {
	if(validateInput($_GET["playerId"],'digit') === true) {
		$playerObj = new Player($_GET["playerId"],false);
		$editMode = 'player';
	}
}

// process the edit
if(isset($_POST['submit']['editPlayer']) && !empty($playerObj)) {

	// the checkboxes
	if(!isset($_POST['details']['deletefromclan'])) {
		$_POST['details']['deletefromclan'] = "0";
	}
	if(!isset($_POST['details']['resetstats'])) {
		$_POST['details']['resetstats'] = "0";
	}

	$check = $playerObj->checkFields($_POST['details']);
}

// process the search
if(isset($_POST['submit']['searchForId'])) {
	$searchFor = trim($_POST['search']['ID']);
	$searchWhere = trim($_POST['search']['what']);
	$check = validateInput($searchFor,'digit');
	$check1 = validateInput($searchWhere,'nospace');
	if($check === true && $check1 === true) {
		// search for given ID
		if($searchWhere === "player") {
			$query = mysql_query("SELECT `playerId`
									FROM `".DB_PREFIX."_Players`
									WHERE `playerId` = '".mysql_escape_string($searchFor)."'");
			if(mysql_num_rows($query) > 0) {
				$result = mysql_fetch_assoc($query);
				header('Location: index.php?mode=admin&amp;task=toolsEditdetails&amp;playerId='.$result['playerId']);
			}
			else {
				$return['msg'] = l('Nothing found');
				$return['status'] = "1";
			}
		}
		elseif($searchWhere === "clan") {
		}
		else {
			$return['msg'] = l('Invalid Input');
			$return['status'] = "1";
		}
	}
	else {
		$return['msg'] = l('Invalid Input');
		$return['status'] = "1";
	}
}

pageHeader(array(l("Admin"),l('Edit Details')), array(l("Admin")=>"index.php?mode=admin",l('Edit Details')=>''));
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
	<h1><?php echo l('Edit Player or Clan Details'); ?></h1>
	<?php
		if(!empty($return)) {
			if($return['status'] === "1") {
				echo '<div class="error">',$return['msg'],'</div>';
			}
			elseif($return['status'] === "2") {
				echo '<div class="success">',$return['msg'],'</div>';
			}
		}
		if($editMode === "player") {
		?>
		<h2><?php echo l('Player'),': ',$playerObj->getParam('name'); ?></h2>
		<form method="post" action="">
			<div style="float: left; margin-right: 20px;">
				<label><?php echo l('Real Name'); ?> :</label>
				<input type="text" name="details[fullName]" value="<?php echo $playerObj->getParam('fullName'); ?>" />
				<label><?php echo l('E-mail Address'); ?> :</label>
				<input type="text" name="details[email]" value="<?php echo $playerObj->getParam('email'); ?>" />
				<label><?php echo l('Homepage'); ?> :</label>
				<input type="text" name="details[homepage]" value="<?php echo $playerObj->getParam('homepage'); ?>" />
				<label><?php echo l('ICQ Number'); ?> :</label>
				<input type="text" name="details[icq]" value="<?php echo $playerObj->getParam('icq'); ?>" />
			</div>
			<div style="float: left">
				<label><?php echo l('MySpace'); ?> :</label>
				<input type="text" name="details[myspace]" value="<?php echo $playerObj->getParam('myspace'); ?>" />
				<label><?php echo l('Facebook'); ?> :</label>
				<input type="text" name="details[facebook]" value="<?php echo $playerObj->getParam('facebook'); ?>" />
				<label><?php echo l('Jabber'); ?> :</label>
				<input type="text" name="details[jabber]" value="<?php echo $playerObj->getParam('jabber'); ?>" />
				<label><?php echo l('Steam Profile'); ?> :</label>
				<input type="text" name="details[steamprofile]" value="<?php echo $playerObj->getParam('steamprofile'); ?>" />
			</div>
			<br style="clear: both" /><br />
			<b><?php echo l('Hide Ranking'); ?> :</b>
			<select name="details[hideranking]">
				<option value="1" <?php if($playerObj->getParam('hideranking') === "1") echo 'selected="1"';?>><?php echo l('Yes'); ?></option>
				<option value="0" <?php if($playerObj->getParam('hideranking') === "0") echo 'selected="1"';?>><?php echo l('No'); ?></option>
			</select><br />
			<br />
			<b><?php echo l('Delete From Clan'); ?> :</b>
			<input type="checkbox" name="details[deletefromclan]" value="1" /><br />
			<br />
			<b><?php echo l('Reset player stats'); ?> :</b>
			<input type="checkbox" name="details[resetstats]" value="1" />
			<p>
				<button type="submit" title=" <?php echo l('Apply'); ?>" name="submit[editPlayer]">
					<?php echo l('Apply'); ?>
				</button>
			</p>
		</form>
		<?php
		}
	?>
	<p>&nbsp;</p>
	<form method="post" action="">
		<?php echo l('You can enter a player or clan ID number directly, or you can search for a player or clan'); ?>.<br />
		<br />
		<?php echo l('Player'); ?> <input type="radio" name="search[what]" value="player" checked="1" />
		<?php echo l('Clan'); ?> <input type="radio" name="search[what]" value="clan" /><br />
		<?php echo l('ID'); ?>: <input type="text" name="search[ID]" value="" />
		<button type="submit" title=" <?php echo l('Edit'); ?>" name="submit[searchForId]">
			<?php echo l('Edit'); ?>
		</button>
	</form>
</div>



<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr valign="top">
	<td width="100%"><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo l('Jump Direct'); ?></b><?php echo $g_options["fontend_normal"]; ?><p>

		<form method="GET" action="index.php">
		<input type="hidden" name="mode" value="admin">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="90%">
				<table width="40%" border="0" cellspacing="0" cellpadding="0">

				<tr valign="top" bgcolor="<?php echo $g_options["table_border"]; ?>">
					<td>
						<table width="100%" border="0" cellspacing="1" cellpadding="4">

						<tr valign="middle">
							<td nowrap width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?><?php echo l('Type'); ?>:<?php echo $g_options["fontend_normal"]; ?></td>
							<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
								<?php
									echo getSelect("task",
										array(
											"toolsEditdetailsPlayer"=>"Player",
											"toolsEditdetailsClan"=>"Clan"
										)
									);
								?>
							<td rowspan="2"  bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><input type="submit" value=" <?php echo l('Edit'); ?> &gt;&gt; " class="submit"></td>
						</tr>

						<tr valign="middle">
							<td nowrap width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?><?php echo l('ID Number'); ?>:<?php echo $g_options["fontend_normal"]; ?></td>
							<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><input type="text" name="id" size=15 maxlength=12 class="textbox"></td>
						</tr>

						</table>
					</td>
				</tr>

				</table></td>
		</tr>

		</table>

		</form><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table><p>
