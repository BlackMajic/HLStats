<?php
/**
 * $Id: hlquery_funcs.inc.php 658 2009-02-20 15:16:03Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstatsinc/hlquery_funcs.inc.php $
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

   // Half-Life Query Functions

   // http://developer.valvesoftware.com/wiki/Server_Queries

   // These functions with the use of Detritus' binary functions allow HL servers to be queried
   // and data to be returned in a friendly readable format
function GetServerData($command, $ip, $port) {

	$data = '';
	if (!$server_connection = fsockopen('udp://'.$ip, $port)) {
		return false;
	}

	socket_set_blocking($server_connection, 1);
	# Time out after 4 seconds
	socket_set_timeout($server_connection, 4);
	//fwrite($server_connection, $command, strlen($command));
	fwrite($server_connection, $command);
	# This must time out first to make sure the right arguement is returned
	# The above timeout is just to make sure it doesn't go over 4 seconds
	$timeout = microtime();
	$timeout += 4;
	$formated_data = '';
	do
	{

	# Rough explanation
	# First loop is a hack to force packet scanning
	# for the Valve split packet bug
		$packetsfound = 0;
		$packets = 0;
		$packet_data = array();
		do
		{
		# Second loop is a loop to keep grabbing packets
		# when split packets are not bugged
			$data = '';

			do
			{
			# Third loop keeps scanning for data
			# once we have found a packet

				$data .= fread($server_connection, 8192);
				if (microtime() > $timeout)
				{
					# Connection timed out

					fclose($server_connection);
					return false;
				}
				$data_status = socket_get_status($server_connection);
			}
			while ($data_status['unread_bytes']);

			$tmp = 0;
			if (GetInt8($data, $tmp) == 254) {
				# We have a split packet!
				$datacounter = 0;

				# Type
				GetInt32($data, $datacounter);
				# Packet ID
				GetInt32($data, $datacounter);

				$split_packet_info = str_pad(decbin(GetInt8($data, $datacounter)), 8, 0, STR_PAD_LEFT);
				$packet_num = bindec(substr($split_packet_info, 0, 4));
				$packets = bindec(substr($split_packet_info, 4));

				$data = substr($data, $datacounter);
				$packet_data[$packet_num] = $data;
			}
			else
			{
				# Packet hasn't indicitated it's split
				# It could be a lone packet or the Valve split packet bug
				# If later, pray to God it isn't a split packet that has come in reverse order

				# Only cut this off if we can safely assume it's a multipacket and we don't know it is
				if (strstr($data, "\xFF\xFF\xFF\xFFl"))
					$datacounter = 5;
				else
					$datacounter = 0;

				# Get rid of the crap at the start
				$data = substr($data, $datacounter);

				$packet_data[] = $data;
			}
			$packetsfound++;
		}
		while ($packetsfound < $packets);

		ksort($packet_data);
		$formated_data .= implode('', $packet_data);

		# The above code follows protocol
		# To protect against Valve not following protocol
		# we must check if the data is :-
		# a) an rcon status check
		# b) contains the start of the packet
		# c) contains of packet
		# Future ref:
		# May need to actually check the packet to see if the X Users
		# actually is equal to amount of users in packet in case a mid section
		# goes AWOL

		# RCon status packet:
		# \xFF\xFF\xFF\xFFrcon [32bit Int] "[rcon password string]" status
		# Might want to use regexps here
		if (strstr($command, "\xFF\xFF\xFF\xFFrcon"))
		{
			# check if header part is found
			# Hostname: HLstats 0wns \o/
			if (strstr($formated_data, 'hostname: '))
			{
				# Woohoo found
				# Now find if it ends with X users
				# Might want to use regexps
				if (strstr($formated_data, ' users'))
					break;
			}
			elseif (eregi('^Bad challenge.', $formated_data))
				break;
			elseif (eregi('^Bad rcon_password.', $formated_data))
				break;

			# We are going to loop again and attempt a further scan
			# as we couldn't find users or hostname in the packet
		}
		else
			break;
	}
	while (1);

	fclose($server_connection);

	if (strlen($formated_data) == 0)
	{
		# We got no data?! Something must have gone wrong
		return false;
	}
 	return $formated_data;
}

function HalfLife_Rules($ip, $port)
{
	$cmd = "\xFF\xFF\xFF\xFFV\x00";
	if(!$serverdata = GetServerData($cmd, $ip, $port))
		return false;

	return Decode_Rule_Packet($serverdata);
}


function HalfLife_Players($ip, $port)
{
	$cmd = "\xFF\xFF\xFF\xFFU\x00";

	if(!$serverdata = GetServerData($cmd, $ip, $port))
		return array();

	return Decode_Player_Packet($serverdata);
}

function HalfLife_Details ($ip, $port)
{
	$cmd = "\xFF\xFF\xFF\xFFT\x00"; 	if (!$serverdata = GetServerData($cmd, $ip, $port))
		return false;

	$serverdetails = array();
	$datastart = 0;

	GetInt32($serverdata, $datastart);
	GetInt8($serverdata, $datastart); 	$serverdetails['address'] = GetString($serverdata, $datastart);
	$serverdetails['hostname'] = GetString($serverdata, $datastart);
	$serverdetails['map'] = GetString($serverdata, $datastart);
	$serverdetails['gamedir'] = GetString($serverdata, $datastart);
	$serverdetails['description'] = GetString($serverdata, $datastart);
	$serverdetails['players'] = GetInt8($serverdata, $datastart);
	$serverdetails['max'] = GetInt8($serverdata, $datastart);
	$serverdetails['protocol'] = GetInt8($serverdata, $datastart);
	$serverdetails['type'] = chr(GetInt8($serverdata, $datastart));
	$serverdetails['os'] = chr(GetInt8($serverdata, $datastart));
	$serverdetails['password'] = GetInt8($serverdata, $datastart);
	$serverdetails['ismod'] = GetInt8($serverdata, $datastart);
	if($serverdetails['ismod'])
	{
		$serverdetails['mod_website'] = GetString($serverdata, $datastart);
		$serverdetails['mod_download'] = GetString($serverdata, $datastart);
		$serverdetails['hl_version'] = GetString($serverdata, $datastart);
		$serverdetails['mod_version'] = GetInt32($serverdata, $datastart);
		$serverdetails['mod_size'] = GetInt32($serverdata, $datastart);
		$serverdetails['mod_type'] = GetInt8($serverdata, $datastart);
		$serverdetails['mod_dll'] = GetInt8($serverdata, $datastart);
	}
	$serverdetails['secure'] = GetInt8($serverdata, $datastart);
	$serverdetails['botcount'] = GetInt8($serverdata, $datastart);

	return $serverdetails;
}

function Source_A2S_Info ($ip, $port)
{
	$cmd = "\xFF\xFF\xFF\xFF".'TSource Engine Query'."\x00";
	if (!$serverdata = GetServerData($cmd, $ip, $port))
		return false;

	$serverdetails = array();
	$datastart = 0;

	# Junk
	GetInt32($serverdata, $datastart);

	$serverdetails['gametype'] = GetInt8($serverdata, $datastart);
	# I = 73 = Source
	# m = 109 = HL1
	if ($serverdetails['gametype'] == 73)
		return Decode_Source_Info_Packet($serverdata);
	else
		return Decode_HL1_Info_Packet($serverdata);
}

function Source_A2S_Player ($ip, $port, $challenge) {
	$cmd = "\xFF\xFF\xFF\xFF\x55".pack('l', $challenge);
	if(!$serverdata = GetServerData($cmd, $ip, $port))
		return array();

	GetInt32($serverdata, $datastart);
	//$type = GetInt8($packet, $datastart);
	$type = GetInt8($serverdata, $datastart);
	if ($type == 65) {
		$challenge = Decode_Challenge_Packet($serverdata, $datastart);
		return Source_A2S_Player($ip, $port, $challenge);
	}
	return Decode_Player_Packet($serverdata);
}

function Source_A2S_Rules($ip, $port, $challenge)
{
	$cmd = "\xFF\xFF\xFF\xFF\x56".pack('l', $challenge);
	if(!$serverdata = GetServerData($cmd, $ip, $port))
		return false;

	$datastart = 0;
	GetInt32($serverdata, $datastart);
	$type = GetInt8($serverdata, $datastart);

	if ($type == 65)
	{
		$challenge = Decode_Challenge_Packet($serverdata, $datastart);
		return Source_A2S_Rules($ip, $port, $challenge);
	}
	return Decode_Rule_Packet($serverdata);
}

function Source_A2S_GetChallenge ($ip, $port) {

	/*
	$cmd = "\xFF\xFF\xFF\xFF"."\x57";

	if(!$serverdata = GetServerData($cmd, $ip, $port))
		//return "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF";
		return "\xFF\xFF\xFF\xFF";

	return Decode_Challenge_Packet($serverdata);
	*/
	// possible fix because we get no challange
	// http://developer.valvesoftware.com/wiki/Server_Queries#A2S_PLAYER
	return "\xFF\xFF\xFF\xFF";
}

