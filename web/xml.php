<?php
/**
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

// Check PHP configuration
if (version_compare(phpversion(), "5.0.0", "<")) {
	die("HLStats requires PHP version 5.0.0 or newer (you are running PHP version " . phpversion() . ").");
}

if (!get_magic_quotes_gpc()) {
	die("HLStats requires <b>magic_quotes_gpc</b> to be <i>enabled</i>. Check your php.ini or refer to the PHP manual for more information.");
}

if (get_magic_quotes_runtime()) {
	die("HLStats requires <b>magic_quotes_runtime</b> to be <i>disabled</i>. Check your php.ini or refer to the PHP manual for more information.");
}

date_default_timezone_set('Europe/Berlin');

// if you have problems with your installation
// activate this paramter by setting it to true
define('SHOW_DEBUG',true);

// do not display errors in live version
if(SHOW_DEBUG === true) {
	error_reporting(8191);
	ini_set('display_errors',true);
}
else {
	ini_set('display_errors',false);
}

// load config
require('./hlstatsinc/hlstats.conf.php');

/**
 * load required stuff
 *
 * functions functions.inc.php
 * db class
 * general classes like table class
 */
require(INCLUDE_PATH . "/functions.inc.php");
require(INCLUDE_PATH . "/classes.inc.php");

// deb class and options
$db_con = mysql_connect(DB_ADDR,DB_USER,DB_PASS);
$db_sel = mysql_select_db(DB_NAME,$db_con);

// get the hlstats options
$g_options = getOptions();

// hlstats url
$hlsUrl = "http://www.".$_SERVER['SERVER_NAME'].str_replace("xml.php","",$_SERVER['SCRIPT_NAME']);

