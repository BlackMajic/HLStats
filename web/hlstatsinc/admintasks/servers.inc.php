<?php
/**
 * manage the server for a game
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

$gc = false;
$servers = false;
// get the game, without it we can no do anyting
if(isset($_GET['gc'])) {
	$gc = trim($_GET['gc']);
	$check = validateInput($gc,'nospace');
	if($check === true) {

		// update
		if(!empty($_POST['server']) && !empty($_POST['port'])) {
			// update given patterns
			foreach($_POST['server'] as $k=>$v) {
				$v = trim($v);
				if(!empty($v) && isset($_POST['server'][$k])) {
					$query = mysql_query("UPDATE `".DB_PREFIX."_Servers`
											SET `address` = '".$v."',
												`port` = '".mysql_escape_string(trim($_POST['port'][$k]))."',
												`name` = '".mysql_escape_string(trim($_POST['name'][$k]))."',
												`port` = '".mysql_escape_string(trim($_POST['port'][$k]))."',
												`port` = '".mysql_escape_string(trim($_POST['port'][$k]))."',
												`port` = '".mysql_escape_string(trim($_POST['port'][$k]))."',
												`port` = '".mysql_escape_string(trim($_POST['port'][$k]))."'
											WHERE `serverId` = '".$k."'");
					if($query === false) {
						$return['status'] = "1";
						$return['msg'] = l('Data could not be saved');
					}
				}
			}
		}

		// load the server
		$query = mysql_query("SELECT s.serverId, s.address, s.port,
								s.name AS serverName,
								s.publicaddress, s.statusurl,
								s.rcon_password, s.defaultMap,
								g.name AS gameName
							FROM `".DB_PREFIX."_Servers` AS s
							LEFT JOIN `".DB_PREFIX."_Games` AS g ON g.code = s.game
							WHERE s.game = '".mysql_escape_string($gc)."'
							ORDER BY address ASC, port ASC");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$servers[] = $result;
			}
		}
	}
}
else {
	exit('Missing game code');
}

$rcol = "row-dark";

pageHeader(array(l("Admin"),l('Servers')), array(l("Admin")=>"index.php?mode=admin",l('Servers')=>''));
?>

<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="index.php?mode=admin&task=gameoverview&code=<?php echo $gc; ?>"><?php echo l('Back to game overview'); ?></a>
			</li>
			<li>
				<a href="index.php?mode=admin"><?php echo l('Back to admin overview'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l('Servers for '); ?>: <?php echo $servers[0]['gameName']; ?></h1>
	<p>
		<?php echo l('Enter the addresses of all servers that you want to accept data from'); ?>
	</p>
	<p>
		<?php echo l('HLStats can use Rcon to give feedback to users when they'); ?>
		<a href="index.php?mode=help#set"><?php echo l('update their profile'); ?></a>
		<?php echo l('if you enable Rcon support in hlstats.conf and specify an Rcon Password for each server'); ?>.
	</p>
	<p>
		<?php echo l('The Default map is used to sepecify the map if HLStats is unable to determine the map'); ?>.
	</p>
	<?php if(!empty($servers)) { ?>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr>
			<th>&nbsp;</th>
			<th><?php echo l('IP Address'); ?> *</th>
			<th><?php echo l('Port'); ?> *</th>
			<th><?php echo l('Server Name'); ?> *</th>
			<th><?php echo l('Delete'); ?></th>
		</tr>
	<?php foreach($servers as $s) { ?>
		<tr>
			<td class="<?php echo toggleRowClass($rcol); ?>"><img src="hlstatsimg/server.gif" alt="<?php echo l('Server'); ?>" /></td>
			<td>
				<input size="10" type="text" name="server[<?php echo $s['serverId']; ?>]" value="<?php echo $s['address']; ?>" />
			</td>
			<td class="<?php echo ($rcol); ?>">
				<input size="5" type="text" name="port[<?php echo $s['serverId']; ?>]" value="<?php echo $s['port']; ?>" />
			</td>
			<td class="<?php echo ($rcol); ?>">
				<input size="25" type="text" name="name[<?php echo $s['serverId']; ?>]" value="<?php echo $s['serverName']; ?>" />
			</td>
			<td align="center" class="<?php echo ($rcol); ?>">
				<input type="checkbox" name="del[<?php echo $s['serverId']; ?>]" value="yes" />
			</td>
		</tr>
		<tr>
			<td class="<?php echo ($rcol); ?>">&nbsp;</td>
			<td colspan="3" class="small <?php echo ($rcol); ?>">
				<b><?php echo l('Rcon Password'); ?> :</b>
				<input size="10"  type="text" name="rcon[<?php echo $s['serverId']; ?>]" value="<?php echo $s['rcon_password']; ?>" />&nbsp;
				&nbsp;
				<b><?php echo l('Default Server Map'); ?> :</b>
				<input size="10"  type="text" name="map[<?php echo $s['serverId']; ?>]" value="<?php echo $s['defaultMap']; ?>" />
			</td>
			<td class="<?php echo ($rcol); ?>">&nbsp;</td>
		</tr>
	<?php } ?>
		<tr>
			<td class="<?php echo toggleRowClass($rcol); ?>"><?php echo l('new'); ?></td>
			<td class="<?php echo ($rcol); ?>">
				<input size="10" type="text" name="newIP" value="" />
			</td>
			<td class="<?php echo ($rcol); ?>">
				<input size="5" type="text" name="newport" value="" />
			</td>
			<td class="<?php echo ($rcol); ?>">
				<input size="25" type="text" name="newname" value="" />
			</td>
		</tr>
		<tr>
			<td class="<?php echo ($rcol); ?>">&nbsp;</td>
			<td colspan="3" class="small <?php echo ($rcol); ?>">
				<b><?php echo l('Rcon Password'); ?> :</b>
				<input size="10"  type="text" name="newrcon" value="" />&nbsp;
				&nbsp;
				<b><?php echo l('Default Server Map'); ?> :</b>
				<input size="10"  type="text" name="newmap" value="" />
			</td>
			<td class="<?php echo ($rcol); ?>">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5" align="right">
				<button type="submit" name="sub[saveServer]" title="<?php echo l('Save'); ?>">
					<?php echo l('Save'); ?>
				</button>
			</td>
		</tr>
	</table>
	<?php } ?>
</div>