# Packet decoding functions

function Decode_Challenge_Packet ($packet) {
	$datastart = 0;

	GetInt32($packet, $datastart);

	$type = GetInt8($packet, $datastart);
	$challenge = GetInt32($packet, $datastart);

	return $challenge;
}

function Decode_Source_Info_Packet ($packet)
{
	$serverdetails = array();
	$datastart = 0;

	# Junk
	GetInt32($packet, $datastart);
	$serverdetails['gametype'] = GetInt8($packet, $datastart);
	$serverdetails['netversion'] = GetInt8($packet, $datastart);
	$serverdetails['hostname'] = GetString($packet, $datastart);
	$serverdetails['map'] = GetString($packet, $datastart);
	$serverdetails['gamedir'] = GetString($packet, $datastart);
	$serverdetails['gamedesc'] = GetString($packet, $datastart);
	$serverdetails['appid'] = GetInt16($packet, $datastart);
	$serverdetails['numplayers'] = GetInt8($packet, $datastart);
	$serverdetails['maxplayers'] = GetInt8($packet, $datastart);
	$serverdetails['numbots'] = GetInt8($packet, $datastart);
	$serverdetails['servertype'] = chr(GetInt8($packet, $datastart));
	$serverdetails['serveros'] = chr(GetInt8($packet, $datastart));
	$serverdetails['password'] = GetInt8($packet, $datastart);
	$serverdetails['secure'] = GetInt8($packet, $datastart);
	$serverdetails['gameversion'] = GetString($packet, $datastart);

	return $serverdetails;
}

