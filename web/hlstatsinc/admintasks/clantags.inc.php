<?php
/**
 * manage the clan tags
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
if(isset($_POST['sub']['patterns'])) {

	if(!empty($_POST['del'])) {
		foreach($_POST['del'] as $k=>$v) {
			$query = mysql_query("DELETE FROM `".DB_PREFIX."_ClanTags`
									WHERE `id` = '".mysql_escape_string($k)."'");
			unset($_POST['pat'][$k]);
		}
	}

	if(!empty($_POST['pat']) && !empty($_POST['sel'])) {
		// update given patterns
		foreach($_POST['pat'] as $k=>$v) {
			$v = trim($v);
			if(!empty($v) && isset($_POST['sel'][$k])) {
				$query = mysql_query("UPDATE `".DB_PREFIX."_ClanTags`
										SET `pattern` = '".$v."',
											`position` = '".mysql_escape_string($_POST['sel'][$k])."'
										WHERE `id` = '".$k."'");
				if($query === false) {
					$return['status'] = "1";
					$return['msg'] = l('Data could not be saved');
				}
			}
		}
	}

	if(isset($_POST['newpat'])) {
		$newOne = trim($_POST['newpat']);
		if(!empty($newOne) && !empty($_POST['newsel'])) {
			$query = mysql_query("INSERT INTO `".DB_PREFIX."_ClanTags`
									SET `pattern` = '".mysql_escape_string($newOne)."',
										`position` = '".mysql_escape_string($_POST['newsel'])."'");
			if($query === false) {
				$return['status'] = "1";
				$return['msg'] = l('Data could not be saved');
			}
		}
	}

	if($return === false) {
		header('Location: index.php?mode=admin&task=clantags#tags');
	}

}

$patterns = false;
// get the patterns
$query = mysql_query("SELECT id,pattern,position
		FROM `".DB_PREFIX."_ClanTags`
		ORDER BY position, pattern, id");
if(mysql_num_rows($query) > 0) {
	while($result = mysql_fetch_assoc($query)) {
		$patterns[] = $result;
	}
}

pageHeader(array(l("Admin"),l('Clan Tag Patterns')), array(l("Admin")=>"index.php?mode=admin",l('Clan Tag Patterns')=>''));
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
	<h1><?php echo l('Clan Tag Patterns'); ?></h1>
	<p>
		<?php echo l("Here you can define the patterns used to determine what clan a player is in. These patterns are applied to players' names when they connect or change name"); ?>.
	</p>
	<p>
		<h2><?php echo l("Special characters in the pattern"); ?></h2>
		<table border="0" cellspacing="0" cellpadding="4">
			<tr>
				<th><?php echo l('Character'); ?></th>
				<th><?php echo l('Description'); ?></th>
			</tr>

			<tr>
				<td><tt>A</tt></td>
				<td><?php echo l('Matches one character. Character is required'); ?></td>
			</tr>

			<tr>
				<td><tt>X</tt></td>
				<td><?php echo l('Matches zero or one characters. Character is optional'); ?></td>
			</tr>

			<tr>
				<td><tt>a</tt></td>
				<td><?php echo l('Matches literal A or a'); ?></td>
			</tr>

			<tr>
				<td><tt>x</tt></td>
				<td><?php echo l('Matches literal X or x'); ?></td>
			</tr>
		</table>
	</p>
	<p>
		<h2><?php echo l('Example patterns'); ?></h2>
		<table border="0" cellspacing="0" cellpadding="4">
			<tr>
				<th><?php echo l('Pattern'); ?></th>
				<th><?php echo l('Description'); ?></th>
				<th><?php echo l('Example'); ?></th>
			</tr>
			<tr>
				<td><tt>[AXXXXX]</tt></td>
				<td><?php echo l('Matches 1 to 6 characters inside square braces'); ?></td>
				<td><tt>[ZOOM]Player</tt></td>
			</tr>

			<tr>
				<td><tt>{AAXX}</tt></td>
				<td><?php echo l('Matches 2 to 4 characters inside curly braces'); ?></td>
				<td><tt>{S3G}Player</tt></td>
			</tr>

			<tr>
				<td><tt>rex>></tt></td>
				<td><?php echo l('Matches the string rex>>, REX>>, etc.'); ?></td>
				<td><tt>REX>>Tyranno</tt></td>
			</tr>
		</table>
	</p>
	<p>
		<?php echo l('Avoid adding patterns to the database that are too generic. Always ensure you have at least one literal (non-special) character in the pattern -- for example if you were to add the pattern "AXXA", it would match any player with 2 or more letters in their name'); ?>!<br />
		<br />
		<?php echo l("The Match Position field sets which end of the player's name the clan tag is allowed to appear"); ?>.
	</p>
	<a name="tags"></a>
	<?php
		if(!empty($return)) {
			if($return['status'] === "1") {
				echo '<div class="error">',$return['msg'],'</div>';
			}
			elseif($return['status'] === "2") {
				echo '<div class="success">',$return['msg'],'</div>';
			}
		}
	?>
	<?php if(!empty($patterns)) { ?>
	<form method="post" action="">
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr>
			<th><?php echo l('Pattern'); ?></th>
			<th><?php echo l('Match Position'); ?></th>
			<th><?php echo l('Delete'); ?></th>
		</tr>
	<?php
			foreach($patterns as $pat) {
				echo '<tr>';

				echo '<td><input type="text" size="30" name="pat[',$pat['id'],']" value="',$pat['pattern'],'"/></td>';

				echo '<td>';
				echo '<select name="sel[',$pat['id'],']">';
					$selected = '';
					if($pat['position'] == "EITHER") $selected = 'selected="1"';
					echo '<option ',$selected,' value="EITHER">',l('EITHER'),'</option>';
					$selected = '';
					if($pat['position'] == "START") $selected = 'selected="1"';
					echo '<option ',$selected,' value="START">',l('START'),'</option>';
					$selected = '';
					if($pat['position'] == "END") $selected = 'selected="1"';
					echo '<option ',$selected,' value="END">',l('END'),'</option>';
				echo '</select>';
				echo '</td>';

				echo '<td><input type="checkbox" name="del[',$pat['id'],']" value="yes" /></td>';

				echo '</tr>';
			}
	?>
		<tr>
			<td>
				<?php echo l('new'); ?> <input type="text" size="30" name="newpat" value="" />
			</td>
			<td colspan="2">
				<select name="newsel">
					<option value="EITHER"><?php echo l('EITHER'); ?></option>
					<option  value="START"><?php echo l('START'); ?></option>
					<option  value="END"><?php echo l('END'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<button type="submit" name="sub[patterns]" title="<?php echo l('Save'); ?>">
					<?php echo l('Save'); ?>
				</button>
			</td>
		</tr>
	</table>
	</form>
	<?php } ?>
</div>
