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

class Player {
	/**
	 * the player id
	 */
	public $playerId = false;

	/**
	 * the game
	 * need for player lookup via uniqueid
	 */
	private $_game = false;

	/**
	 * the player data
	 * non empty if successfull
	 */
	private $_playerData = false;

	/**
	 * load the player id
	 */
	public function __construct($id,$mode,$game) {
		$ret = false;
		
		if(!empty($id)) {
			$this->_game = $game;
			
			if($mode === false) {
				$this->playerId = $id;
			}
			elseif($mode === true && !empty($game)) {
				$this->_lookupPlayerIdFromUniqeId($id);
				if(empty($this->playerId)) {
					new Exception("PlayerID can't be looked up via uniqueid in Player.class");
				}
			}
			else {
				new Exception("Player mode is missing for Player.class");
			}
		}
		else {
			new Exception("Player ID or game is missing for Player.class");
		}

		$ret = $this->_load();

		return $ret;
	}

	/**
	 * return given param from player
	 */
	public function getParam($param) {
		$ret = false;
		
		if(!empty($param)) {
			if(isset($this->_playerData[$param])) {
				$ret = $this->_playerData[$param];
			}
		}

		return $ret;
	}

	/**
	 * load the player data from db
	 */
	private function _load() {
		if(!empty($this->playerId)) {
			$query = mysql_query("SELECT
					".DB_PREFIX."_Players.lastName AS name,
					".DB_PREFIX."_Players.clan,
					".DB_PREFIX."_Players.fullName,
					".DB_PREFIX."_Players.email,
					".DB_PREFIX."_Players.homepage,
					".DB_PREFIX."_Players.icq,
					".DB_PREFIX."_Players.game,
					".DB_PREFIX."_Players.skill,
					".DB_PREFIX."_Players.oldSkill,
					".DB_PREFIX."_Players.kills,
					".DB_PREFIX."_Players.deaths,
					IFNULL(kills/deaths, '-') AS kpd,
					".DB_PREFIX."_Players.suicides,
					CONCAT(".DB_PREFIX."_Clans.tag, ' ', ".DB_PREFIX."_Clans.name) AS clan_name
				FROM
					".DB_PREFIX."_Players
				LEFT JOIN ".DB_PREFIX."_Clans ON
					".DB_PREFIX."_Clans.clanId = ".DB_PREFIX."_Players.clan
				WHERE
					playerId='".mysql_escape_string($this->playerId)."'");
			if(mysql_num_rows($query)) {
				$result = mysql_fetch_assoc($query);
				$this->_playerData = $result;
			}
		}

		$this->_getUniqueIds();
		$this->_getLastConnect();
		$this->_getMaxConnectTime();
		$this->_getAvgPing();
	}

	/**
	 * get the playerId via the player uniqueid
	 * the game is also needed !
	 */
	private function _lookupPlayerIdFromUniqeId($id) {
		$ret = false;
		
		$query = mysql_query("SELECT playerId FROM ".DB_PREFIX."_PlayerUniqueIds
					WHERE uniqueId='".mysql_escape_string($id)."'
						AND game='".mysql_escape_string($this->_game)."'");

		if(mysql_num_rows($query) > 1) {
			$result = mysql_fetch_assoc($query);
			$this->playerId = $result['playerId'];

			$ret = true;
		}
				
		return $ret;
	}

	/**
	 * get the playr uniqueids if any
	 */
	private function _getUniqueIds() {
		$this->_playerData['uniqueIds'] = '-';
		$query = mysql_query("SELECT uniqueId
						FROM ".DB_PREFIX."_PlayerUniqueIds
						WHERE playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 1) {
			while ($result = mysql_fetch_assoc($query)) {
				$ret = $result['uniqueId'].",";
			}
			$this->_playerData['uniqueIds'] = trim($ret,',');
			mysql_free_result($query);
		}
	}

	/**
	 * get the last connect from connect table
	 */
	private function _getLastConnect() {
		$this->_playerData['lastConnect'] = l('No info');
		$query = mysql_query("SELECT MAX(eventTime) AS eventTime
					FROM ".DB_PREFIX."_Events_Connects
					WHERE playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 1) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['lastConnect'] = $result['eventTime'];
			mysql_free_result($query);
		}
	}

	/**
	 * get the max connection time
	 * if we have the information
	 */
	private function _getMaxConnectTime() {
		$this->_playerData['maxTime'] = l('No info');
		$query = mysql_query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(time))) AS tTime
					FROM ".DB_PREFIX."_Events_StatsmeTime
					WHERE playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 1) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['maxTime'] = $result['tTime'];
			mysql_free_result($query);
		}
	}

	/**
	 * get the average ping if we have the information
	 */
	private function _getAvgPing() {
		$this->_playerData['avgPing'] = l('No info');
		$query = mysql_query("SELECT ROUND(SUM(ping) / COUNT(ping), 1) AS av_ping
					FROM ".DB_PREFIX."_Events_StatsmeLatency
					WHERE playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 1) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['avgPing'] = $result['av_ping'];
			mysql_free_result($query);
		}		
	}
}

?>