// check if we are allowed to use this feature
if($g_options['allowXML'] == "1") {
	// we are allowed to return some xml data
	switch ($_GET['mode']) {
		/**
		 * return only the top 10 players for given gameCode
		 */
		case 'playerlist':
			$gameCode = sanitize($_GET['gameCode']);
			if(!empty($gameCode) && validateInput($gameCode,'nospace')) {
				$query = mysql_query("SELECT
			    			t1.playerId,lastName,skill
			    		FROM
			    			hlstats_Players as t1 INNER JOIN hlstats_PlayerUniqueIds as t2
			    			ON t1.playerId = t2.playerId
			    		WHERE
			    			t1.game='".$gameCode."'
			    			AND t1.hideranking=0
			    			AND t2.uniqueId not like 'BOT:%'
			    		ORDER BY skill DESC
			    		LIMIT 10");
				$xmlBody = "<players info='top 10 playerlist'>";
				while ($playerData = mysql_fetch_assoc($query)) {
					$xmlBody .="<player>";
					$xmlBody .="<name><![CDATA[".htmlentities($playerData['lastName'],ENT_COMPAT,"UTF-8")."]]></name>";
					$xmlBody .="<skill>".$playerData['skill']."</skill>";
					$xmlBody .="<profile><![CDATA[".$hlsUrl."hlstats.php&mode=playerinfo&player=".$playerData['playerId']."]]></profile>";
					$xmlBody .="</player>";
				}
				$xmlBody .= "</players>";
			}
			else {
				$xmlBody = "<message>No game Code given.</message>";
			}

		break;

		case 'worldstats':
			$gameCode = sanitize($_GET['gameCode']);
			if(!empty($gameCode) && validateInput($gameCode,'nospace')) {
				$query = "SELECT
			    			t1.playerId,lastName,skill
			    		FROM
			    			hlstats_Players as t1 INNER JOIN hlstats_PlayerUniqueIds as t2
			    			ON t1.playerId = t2.playerId
			    		WHERE
			    			t1.game='".$gameCode."'
			    			AND t1.hideranking=0
			    			AND t2.uniqueId not like 'BOT:%'
			    		ORDER BY skill DESC
			    		LIMIT 10";
			}
		break;

		/**
		 * return some information about the given server like livestats view
		 */
		case 'serverinfo':
		default:
			// we want some server info
			$serverId = sanitize($_GET['serverId']);
			if(!empty($serverId) && validateInput($serverId,'digit')) {
				// check if we have such server
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
							hlstats_Servers s
						LEFT JOIN
							hlstats_Games g
						ON
							s.game=g.code
						WHERE
							serverId=".$serverId."
							");
				if (mysql_num_rows($query) === 1) {
					// get the server data
					$serverData = mysql_fetch_assoc($query);

					$xmlBody = "<server>";
					$xmlBody .= "<name>".$serverData['name']."</name>";
					$xmlBody .= "<ip>".$serverData['address']."</ip>";
					$xmlBody .= "<port>".$serverData['port']."</port>";
					$xmlBody .= "<game>".$serverData['gamename']."</game>";

					// load the required stuff
					include(INCLUDE_PATH.'/binary_funcs.inc.php');
					include(INCLUDE_PATH.'/hlquery_funcs.inc.php');

					$xmlBody .= "<additional>";
					// run some query to display some more info
					if ($serverData['publicaddress'] != "") {
						# Port maybe different
						$temp = explode(':', $serverData['publicaddress']);
						$server_ip = $serverData['address'];
						if (isset($temp[1])) {
							$server_port = $temp[1];
						}
						else {
							$server_port = $serverData['port'];
						}
					}
					else {
						$server_ip = $serverData['address'];
						$server_port = $serverData['port'];
					}

					// check if we have a rcon password
					$server_rcon = false;
					if($serverData['rcon_password'] != "") {
						$server_rcon = $serverData['rcon_password'];
					}

					$server_hltv = array();
					$server_players = array();

					# Get info
					if (($server_details = Source_A2S_Info($server_ip, $server_port)) !== false) {
						if ($server_details['gametype'] == 73) {
							$serverData['source'] = 1;
							$server_details['address'] = $server_ip.':'.$server_port;
						}
						else {
							$serverData['source'] = 0;
						}

						$server_details['hltvcount'] = count($server_hltv);

						$server_details['players_real'] = $server_details['numplayers'];
						$server_details['players_real'] -= $server_details['numbots'];
						$server_details['players_real'] -= $server_details['hltvcount'];

						$server_details['players_connecting'] = $server_details['numplayers'];
						$server_details['players_connecting'] -= $server_details['numbots'];
						$server_details['players_connecting'] -= count($server_players);
						$server_details['players_connecting'] -= $server_details['hltvcount'];

						// we have some info from the server (no rcon yet)
						$xmlBody .= "<map>".$server_details['map']."</map>";
						$xmlBody .= "<serverName>".htmlentities($server_details['hostname'],ENT_COMPAT,"UTF-8")."</serverName>";
						$xmlBody .= "<maxplayers>".$server_details['maxplayers']."</maxplayers>";
						$xmlBody .= "<players>".$server_details['players_real']."</players>";
						$xmlBody .= "<secure>".$server_details['secure']."</secure>";

						if ($server_details['botcount'] > 0) {
							$xmlBody .= "<bots>".$server_details['numbots']."</bots>";
						}

						# Get challenge
						$query_challenge = Source_A2S_GetChallenge($server_ip, $server_port);

						# Get packets with challenge number
						$server_rules = Source_A2S_Rules($server_ip, $server_port, $query_challenge);
						$server_players = Source_A2S_Player($server_ip, $server_port, $query_challenge);

						$server_details = Format_Info_Array($server_details);

						// the nextmap
						if (isset($server_rules['cm_nextmap'])) {
							$server_details['nextmap'] = $server_rules['cm_nextmap'];
						}
						elseif (isset($server_rules['amx_nextmap'])) {
							$server_details['nextmap'] = $server_rules['amx_nextmap'];
						}
						elseif (isset($server_rules['mani_nextmap'])) {
							$server_details['nextmap'] = $server_rules['mani_nextmap'];
						}
						if(isset($server_details['nextmap']) && $server_details['nextmap'] != "") {
							$xmlBody .= "<nextmap>".$server_details['nextmap']."</nextmap>";
						}

						# Some unfortunate games like CS don't usually give the map timeleft
						# I wonder if some plugin can yet again provide a use here...
						# Generally the plugin version is more reliable so that is the highest priority to use
						if (isset($server_rules['amx_timeleft'])) {
							$server_details['timeleft'] = $server_rules['amx_timeleft'];
						}
						elseif (isset($server_rules['cm_timeleft'])) {
							$server_details['timeleft'] = $server_rules['cm_timeleft'];
						}
						elseif (isset($server_rules['mp_timeleft'])) {
							$server_details['timeleft'] = sprintf('%02u:%02u', ($server_rules['mp_timeleft'] / 60), ($server_rules['mp_timeleft'] % 60));
						}
						elseif (isset($server_rules['mani_timeleft'])) {
							$server_details['timeleft'] = $server_rules['mani_timeleft'];
						}
						if (isset($server_details['timeleft'])) {
							$xmlBody .= "<timeleft>".$server_details['timeleft']."</timeleft>";
						}

						// frags left
						if ($server_rules['mp_fraglimit'] > 0) {
							$xmlBody .= "<fragsmax>".$server_rules['mp_fraglimit']."</fragsmax>";
							$xmlBody .= "<fragsleft>".$server_rules['mp_fragsleft']."</fragsleft>";
						}
					}
					else {
						$xmlBody .= "<message>No info available</message>";
					}

					$xmlBody .= "</additional>";

					// end of xml body
					$xmlBody .= "</server>";
				}
				else {
					// we have no such server
					$xmlBody = "<message>No such server.</message>";
				}
			}
			else {
				// we have no server id
				$xmlBody = "<message>No server ID given.</message>";
			}
	}
}
else {
	$xmlBody = "<message>Service not available.</message>";
}

// prepare the xml data
$xmlReturn = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
$xmlReturn .= '<root>';
$xmlReturn .= $xmlBody;
$xmlReturn .= '</root>';

// return the xml data
echo $xmlReturn;
?>