function Decode_HL1_Info_Packet ($packet)
{
	$serverdetails = array();
	$datastart = 0;

	GetInt32($packet, $datastart); 	$serverdetails['gametype'] = GetInt8($packet, $datastart);
	$serverdetails['address'] = GetString($packet, $datastart);
	$serverdetails['hostname'] = GetString($packet, $datastart);
	$serverdetails['map'] = GetString($packet, $datastart);
	$serverdetails['gamedir'] = GetString($packet, $datastart);
	$serverdetails['gamedesc'] = GetString($packet, $datastart);
	$serverdetails['numplayers'] = GetInt8($packet, $datastart);
	$serverdetails['maxplayers'] = GetInt8($packet, $datastart);
	$serverdetails['gameversion'] = GetInt8($packet, $datastart);
	$serverdetails['servertype'] = chr(GetInt8($packet, $datastart));
	$serverdetails['serveros'] = chr(GetInt8($packet, $datastart));
	$serverdetails['password'] = GetInt8($packet, $datastart);
	$serverdetails['ismod'] = GetInt8($packet, $datastart);
	if($serverdetails['ismod'])
	{
		$serverdetails['mod_website'] = GetString($packet, $datastart);
		$serverdetails['mod_download'] = GetString($packet, $datastart);
		$serverdetails['hl_version'] = GetString($packet, $datastart);
		$serverdetails['mod_version'] = GetInt32($packet, $datastart);
		$serverdetails['mod_size'] = GetInt32($packet, $datastart);
		$serverdetails['mod_type'] = GetInt8($packet, $datastart);
		$serverdetails['mod_dll'] = GetInt8($packet, $datastart);
	}
	$serverdetails['secure'] = GetInt8($packet, $datastart);
	$serverdetails['numbots'] = GetInt8($packet, $datastart);

	return $serverdetails;
}

function Decode_Player_Packet ($packet)
{
	$serverplayers = array();
	$datastart = 0;

	GetInt32($packet, $datastart);

	$type = GetInt8($packet, $datastart);
	$players = GetInt8($packet, $datastart);

	if ($players == 0) return $serverplayers;

	for ($i = 0; $i < $players; $i++) {
		$index = GetInt8($packet, $datastart);
		$name = GetString($packet, $datastart);
		$frags = GetInt32($packet, $datastart);
		$time = GetFloat32($packet, $datastart);
		$serverplayers[$i] = array('index' => $index, 'name' => $name, 'frags' => $frags, 'time' => $time);
	}
	return $serverplayers;
}

function Decode_Rule_Packet ($packet)
{
	$serverrules = array();
	$datastart = 0;

	GetInt32($packet, $datastart);

	$type = GetInt8($packet, $datastart);
	$rules = GetInt16($packet, $datastart);

	if ($rules == 0)
		return false;

	for ($i = 1; $i <= $rules; $i++)
	{
		$rulename = GetString($packet, $datastart);
		$rulevalue = GetString($packet, $datastart);
		$serverrules[$rulename] = $rulevalue;
	}
	return $serverrules;
}

