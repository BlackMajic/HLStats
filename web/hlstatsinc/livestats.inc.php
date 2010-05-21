<?php
/**
 * livestats game overview
 * makes a rcon status call to the game server
 * @package HLStats
 * @author Johannes 'Banana' Keßler
 * @copyright Johannes 'Banana' Keßler
 * @todo rcon stats call with rcon password
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

// Live Stats
// The binary functions need to be included
// Along with the HL Query functions

$serverId = '';
if(!empty($_GET["server"])) {
	if(validateInput($_GET["server"],'digit') === true) {
		$serverId = $_GET["server"];
	}
	else {
		die("No server ID specified.");
	}
}


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

include('hlstatsinc/binary_funcs.inc.php');
include('hlstatsinc/hlquery_funcs.inc.php');

$query = mysql_query("SELECT s.serverId, s.name, s.address,
			s.port, s.publicaddress, s.game, s.rcon_password,
			g.name gamename
		FROM ".DB_PREFIX."_Servers AS s
		LEFT JOIN ".DB_PREFIX."_Games AS g ON s.game=g.code
		WHERE serverId = '".mysql_escape_string($serverId)."'");
if (mysql_num_rows($query) != 1) {
	die("Invalid or no server specified.");
}
else {
	$server = mysql_fetch_assoc($query);
}

pageHeader(
	array(l('Server Statistics'), $server['name']),
	array($server['gamename'] => 'index.php?game=' . $server['game'],
		l('Server Statistics') => 'index.php?game=' . $server['game'],
		$server['name'] => ''
	)
);

if(!empty($server['publicaddress'])) {
	// Port maybe different
	$temp = explode(':', $server['publicaddress']);
	$server_ip = $server['address'];
	if (isset($temp[1])) {
		$server_port = $temp[1];
	}
	else {
		$server_port = $server['port'];
	}
}
else {
	$server_ip = $server['address'];
	$server_port = $server['port'];
}
$server_rcon = $server['rcon_password'];

$server_hltv = array();
$server_players = array();

// Get info
if (!$server_details = Source_A2S_Info($server_ip, $server_port)) {
	die(l("The details for this server couldn't be retrieved, this maybe because the server is currently unavailable."));
}

$server['source'] = 0;
// check if we have a source server or not
if ($server_details['gametype'] == 73) {
	$server['source'] = 1;
	$server_details['address'] = $server_ip.':'.$server_port;
}


// Get rcon challenge
$query_challenge = Source_A2S_GetChallenge($server_ip, $server_port);

// Get packets with challenge number
// strange as of 29.10.2008 only the Source_A2S_Rules returns a challenge which
// is used and needed in Source_A2S_Player
$server_rules = Source_A2S_Rules($server_ip, $server_port, $query_challenge);
$server_players = Source_A2S_Player($server_ip, $server_port, $query_challenge);

$server_details = Format_Info_Array($server_details);
// If HLStats currently stores the rcon, might as well try to get more data from a HL status

// since the rcon is broken we deactivate this.... 31.10.2008 banana
// @todo: to complete this and use eg. steamcondenser
$server_rcon  = false;
$server_status = false;
if ($server_rcon) {
	if ($server['source'] == 1) {
		$server_players_tmp = $server_players;
		$server_status = Source_Rcon($server_ip, $server_port, $server_rcon, 'status');
	}
	else {
		$server_status = HalfLife_Rcon($server_ip, $server_port, $server_rcon, 'status', $query_challenge);
	}

	if ($server_status) {
		# Rcon worked
		$server_players = array();
		$server_hltv = array();
		Parse_HL_Status($server_status, $server['source'], $server_players, $server_hltv);

		$player_columns[] = array('column' => 'id', 'name' => ' ID', 'align' => 'left', 'width' => '25');
		$player_columns[] = array('column' => 'name', 'name' => 'Name', 'align' => 'left', 'width' => '');
		$player_columns[] = array('column' => 'uid', 'name' => 'Unique ID', 'align' => 'left', 'width' => '125');
		$player_columns[] = array('column' => 'ping', 'name' => 'Ping', 'align' => 'right', 'width' => '35');
		$player_columns[] = array('column' => 'frags', 'name' => 'Frags', 'align' => 'right', 'width' => '30');
		$player_columns[] = array('column' => 'loss', 'name' => 'Loss', 'align' => 'right', 'width' => '30');
		$player_columns[] = array('column' => 'time', 'name' => 'Connected', 'align' => 'right', 'width' => '75');
	}
}

// If rcon failed then $server_status is FALSE and if we don't have rcon
// it won't exist
if ($server_status === false) {
	$player_columns[] = array('column' => 'index', 'name' => 'ID', 'align' => 'left', 'width' => '25');
	$player_columns[] = array('column' => 'name', 'name' => 'Name', 'align' => 'left', 'width' => '');
	$player_columns[] = array('column' => 'frags', 'name' => 'Frags', 'align' => 'right', 'width' => '40');
	$player_columns[] = array('column' => 'time', 'name' => 'Connected', 'align' => 'right', 'width' => '75');
}

$server_details['hltvcount'] = count($server_hltv);

$server_details['players_real'] = $server_details['numplayers'];
$server_details['players_real'] -= $server_details['numbots'];
$server_details['players_real'] -= $server_details['hltvcount'];

$server_details['players_connecting'] = $server_details['numplayers'];
$server_details['players_connecting'] -= count($server_players);
$server_details['players_connecting'] -= $server_details['numbots'];
$server_details['players_connecting'] -= $server_details['hltvcount'];

// map image
$mapImage = $g_options['imgdir'].'/maps/'.strtolower($server['game']).'/'.strtolower($server_details['map']).'.jpg';
if(!file_exists($mapImage)) {
	$mapImage = $g_options['imgdir'].'/noimage.jpg';
}

// server details
if (isset($server_rules['cm_nextmap']))
	$server_details['nextmap'] = $server_rules['cm_nextmap'];
elseif (isset($server_rules['amx_nextmap']))
	$server_details['nextmap'] = $server_rules['amx_nextmap'];
elseif (isset($server_rules['mani_nextmap']))
	$server_details['nextmap'] = $server_rules['mani_nextmap'];

// Some unfortunate games like CS don't usually give the map timeleft
// I wonder if some plugin can yet again provide a use here...
// Generally the plugin version is more reliable so that is the highest priority to use
if (isset($server_rules['amx_timeleft']))
	$server_details['timeleft'] = $server_rules['amx_timeleft'];
elseif (isset($server_rules['cm_timeleft']))
	$server_details['timeleft'] = $server_rules['cm_timeleft'];
elseif (isset($server_rules['mani_timeleft']))
	$server_details['timeleft'] = $server_rules['mani_timeleft'];
elseif (isset($server_rules['mp_timeleft']))
	$server_details['timeleft'] = sprintf('%02u:%02u', ($server_rules['mp_timeleft'] / 60), ($server_rules['mp_timeleft'] % 60));

// Load our plugin list
$server_details['addon_count'] = 0;
$query = mysql_query("SELECT * FROM ".DB_PREFIX."_Server_Addons");
while ($addon_list = mysql_fetch_assoc($query)) {
	$server_addon[$addon_list['rule']] = array('addon' => $addon_list['addon'], 'url' => $addon_list['url']);
}

?>

<div id="sidebar" >
	<h1><?php echo l('Server details'); ?></h1>
	<br />
	<img src="<?php echo $mapImage; ?>" width="218" height="164" title="<?php echo $server_details['map']; ?>" alt="<?php echo $server_details['map']; ?>" />
	<?php echo l('Address'); ?>: <?php echo $server_details['address']; ?><br>
	<?php echo l('Server Type'); ?>: <?php echo $server_details['serveros']; ?>, <?php echo $server_details['servertype']; ?><br>
	<?php echo l('Map'); ?>: <a href="index.php?mode=mapinfo&amp;map=<?php echo $server_details['map']; ?>&amp;game=<?php echo $server['game']; ?>"><?php echo $server_details['map']; ?></a><br>
	<?php
		if (isset($server_details['nextmap'])) {
			echo l('Nextmap'),': <a href="index.php?mode=mapinfo&amp;map='.$server_details['nextmap'].'&amp;game='.$server['game'].'">'.$server_details['nextmap'].'</a><br>';
		}

		// Are there any time limits or frag limits?
		if (isset($server_details['timeleft'])) {
			echo l('Timeleft'),': '.$server_details['timeleft'];
			if(isset($server_rules['mp_timelimit'])) {
				echo ' ('.sprintf('%02u:%02u', $server_rules['mp_timelimit'], 0).')';
			}
			echo '<br>';
		}
		if (!empty($server_rules['mp_fraglimit'])) {
			echo l('Fragsleft'),': '.$server_rules['mp_fragsleft'];
			if (isset($server_rules['mp_fragslimit'])) {
				echo '('.$server_rules['mp_fraglimit'].')';
			}
			echo '<br>';
		}
	?>
	<?php echo l('Password'); ?>: <?php echo $server_details['password']; ?><br>
	<?php echo l('Players'); ?>: <?php echo $server_details['players_real']; ?>/<?php echo $server_details['maxplayers'];?><br>
	<?php
		if (!empty($server_details['botcount'])) { // Don't show this information if there are no bots
			echo l('Bots'),' : ',$server_details['numbots'],'/',$server_details['maxplayers'],'<br>';
		}
		if (!empty($server_details['hltvcount'])) { // Don't show this information if there is no HLTV
			echo l('HLTV'),' : ',$server_details['hltvcount'],'/',$server_details['maxplayers'],'<br>';
		}
		if (!empty($server_details['players_connecting'])) { // Don't show this information if there are no players connecting
			echo l('Connecting'),' : ',$server_details['players_connecting'],'/',$server_details['maxplayers'],'<br>';
		}
		echo l('Valve Anti-Cheat'),' : ',$server_details['secure'],'<br>';

		if(!empty($server_addon)) {
			echo l('Server Addons').'<ul>';
			foreach ($server_rules as $key => $value) {
				if (isset($server_addon[$key])) {
					if ($server_addon[$key]['url']) {
						echo '<li><a href="'.$server_addon[$key]['url'].'" target="_blank">'.str_replace('%', $value, $server_addon[$key]['addon']).'</a></li>';
					}
					else {
						echo '<li>'.str_replace('%', $value, $server_addon[$key]['addon']).'</li>';
					}
				}
			}
			echo '</ul>';
		}
	?>
</div>
<div id="main">
	<h1><?php echo htmlentities($server_details['hostname'], ENT_COMPAT, "UTF-8"); ?></h1>
	<p>
		<a href="index.php?game=<?php echo $server['game']; ?>"><?php echo $server_details['gamedesc'];?></a>
	</p>
	<?php
		# Ok we have an array of players
		# and we have an array of columns
		# So lets dynamically create something
		$totalfrags = 0;
		$totalping = 0;
		$totaltime = 0;
		$nonbots = 0;
		if(!empty($server_players)) { ?>
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
			<tr>
				<th width="20">&nbsp;</th>
				<th><?php echo l('Name'); ?></th>
				<th><?php echo l('Frags'); ?></th>
				<th><?php echo l('Connected'); ?></th>
			</tr>
			<?php
			foreach($server_players as $k=>$p) {
				# figure out if the player is a bot
				# HLTV and rcon is not really a bot we will have to treat it like one.
				# However, HLTV shouldn't even show up at this point
				$is_bot = 0;
				$is_rcon = 0;
				if (isset($p['uid'])) {
					if ('UNKNOWN' == $p['uid'])
						$is_bot = 1;
					elseif ('BOT' == $p['uid'])
						$is_bot = 1;
					elseif ('HLTV' == $p['uid'])
						$is_bot = 1;
					elseif ('rcon' == $p['uid'])
						$is_rcon = 1;
					elseif ('0' == $p['uid'])
						$is_bot = 1;
					elseif ('0' == $p['ping'])
						$is_bot = 1;
				}

				$img = "player.gif";
				if($is_bot === 1 || $is_rcon === 1) {
					$img = "server.gif";
				}

				echo '<tr>';

				echo '<td><img src="',$g_options['imgdir'],$img,'" alt="" width="16"/></td>';
				echo '<td><a href="index.php?mode=search&amp;game='.$server['game'].'&amp;q='.urlencode(makeSavePlayerName($p['name'])).'">',makeSavePlayerName($p['name']),'</a></td>';
				echo '<td>',$p['frags'],'</td>';
				echo '<td>',Format_Time($p['time']),'</td>';

				echo '</tr>';
			}
			?>
		</table>
	<?php } ?>
</div>
