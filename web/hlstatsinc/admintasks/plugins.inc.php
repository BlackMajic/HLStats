<?php
/**
 * manage the plugins which will be shown in server live view
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

/*
	if ($_POST)
	{
		if ($edlist->update())
			message("success", l("Operation successful"));
		else
			message("warning", $edlist->error());
	}
*/
$return = false;

if(isset($_POST['sub']['saveAddons'])) {
	if(!empty($_POST['rule']) && !empty($_POST['add'])) {
		// update given addons
		foreach($_POST['rule'] as $k=>$v) {
			$v = trim($v);
			if(!empty($v) && isset($_POST['add'][$k])) {
				$query = mysql_query("UPDATE `".DB_PREFIX."_Server_Addons`
										SET `rule` = '".$v."',
											`addon` = '".mysql_escape_string($_POST['add'][$k])."',
											`url` = '".mysql_escape_string($_POST['url'][$k])."'
										WHERE `rule` = '".$k."'");
				if($query === false) {
					$return['status'] = "1";
					$return['msg'] = l('Data could not be saved');
				}
			}
		}
	}

	if($return === false) {
		header('Location: index.php?mode=admin&task=plugins#plugins');
	}
}

$addons = false;
// get the addons from db
$query = mysql_query("SELECT rule,addon,url
						FROM `".DB_PREFIX."_Server_Addons`
						ORDER BY rule ASC");
if(mysql_num_rows($query) > 0) {
	unset($result);
	while($result = mysql_fetch_assoc($query)) {
		$addons[] = $result;
	}
}

pageHeader(array(l("Admin"),l('Server Plugins')), array(l("Admin")=>"index.php?mode=admin",l('Server Plugins')=>''));
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
	<h1><?php echo l('Server Plugins'); ?></h1>
	<p>
		<?php echo l('Here you can define a list of addons (plugins) the HLStats live statistics page will detect'); ?>.<br>
		<?php echo l('When HLStats queries a server for the rules the server will return something like this'); ?>:<br>
		<table border="1" cellspacing="0" cellpadding="4">
			<tr>
				<th><?php echo l('Rule'); ?></th>
				<th><?php echo l('Value'); ?></th>
			</tr>
			<tr>
				<td>mp_footsteps</td>
				<td>1</td>
			</tr>
			<tr>
				<td>sv_timelimit</td>
				<td>30</td>
			</tr>
		</table><br />
		<br />
		
		<?php echo l("Addons usually create a cvar that is publicly available in the rules list. In most cases the cvar that shows the addons existance just shows the version of the addon. You can configure HLStats on this page to then show the proper name of the plugin and it's version on the live statistics page. For example"); ?>:<br />
		<br />
		<table border="1" cellspacing="0" cellpadding="4">
			<tr>
				<th><?php echo l('Rule'); ?></th>
				<th><?php echo l('Value'); ?></th>
				<th><?php echo l('Addon'); ?></th>
				<th><?php echo l('Version'); ?></th>
			</tr>
			<tr>
				<td>cdversion</td>
				<td>4.14</td>
				<td>Cheating Death</td>
				<td>4.14</td>
			</tr>
			<tr>
				<td>hlguard_version</td>
				<td>4.14</td>
				<td>HLGuard</td>
				<td>4.14</td>
			</tr>
		</table><br />
		<br />
		<?php echo l('The value in the table above shows the addon version. To include the version in your proper name of the addon you can use a'); ?> <b>%</b>.<br />
		<?php echo l('If the addon happens to have a home page where more information can be found on the addon, you can put it in as the URL which will be linked to'); ?>.<br>
		<?php echo l('These default addons should help make understanding this feature easier'); ?>.<br>
		<br>
	</p>
	<a name="plugins"></a>
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
	<?php if(!empty($addons)) { ?>
	<form method="post" action="">
		<table cellpadding="2" cellspacing="0" border="1" width="100%">
			<tr>
				<th><?php echo l('Rule'); ?></td>
				<th><?php echo l('Addon'); ?></td>
				<th><?php echo l('URL'); ?></td>
				<th><?php echo l('Delete'); ?></td>
			</tr>
			<?php foreach($addons as $addon) { ?>
			<tr>
				<td>
					<input type="text" name="rule[<?php echo $addon['rule']; ?>]" value="<?php echo $addon['rule']; ?>" />
				</td>
				<td>
					<input type="text" name="add[<?php echo $addon['rule']; ?>]" value="<?php echo $addon['addon']; ?>" />
				</td>
				<td>
					<input type="text" name="url[<?php echo $addon['rule']; ?>]" value="<?php echo $addon['url']; ?>" />
				</td>
				<td>
					<input type="checkbox" name="del[<?php echo $addon['rule']; ?>]" value="yes" />
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td>
					<?php echo l('new'); ?>: <br/>
					<input type="text" name="newrule" value="" />
				</td>
				<td>
					<input type="text" name="newadd" value="" />
				</td>
				<td colspan="2">
					<input type="text" name="newurl" value="" />
				</td>
			</tr>
			<tr>
				<td colspan="4" align="right">
					<button type="submit" title="<?php echo l('Save'); ?>" name="sub[saveAddons]">
						<?php echo l('Save'); ?>
					</button>
				</td>
			</tr>
		</table>
	</form>
	<?php } ?>
</div>