# Rcon functions

function HalfLife_GetChallenge ($ip, $port)
{
	$cmd = "\xFF\xFF\xFF\xFF".'challenge rcon'."\n";

	if (!$serverdata = GetServerData($cmd, $ip, $port))
		return false;

	$datastart = 0;

	GetInt32($serverdata, $datastart);
	$challenge = GetString($serverdata, $datastart);

	$tmp = explode(' ', $challenge);
	$challenge = trim($tmp[2]); 	return $challenge;
}

function HalfLife_Rcon ($ip, $port, $rcon, $command, &$challenge, $challenged = 0)
{
	# No point challenging more than we have too.
	if (strlen($challenge) < 5)
	{
		$challenge = HalfLife_GetChallenge($ip, $port);
		$challenged++;
	}

	$cmd = "\xFF\xFF\xFF\xFFrcon $challenge \"$rcon\" $command";
 	if (!$serverdata = GetServerData($cmd, $ip, $port))
		return false;

	$serverdata = trim($serverdata);
	if (eregi('^Bad challenge.', $serverdata))
	{
		# Try for the challenge twice, if that fails... fail?
		if($challenged < 2)
		{
			$challenge = '';
			$serverdata = HalfLife_Rcon($ip, $port, $rcon, $command, $challenge, $challenged);
		}
		else
			return false;
	}
	elseif (eregi('^Bad rcon_password.', $serverdata))
		return false;

	return $serverdata;
}

function Get_A_Packet (&$server_connection)
{
	$timeout = microtime();
	$timeout += 5;
	$data = '';

	do
	{
		$data .= fread($server_connection, 8192);

		if (microtime() > $timeout)
		{
			# Connection timed out
			return false;
		}
		$data_status = socket_get_status($server_connection);
	}
	while ($data_status['unread_bytes']);

	return $data;
}

/**
 * http://developer.valvesoftware.com/wiki/Source_RCON_Protocol
 *
 * @param string $ip
 * @param string $port
 * @param string $rcon
 * @param string $string1
 * @param string $string2
 * @return array $data
 */
function Source_Rcon ($ip, $port, $rcon, $string1, $string2 = NULL) {

	$reqid = mt_rand(0, 255);
	$reqtype = 3;

	# add the zero
	/*
	$rcon .= "\x00";
	$string1 .= "\x00";
	$string2 .= "\x00";
	*/

 	if (!$server_connection = fsockopen("udp://".$ip, $port)) {
		return false;
	}

	socket_set_blocking($server_connection, 1);
	# Time out after 5 seconds
	socket_set_timeout($server_connection, 5);

	# First we need to auth
	# Auth packet creation
	$command = pack('VV', $reqid, $reqtype).$rcon.$string2;
	$cmdlen = strlen($command);
	$command = pack('V', $cmdlen).$command;

	# Request Auth
	fwrite($server_connection, $command, strlen($command)); 	# Get the thingy packet that currently has no use
	$data = Get_A_Packet($server_connection);

	# Get auth packet
	$data = Get_A_Packet($server_connection); 	$datastart = 0;
	$recsize = GetInt32($data, $datastart);
	$recid = GetInt32($data, $datastart);
	$rectype = GetInt32($data, $datastart);
	$recstring1 = GetString($data, $datastart);
	$recstring2 = GetString($data, $datastart);

	# If we don't get what we expect, we must have failed?
	if ($rectype != 2) {
		fclose($server_connection);
		return false;
	}
	# If the packet ID's dont match, auth failed
	if ($recid != $reqid) {
		fclose($server_connection);
		return false;
	}
	# Prepare new command
	$reqid = mt_rand(0, 255);
	$reqtype = 2;
	$command = pack('VV', $reqid, $reqtype).$string1.$string2;
	$cmdlen = strlen ($command);
	$command = pack('V', $cmdlen).$command;

	# Send new command
	fwrite($server_connection, $command, strlen($command)); 	$data = '';
	# i will increment every loop
	# t will only increment when a packet was received.
	# should something break, it should.. in theory, kill itself
	# but hopefully something will have killed it earlier
	$i = 0;
	$t = 1;

	#while ($i <= $t)
	#{
		#$i++;
		$packet = '';
		#if ($packet = Get_A_Packet($server_connection))
		$packet = Get_A_Packet($server_connection);
		#{
		#	$t++;
		#}

		$datastart = 0;
		$recsize = GetInt32($packet, $datastart);
		$recid = GetInt32($packet, $datastart);

		if ($recid == $reqid)
		{
			# I wonder how long it will be till this gets triggered
			# Maybe? Never? Fear the bugs in the protocol!
			# Header packet
			$rectype = GetInt32($packet, $datastart);
			$data .= GetString($packet, $datastart);
			$recstring2 = GetString($packet, $datastart);
		}
		else
			$data .= $packet;
	#}
	# Make sure socket is closed
	fclose($server_connection);

	return $data;
}

