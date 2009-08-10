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

	// Live Stats
	// The binary functions need to be included
	// Along with the HL Query functions

$serverId = '';
if(!empty($_GET["server"])) {
	if(validateInput($_GET["server"],'digit') === true) {
		$serverId = $_GET["server"];
	}
	else {
		error("No server ID specified.");
	}
}


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

include(INCLUDE_PATH.'/binary_funcs.inc.php');
include(INCLUDE_PATH.'/hlquery_funcs.inc.php');

$query = mysql_query("
		SELECT
			s.serverId,
			s.name,
			s.address,
			s.port,
			s.publicaddress,
			s.game,
			s.rcon_password,
			g.name gamename
		FROM
			".DB_PREFIX."_Servers s
		LEFT JOIN
			".DB_PREFIX."_Games g
		ON
			s.game=g.code
		WHERE
			serverId=".$serverId."
			");
if (mysql_num_rows($query) != 1) {
	error("Invalid or no server specified.");
}
else {
	$server = mysql_fetch_assoc($query);
}

pageHeader(
	array('Server Statistics', $server['name']),
	array($server['gamename'] => $g_options['scripturl'] . '?game=' . $server['game'],
		l('Server Statistics') => $g_options['scripturl'] . '?game=' . $server['game'],
		$server['name'] => ''
	)
);

if ($server['publicaddress']) {
	# Port maybe different
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

# Get info
if (!$server_details = Source_A2S_Info($server_ip, $server_port)) {
	error(l("The details for this server couldn't be retrieved, this maybe because the server is currently unavailable."));
}

if ($server_details['gametype'] == 73)
{
	$server['source'] = 1;
	$server_details['address'] = $server_ip.':'.$server_port;
}
else {
	$server['source'] = 0;
}


# Get challenge
$query_challenge = Source_A2S_GetChallenge($server_ip, $server_port);

# Get packets with challenge number
# strange as of 29.10.2008 only the Source_A2S_Rules returns a challenge which
# is used and needed in Source_A2S_Player (banana)
$server_rules = Source_A2S_Rules($server_ip, $server_port, &$query_challenge);
$server_players = Source_A2S_Player($server_ip, $server_port, &$query_challenge);

$server_details = Format_Info_Array($server_details);
# If HLStats currently stores the rcon, might as well try to get more data from a HL status

// since the rcon is broken we deaktivate this.... 31.10.2008 banana
$server_rcon  = false;
$server_status = false;
if ($server_rcon) {
	if ($server['source'] == 1) {
		$server_players_tmp = $server_players;
		$server_status = Source_Rcon($server_ip, $server_port, $server_rcon, 'status');
	}
	else {
		$server_status = HalfLife_Rcon($server_ip, $server_port, $server_rcon, 'status', &$query_challenge);
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

# If rcon failed then $server_status is FALSE and if we don't have rcon
# it won't exist
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
$server_details['players_connecting'] -= $server_details['numbots'];
$server_details['players_connecting'] -= count($server_players);
$server_details['players_connecting'] -= $server_details['hltvcount'];
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $g_options['table_border']; ?>">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="1" cellpadding="2">
				<tr bgcolor="<?php echo $g_options['table_bgcolor1']; ?>">
					<td style="padding: 0px;" width="208" height="163"><img src="<?php
$image = getImage('/maps/'.strtolower($server['game']).'/'.strtolower($server_details['map']));
if ($image) {
	echo $image['url'];
}
else {
	echo $g_options['imgdir'].'/noimage.gif';
}
?>" width="218" height="164" alt="<?php echo $server_details['map']; ?>"></td>
					<td rowspan="2" valign="top" align="center" class="fontNormal">
						<b><?php echo htmlentities($server_details['hostname'], ENT_COMPAT, "UTF-8"); ?></b><br>
						<a href="<?php echo $g_options['scripturl']; ?>?game=<?php echo $server['game']; ?>"><?php echo $server_details['gamedesc'];?></a><br><br>
<?php
# Ok we have an array of players
# and we have an array of columns
# So lets dynamically create something
if (count($server_players) > 0)
{
	# If we have players, lets create the table
?>
						<table border="0" width="99%" cellpadding="0">
							<tr>
								<th width="16"></th>
<?php
	foreach ($player_columns as $column) {
?>
								<th align="<?php echo $column['align']; ?>" width="<?php echo $column['width']; ?>"><?php echo $column['name']; ?></th>
<?php
	}
?>
							</tr>
<?php
	$totalfrags = 0;
	$totalping = 0;
	$totaltime = 0;
	$nonbots = 0;
	foreach ($server_players as $key => $player) {
?>
							<tr>
<?php
		# figure out if the player is a bot
		# HLTV and rcon is not really a bot we will have to treat it like one.
		# However, HLTV shouldn't even show up at this point
		$is_bot = 0;
		$is_rcon = 0;
		if (isset($player['uid'])) {
			if ('UNKNOWN' == $player['uid'])
				$is_bot = 1;
			elseif ('BOT' == $player['uid'])
				$is_bot = 1;
			elseif ('HLTV' == $player['uid'])
				$is_bot = 1;
			elseif ('rcon' == $player['uid'])
				$is_rcon = 1;
			elseif ('0' == $player['uid'])
				$is_bot = 1;
			elseif ('0' == $player['ping'])
				$is_bot = 1;
		}

		if ($is_bot === 1 && $is_rcon === 1)
		{
?>
								<td><img src="<?php echo $g_options['imgdir']; ?>/server.gif" alt="Bot"></td>
<?php
			$searchFor = 'BOT:'.md5($player['name'].$server_ip.':'.$server_port);
			$searchType = 'uniqueid';
		}
		else
		{
			# I'm a real boy
			$nonbots++;
?>
								<td><img src="<?php echo $g_options['imgdir']; ?>/player.gif" alt="Player"></td>
<?php
			if (isset($player['uid']) && MODE == 'Normal') {
				$searchFor  = $player['uid'];
				$searchType = 'uniqueid';
			} else {
				$searchFor = $player['name'];
				$searchType = 'player';
			}
		}
		foreach ($player_columns as $column) {

			if($is_bot === 0 && $is_rcon === 0) {
				# Special columns
				# Name = a link
				#  If we have UID - link to UID, else player
				# Loss = add a %
				# Connected = Format it
				if ('name' == $column['column'])
					$temp = '<a href="'.$g_options['scripturl'].'?mode=search&amp;q='.HTMLEntities(URLEncode($searchFor), ENT_QUOTES, 'UTF-8').'&amp;st='.$searchType.'&amp;game='.$server['game'].'">'.str_replace(' ', '&nbsp;', HTMLEntities($player['name'], ENT_QUOTES, 'UTF-8')).'</a>';
				elseif ('loss' == $column['column'])
					$temp = $player['loss'].'%';
				elseif ('time' == $column['column'])
					$temp = Format_Time($player['time']);
				elseif ('frags' == $column['column']) {
					$temp = $player[$column['column']];

					if ($server_status) {
						if ($server['source'])
							$temp = $server_players_tmp[$key]['frags'];
					}
					$totalfrags += $temp;
				}
				else
					$temp = $player[$column['column']];
			}
			else {
				$temp = "";
			}
?>
								<td align="<?php echo $column['align']; ?>"><?php echo $temp; ?></td>
<?php
		}
?>
							</tr>
<?php
		if (!$is_bot || !$is_rcon) {
			if(!empty($player['ping'])) $totalping += $player['ping'];
			if(!empty($player['time'])) $totaltime += $player['time'];
		}
	}
	# +1 for the special icon column
	$colspan = count($player_columns) + 1;
?>
							<tr>
								<td colspan="<?php echo $colspan; ?>" nowrap>&nbsp;</td>
							</tr>
							<tr>
								<th align="right" colspan="<?php echo $colspan - 1; ?>" nowrap><?php echo l('Total Time'); ?></th>
								<th align="right"><?php echo Format_Time($totaltime); ?></th>
							</tr>
							<tr>
								<th align="right" colspan="<?php echo $colspan - 1; ?>" nowrap><?php echo l('Total Frags'); ?></th>
								<th align="right"><?php echo $totalfrags; ?></th>
							</tr>
<?php
	# Get the average ping (don't include bots!)
	if (!empty($totalping))
	{
?>
							<tr>
								<th align="right" colspan="<?php echo $colspan - 1; ?>" nowrap><?php echo l('Average Ping'); ?></th>
								<th align="right"><?php echo round($totalping/$nonbots, 0); ?></th>
							</tr>
<?php
	}
?>
						</table>
<?php
}

if (!empty($server_hltv))
{
?>
						<br><br>
						<table border="0" width="99%">
							<tr>
								<th align="left" width="16"></th>
								<th align="left"><?php echo l('Name'); ?></th>
								<th align="left" width="125">IP</th>
								<th align="right" width="40"><?php echo l('Delay'); ?></th>
								<th align="right" width="60"><?php echo l('Viewers'); ?></th>
								<th align="right" width="60"><?php echo l('Capacity'); ?></th>
								<th align="right" width="75"><?php echo l('Connected'); ?></th>
							</tr>
<?php
	foreach ($server_hltv as $hltv)
	{
?>
							<tr>
								<td><img src="<?php echo $g_options['imgdir'];?>/hltv.gif" alt="HLTV"></td>
								<td align="left"><?php echo $hltv['name'];?></td>
								<td align="right"><?php echo $hltv['ip'];?></td>
								<td align="right"><?php echo $hltv['delay'];?></td>
								<td align="right"><?php echo $hltv['viewers'];?></td>
								<td align="right"><?php echo $hltv['capacity'];?></td>
								<td align="right"><?php echo $hltv['time'];?></td>
							</tr>
<?php
	}
?>
						</table>
<?php
}
if ($server_details['players_connecting'] > 0)
{
	echo l('There are currently'),' <b>'.$server_details['players_connecting'].'</b>', l('player(s) connecting to the server.');
}
?>
					</td>
				</tr>
				<tr bgcolor="<?php echo $g_options['table_bgcolor1']; ?>">
					<td valign="top" class="fontNormal">
<?php
# For our main server information area we can grab some
# bits of information from the rules

# If ClanMod or AMX mod is installed we may know what
# the next map is
if (isset($server_rules['cm_nextmap']))
	$server_details['nextmap'] = $server_rules['cm_nextmap'];
elseif (isset($server_rules['amx_nextmap']))
	$server_details['nextmap'] = $server_rules['amx_nextmap'];
elseif (isset($server_rules['mani_nextmap']))
	$server_details['nextmap'] = $server_rules['mani_nextmap'];

# Some unfortunate games like CS don't usually give the map timeleft
# I wonder if some plugin can yet again provide a use here...
# Generally the plugin version is more reliable so that is the highest priority to use
if (isset($server_rules['amx_timeleft']))
	$server_details['timeleft'] = $server_rules['amx_timeleft'];
elseif (isset($server_rules['cm_timeleft']))
	$server_details['timeleft'] = $server_rules['cm_timeleft'];
elseif (isset($server_rules['mani_timeleft']))
	$server_details['timeleft'] = $server_rules['mani_timeleft'];
elseif (isset($server_rules['mp_timeleft']))
	$server_details['timeleft'] = sprintf('%02u:%02u', ($server_rules['mp_timeleft'] / 60), ($server_rules['mp_timeleft'] % 60));
?>
						<?php echo l('Address'); ?>: <?php echo $server_details['address']; ?><br>
						<?php echo l('Server Type'); ?>: <?php echo $server_details['serveros']; ?>, <?php echo $server_details['servertype']; ?><br>
						<?php echo l('Map'); ?>: <a href="<?php echo $g_options['scripturl']; ?>?mode=mapinfo&amp;map=<?php echo $server_details['map']; ?>&amp;game=<?php echo $server['game']; ?>"><?php echo $server_details['map']; ?></a><br>
<?php
if (isset($server_details['nextmap']))
	echo l('Nextmap'),': <a href="'.$g_options['scripturl'].'?mode=mapinfo&amp;map='.$server_details['nextmap'].'&amp;game='.$server['game'].'">'.$server_details['nextmap'].'</a><br>';

# Are there any time limits or frag limits?
if (isset($server_details['timeleft']))
{
	echo l('Timeleft'),': '.$server_details['timeleft'];
	if(isset($server_rules['mp_timelimit']))
		echo ' ('.sprintf('%02u:%02u', $server_rules['mp_timelimit'], 0).')';
	echo '<br>';
}
if (!empty($server_rules['mp_fraglimit']))
{
	echo l('Fragsleft'),': '.$server_rules['mp_fragsleft'];
	if (isset($server_rules['mp_fragslimit']))
		echo '('.$server_rules['mp_fraglimit'].')';
	echo '<br>';
}
?>
						<?php echo l('Password'); ?>: <?php echo $server_details['password']; ?><br>
						<?php echo l('Players'); ?>: <?php echo $server_details['players_real']; ?>/<?php echo $server_details['maxplayers'];?><br>
<?php
if (!empty($server_details['botcount'])) {
	# Don't show this information if there are no bots
?>
						<?php echo l('Bots'); ?>: <?php echo $server_details['numbots']; ?>/<?php echo $server_details['maxplayers']; ?><br>
<?php
}
if (!empty($server_details['hltvcount'])) {
	# Don't show this information if there is no HLTV
?>
						<?php echo l('HLTV'); ?>: <?php echo $server_details['hltvcount']; ?>/<?php echo $server_details['maxplayers']; ?><br>
<?php
}
if (!empty($server_details['players_connecting'])) {
	# Don't show this information if there are no players connecting
?>
						<?php echo l('Connecting'); ?>: <?php echo $server_details['players_connecting'] ?>/<?php echo $server_details['maxplayers']; ?><br>
<?php
}
?>
						<?php echo l('Valve Anti-Cheat'); ?>: <?php echo $server_details['secure']; ?><br>
<?php
$addon_array = array();
$server_details['addon_count'] = 0;
if ($server_rules) {
?>
						<?php echo l('Rules'); ?>:<br>
						<select name="rules" style="width: 200px;">
<?php
	# Load our plugin list
	$query = mysql_query("SELECT * FROM ".DB_PREFIX."_Server_Addons");

	while ($addon_list = mysql_fetch_assoc($query))
		$server_addon[$addon_list['rule']] = array('addon' => $addon_list['addon'], 'url' => $addon_list['url']);

	ksort($server_rules);

	foreach ($server_rules as $key => $value) {
		if (isset($server_addon[$key])) {
			if ($server_addon[$key]['url']) {
				$addon_array[] = '<a href="'.$server_addon[$key]['url'].'" target="_blank">'.str_replace('%', $value, $server_addon[$key]['addon']).'</a>';
			}
			else {
				$addon_array[] = str_replace('%', $value, $server_addon[$key]['addon']);
			}
			$server_details['addon_count']++;
		}
		echo "<option>$key = $value</option>\n";
	}
?>
						</select><br><br>
<?php
}
?>
<?php
if (!empty($server_details['addon_count'])) {
?>
<?php echo l('Server Addons'); ?>:<br>
<ul>
<?php
	foreach ($addon_array as $addon) {
?>
	<li> <?php echo $addon; ?></li>
<?php
	}
?>
</ul>
<?php
}
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
echo '<b>'.round(($time - $start), 6).'</b><br>';
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