# General formatting functions

function Format_Info_Array ($info)
{
	if ($info['servertype'] == 'd')
		$info['servertype'] = 'Dedicated';
	elseif ($info['servertype'] == 'l')
		$info['servertype'] = 'Listen';
	elseif ($info['servertype'] == 'p')
		$info['servertype'] = 'Proxy';

	if ($info['serveros'] == 'w')
		$info['serveros'] = 'Windows';
	elseif ($info['serveros'] == 'l')
		$info['serveros'] = 'Linux';

	if ($info['password'] == 0)
		$info['password'] = 'No';
	elseif ($info['password'] == 1)
		$info['password'] = 'Yes';

	if ($info['secure'] == 0)
		$info['secure'] = 'No';
	elseif ($info['secure'] == 1)
		$info['secure'] = 'Yes';

	return $info;
}

function Format_Time ($temp)
{
	$time = sprintf('%s', str_pad(floor($temp/3600), 2, 0, STR_PAD_LEFT));
	$temp %= 3600;
	$time = sprintf('%s:%s', $time, str_pad(floor($temp/60), 2, 0, STR_PAD_LEFT));
	$temp %= 60;
	$time = sprintf('%s:%s', $time, str_pad(floor($temp), 2, 0, STR_PAD_LEFT));
	return $time;
}

function Deformat_Time ($time)
{
	$temp = explode (':', $time);
	$temp = array_reverse($temp);
	$seconds = 0;
	# Seconds
	$seconds += $temp[0];
	# Minutes
	$seconds += $temp[1] * 60;
	# Hours
	$seconds += $temp[2] * 3600;
	return $seconds;
}

function Parse_HL_Status ($status, $source, &$server_players, &$server_hltv)
{
	$status = str_replace("\x00", '', $status);

	$temp = explode("\n", $status);

	$i = count($temp);
	unset($temp[0]); # hostname
	unset($temp[1]); # version
	unset($temp[2]); # tcp/ip
	unset($temp[3]); # map
	unset($temp[4]); # players
	unset($temp[5]); # *blank*
	unset($temp[6]); # header
	if ($temp[7] == '0 Users')
	{
		# Server is empty
	}
	else
	{
		# Get rid of the "X Users" line
		unset($temp[$i-1]);

		$i = 0;
		foreach ($temp as $players)
		{
			# Get the name before reformating the string
			eregi('"(.*)"', $players, $tmp);
			$name = $tmp[1];

			# de-spacalise the string
			$players = preg_replace("/( +)/", ' ', $players);

			# Get the de-spacelised name from the de-spacalised string
			eregi('"(.*)"', $players, $tmp);
			$name2 = $tmp[1];

			# get rid of the hashes, they only cause problems
			$players = str_replace('# ', '', $players);
			$players = str_replace('#', '', $players);


			$player = explode(' ', $players);
			$count = substr_count($name2, ' ') + 2;

			if ('HLTV' == $player[$count+1])
			{
				# It is possible for multiple HLTVs on 1 server so lets build it like the players.
				$server_hltv[] = array('id' => $player[0],
					'name' => $name,
					'userid' => $player[$count++],
					'uid' => $player[$count++],
					'viewers' => substr($player[$count], 5, 1),
					'capacity' => substr($player[$count++], 7),
					'delay' => substr($player[$count++], 6),
					'time' => Deformat_Time($player[$count++]),
					'ip' => $player[$count++]);
			}
			else
			{
				if ($source)
				{
					# Source status is different
					$server_players[] = array('id' => $player[0],
						'name' => $name,
						'uid' => $player[$count++],
						'time' => Deformat_Time($player[$count++]),
						'ping' => $player[$count++],
						'loss' => $player[$count++],
						'state' => $player[$count++],
						'ip' => $player[$count++]);
				}
				else
				{
					$server_players[] = array('id' => $player[0],
						'name' => $name,
						'userid' => $player[$count++],
						'uid' => $player[$count++],
						'frags' => $player[$count++],
						'time' => Deformat_Time($player[$count++]),
						'ping' => $player[$count++],
						'loss' => $player[$count++],
						'ip' => $player[$count++]);
				}
			}
			$i++;
		}
	}
}
?>
