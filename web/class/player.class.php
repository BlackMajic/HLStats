<?php
/**
 * player class file
 * @package HLStats
 * @author Johannes 'Banana' Keßler
 */

/**
 * Original development:
 *
 * + HLStats - Real-time player and clan rankings and statistics for Half-Life
 * + http://sourceforge.net/projects/hlstats/
 * + Copyright (C) 2001  Simon Garner
 *
 *
 * Additional development:
 *
 * + UA HLStats Team
 * + http://www.unitedadmins.com
 * + 2004 - 2007
 *
 *
 *
 * Current development:
 *
 * + Johannes 'Banana' Keßler
 * + http://hlstats.sourceforge.net
 * + 2007 - 2010
 *
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
 *
 */

/**
 * all information about a player is handled with this class
 * @package HLStats
 */
class Player {
	/**
	 * the player id
	 * @var int The player id
	 */
	public $playerId = 0;

	/**
	 * the game
	 * need for player lookup via uniqueid
	 *
	 * @var string The game code
	 */
	private $_game = false;

	/**
	 * the player data
	 * non empty if successfull
	 * @var array The playerData
	 *
	 */
	private $_playerData = false;

	/**
	 * the options
	 *
	 * @var array The options needed for this class
	 */
	private $_option = array();

	/**
	 * fields which has the data from an input
	 * eg. used at player details update
	 *
	 * @var array $_saveFields
	 */
	private $_saveFields = array();

	/**
	 * load the player id
	 *
	 * @param int $id The player id
	 * @param string $mode If the player lookup is via playerId oder uniqueId
	 * @param string $game The game code
	 *
	 * @return boolean $ret Either true or false
	 */
	public function __construct($id,$mode,$game=false) {
		$ret = false;

		if(!empty($id)) {
			$this->_game = $game;

			if($mode === false) {
				$this->playerId = $id;
			}
			elseif($mode === true && !empty($game)) {

				$this->_lookupPlayerIdFromUniqeId($id);
				if(empty($this->playerId)) {
					throw new Exception("PlayerID can't be looked up via uniqueid in Player.class");
				}
			}
			else {
				throw new Exception("Player mode is missing for Player.class");
			}
		}
		else {
			throw new Exception("Player ID or game is missing for Player.class");
		}

		$ret = $this->_load();

		// set some default values
		$this->setOption('page',1);

		return $ret;
	}

	/**
	 * return given param from player
	 *
	 * @param string $param The information key to get
	 *
	 * @return mixed Either false or the value for the given key
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
	 * set the given option to the given value
	 *
	 * @param string $key The key for this option
	 * @param string $value The value for the given key
	 *
	 * @return void
	 */
	public function setOption($key,$value) {
		if(!empty($key)) {
			$this->_option[$key] = $value;
		}
	}

	/**
	 * return for the given key the value
	 *
	 * @param string $key The key for the wanted value
	 *
	 * @return string The valuefor given key
	 */
	public function getOption($key) {
		$ret = false;

		if(isset($this->_option[$key])) {
			$ret = $this->_option[$key];
		}

		return $ret;
	}

	/**
	 * get the player history for the events
	 * I know this is big, but I don't think there is a better way.
	 *
	 * @return array The history
	 */
	public function getEventHistory() {
		$ret = false;

		$queryStr = "SELECT SQL_CALC_FOUND_ROWS
					'".l('Team Bonus')."' AS eventType,
					eventTime,
					CONCAT('".l('My team received a points bonus of')." ', bonus, ' ".l('for triggering')." \"', ".DB_PREFIX."_Actions.description, '\"') AS eventDesc,
					".DB_PREFIX."_Servers.name AS serverName,
					map
					FROM ".DB_PREFIX."_Events_TeamBonuses AS t
					LEFT JOIN ".DB_PREFIX."_Actions ON
						t.actionId = ".DB_PREFIX."_Actions.id
					LEFT JOIN ".DB_PREFIX."_Servers ON
						".DB_PREFIX."_Servers.serverId = t.serverId
					WHERE
						t.playerId=".mysql_escape_string($this->playerId)."";
		$queryStr .= " UNION ALL
			SELECT '".l('Connect')."' AS eventType,
				eventTime,
				CONCAT('".l('I connected to the server')."') AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_Connects AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Disconnect')."' AS eventType,
				eventTime,
				'".l('I left the game')."' AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_Disconnects AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT 'Entry' AS eventType,
				eventTime,
				'".l('I entered the game')."' AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_Entries AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Kill')."' As eventType,
			eventTime,
			CONCAT('".l('I killed')." <a href=\"index.php?mode=playerinfo&player=', victimId, '\">', ".DB_PREFIX."_Players.lastName, '</a>', ' ".l('with')." ', weapon) AS eventDesc,
			".DB_PREFIX."_Servers.name AS serverName,
			map
		FROM ".DB_PREFIX."_Events_Frags AS t
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId = t.serverId
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.playerId = t.victimId
		WHERE
			t.killerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Death')."' AS eventType,
				eventTime,
				CONCAT('<a href=\"index.php?mode=playerinfo&player=', killerId, '\">', ".DB_PREFIX."_Players.lastName, '</a>', ' ".l('killed me with')." ', weapon) AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_Frags AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			LEFT JOIN ".DB_PREFIX."_Players On
				".DB_PREFIX."_Players.playerId = t.killerId
			WHERE
				t.victimId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Team Kill')."' AS eventType,
				eventTime,
				CONCAT('".l('I killed teammate')." <a href=\"index.php?mode=playerinfo&player=', victimId, '\">', ".DB_PREFIX."_Players.lastName, '</a>', ' ".l('with')." ', weapon) AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_Teamkills AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			LEFT JOIN ".DB_PREFIX."_Players On
				".DB_PREFIX."_Players.playerId = t.victimId
			WHERE
				t.killerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Friendly Fire')."' AS eventType,
				eventTime,
				CONCAT('".l('My teammate')." <a href=\"index.php?mode=playerinfo&player=', killerId, '\">', ".DB_PREFIX."_Players.lastName, '</a>', ' ".l('killed me with')." ', weapon) AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_Teamkills AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			LEFT JOIN ".DB_PREFIX."_Players On
				".DB_PREFIX."_Players.playerId = t.killerId
			WHERE
				t.victimId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Role')."' AS eventType,
				eventTime,
				CONCAT('".l("I changed role to")." ', role) AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_ChangeRole AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Name')."' AS eventTpe,
				eventTime,
				CONCAT('".l('I changed my name from')." \"', oldName, '\" ".l('to')." \"', newName, '\"') AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_ChangeName AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Action')."' AS eventType,
				eventTime,
				CONCAT('".l('I received a points bonus of')." ', bonus, ' ".l('for triggering')." \"', ".DB_PREFIX."_Actions.description, '\"') AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_PlayerActions AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			LEFT JOIN ".DB_PREFIX."_Actions ON
				".DB_PREFIX."_Actions.id = t.actionId
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Action')."' AS eventType,
				eventTime,
				CONCAT('".l('I received a points bonus of')." ', bonus, ' ".l('for triggering')." \"', ".DB_PREFIX."_Actions.description, '\" ".l('against')." <a href=\"index.php?mode=playerinfo&player=', victimId, '\">', ".DB_PREFIX."_Players.lastName, '</a>') AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_PlayerPlayerActions AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			LEFT JOIN ".DB_PREFIX."_Actions ON
				".DB_PREFIX."_Actions.id = t.actionId
			LEFT JOIN ".DB_PREFIX."_Players ON
				".DB_PREFIX."_Players.playerId = t.victimId
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Action')."' AS eventType,
				eventTime,
				CONCAT('<a href=\"index.php?mode=playerinfo&player=', t.playerId, '\">', ".DB_PREFIX."_Players.lastName, '</A> ".l('triggered')." \"', ".DB_PREFIX."_Actions.description, '\" ".l('against me')."') AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_PlayerPlayerActions AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			LEFT JOIN ".DB_PREFIX."_Actions ON
				".DB_PREFIX."_Actions.id = t.actionId
			LEFT JOIN ".DB_PREFIX."_Players ON
				".DB_PREFIX."_Players.playerId = t.playerId
			WHERE
				t.victimId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Suicide')."' AS eventType,
				eventTime,
				CONCAT('".l('I committed suicide with')." \"', weapon, '\"') AS eventDesc,
				".DB_PREFIX."_Servers.name AS servername,
				map
			FROM ".DB_PREFIX."_Events_Suicides AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";
		$queryStr .= " UNION ALL
			SELECT '".l('Team')."' AS eventType,
				eventTime,
				IF(".DB_PREFIX."_Teams.name IS NULL,
					CONCAT('".l('I joined team')." \"', team, '\"'),
					CONCAT('".l('I joined team')." \"', team, '\" (', ".DB_PREFIX."_Teams.name, ')')
				) AS eventDesc,
				".DB_PREFIX."_Servers.name AS serverName,
				map
			FROM ".DB_PREFIX."_Events_ChangeTeam AS t
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId = t.serverId
			LEFT JOIN ".DB_PREFIX."_Teams ON
				".DB_PREFIX."_Teams.code = t.team
			WHERE
				t.playerId=".mysql_escape_string($this->playerId)."
		";

		$queryStr .= " ORDER BY ";
		if(!empty($this->_option['sort']) && !empty($this->_option['sortorder'])) {
			$queryStr .= " ".$this->_option['sort']." ".$this->_option['sortorder']."";
		}

		if($this->_option['page'] === 1) {
			$queryStr .= " LIMIT 0,50";
		}
		else {
			$start = 50*($this->_option['page']-1);
			$queryStr .= " LIMIT ".$start.",50";
		}

		$query = mysql_query($queryStr);
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$ret['data'][] = $result;
			}
		}

		// get the max count for pagination
		$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
		$result = mysql_fetch_assoc($query);
		$ret['pages'] = (int)ceil($result['rows']/50);
		return $ret;
	}

	/**
	 * get the player Chat historydata if we have any
	 *
	 * @return array
	 */
	public function getChatHistory() {
		$ret = array('data' => array(),
					'pages' => false);

		$queryStr = "SELECT SQL_CALC_FOUND_ROWS
	 			".DB_PREFIX."_Events_Chat.eventTime,
	 			CONCAT('".l('I said')." \"', ".DB_PREFIX."_Events_Chat.message, '\"') AS message,
	 			".DB_PREFIX."_Servers.name,
	 			".DB_PREFIX."_Events_Chat.map
				FROM ".DB_PREFIX."_Events_Chat
				LEFT JOIN ".DB_PREFIX."_Servers ON
	 			".DB_PREFIX."_Servers.serverId = ".DB_PREFIX."_Events_Chat.serverId
			WHERE ".DB_PREFIX."_Events_Chat.playerId=".mysql_escape_string($this->playerId)."";

		if($this->_option['page'] === 1) {
			$queryStr .= " LIMIT 0,50";
		}
		else {
			$start = 50*($this->_option['page']-1);
			$queryStr .= " LIMIT ".$start.",50";
		}

		$query = mysql_query($queryStr);
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$ret['data'][] = $result;
			}
		}

		//get data for pagination
		$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
		$result = mysql_fetch_assoc($query);
		$ret['pages'] = (int)ceil($result['rows']/50);

		return $ret;
	}

	/**
	 * load the full information needed for player info page
	 *
	 * @return void
	 */
	public function loadFullInformation() {
		// load additional stuff and save it into the _playerData array
		$this->_getUniqueIds();
		$this->_getLastConnect();
		$this->_getMaxConnectTime();
		$this->_getAvgPing();
		$this->_getTeamkills();
		$this->_getWeaponaccuracy();
		$this->_getAliasTable();
		$this->_getActions();
		$this->_getPlayerPlayerActions();
		$this->_getTeamSelection();
		$this->_getWeaponUsage();
		$this->_getWeaponStats();
		$this->_getWeaponTarget();
		$this->_getMaps();
		$this->_getPlayerKillStats();
		$this->_getRoleSelection();

		$this->_getRank('rankPoints');
	}

	/**
	 * get the playe time for this player per day
	 * @todo: to complete
	 *
	 * @return array The playerTime data for the chart
	 */
	public function getPlaytimePerDayData() {
		$ret = false;
		$query = mysql_query("SELECT ".DB_PREFIX."_Events_StatsmeTime.*,
				TIME_TO_SEC(".DB_PREFIX."_Events_StatsmeTime.time) as tTime
			FROM ".DB_PREFIX."_Events_StatsmeTime
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_StatsmeTime.serverId
			WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
				AND playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$ret[] = $result;
			}
		}

		return $ret;
	}

	/**
	 * get the kills per day
	 *
	 * @return $ret array
	 */
	public function getKillsPerDay() {
		$ret = false;

		// the extract function does not support year_month_day.....
		$query = mysql_query("SELECT COUNT(*) AS dayEvents,
							 CONCAT(EXTRACT(YEAR FROM `eventTime`),'-',EXTRACT(MONTH FROM `eventTime`),'-',EXTRACT(DAY FROM `eventTime`)) AS eventDay
							 FROM `".DB_PREFIX."_Events_Frags`
							 WHERE `killerId` = '".mysql_escape_string($this->playerId)."'
							 GROUP BY eventDay
							 ORDER BY eventTime");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$ret[] = $result;
			}
		}

		return $ret;
	}

	/**
	 * this is used to check if we have missing fields in an input
	 * @param array $params
	 * @return array $ret
	 */
	public function checkFields($params) {
		$ret = false;

		$missing = array();
		$this->_saveFields = array();
		if(!empty($params)) {
			foreach ($params as $k=>$v) {
				$v = trim($v);

				// check if we have a req_key
				if(strstr($k,'req_')) {
					$newKey = str_replace('req_','',$k);
					if($v !== "") {
						$this->_saveFields[$newKey] = $v;
					}
					else {
						$missing[] = $newKey; // is missing
					}
				}
				else {
					$this->_saveFields[$k] = $v;
				}
			}
		}

		if(!empty($missing)) {
			$ret = $missing;
		}
		else {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * update the current loaded player with the data from $_saveFields
	 * if empty $_saveFields to nothin
	 * @return boolean $ret
	 */
	public function updatePlayerProfile() {
		$ret = false;

		// we do not have to update anything
		if(empty($this->_saveFields)) return true;

		// check if we have to reset or delete from clan
		$deleteFromClan = false;
		if(isset($this->_saveFields['deletefromclan']) && $this->_saveFields['deletefromclan'] == "1") {
			$deleteFromClan = true;
		}
		unset($this->_saveFields['deletefromclan']);
		$resetStats = false;
		if(isset($this->_saveFields['resetstats']) && $this->_saveFields['resetstats'] == "1") {
			$resetStats = true;
		}
		unset($this->_saveFields['resetstats']);


		if(!empty($this->playerId)) {
			$queryStr = "UPDATE `".DB_PREFIX."_Players` SET";

			foreach($this->_saveFields as $k=>$v) {
				$queryStr .= " `".$k."` = '".mysql_escape_string($v)."',";
			}
			$queryStr = trim($queryStr,",");

			$queryStr .= " WHERE `playerId` = '".mysql_escape_string($this->playerId)."'";

			$run = mysql_query($queryStr);
			if($run !== false) {
				$ret = true;
			}
		}

		return $ret;
	}

	/**
	 * load the player data from db
	 *
	 * @return void
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
					".DB_PREFIX."_Players.myspace,
					".DB_PREFIX."_Players.facebook,
					".DB_PREFIX."_Players.jabber,
					".DB_PREFIX."_Players.steamprofile,
					".DB_PREFIX."_Players.game,
					".DB_PREFIX."_Players.skill,
					".DB_PREFIX."_Players.oldSkill,
					".DB_PREFIX."_Players.kills,
					".DB_PREFIX."_Players.deaths,
					".DB_PREFIX."_Players.hideranking,
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
				if(empty($this->_game)) {
					$this->_game = $result['game'];
				}
			}
		}
	}

	/**
	 * get the playerId via the player uniqueid
	 * the game is also needed !
	 *
	 * @param string $id The player unique id string
	 *
	 * @return boolean true or false
	 */
	private function _lookupPlayerIdFromUniqeId($id) {
		$ret = false;

		$query = mysql_query("SELECT playerId FROM ".DB_PREFIX."_PlayerUniqueIds
					WHERE uniqueId='".mysql_escape_string($id)."'
						AND game='".mysql_escape_string($this->_game)."'");

		if(mysql_num_rows($query) > 0) {
			$result = mysql_fetch_assoc($query);
			$this->playerId = $result['playerId'];

			$ret = true;
		}

		return $ret;
	}

	/**
	 * get the player uniqueids if any
	 *
	 * @return void
	 */
	private function _getUniqueIds() {
		$this->_playerData['uniqueIds'] = '-';
		$query = mysql_query("SELECT uniqueId
						FROM ".DB_PREFIX."_PlayerUniqueIds
						WHERE playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 0) {
			while ($result = mysql_fetch_assoc($query)) {

				if(strstr($result['uniqueId'],'STEAM_')) {
					$result['uniqueId'] = getSteamProfileUrl($result['uniqueId']);
				}
				$ret = $result['uniqueId'].", ";
			}
			$this->_playerData['uniqueIds'] = trim($ret,', ');
			mysql_free_result($query);
		}
	}

	/**
	 * get the last connect from connect table
	 *
	 * @return void
	 */
	private function _getLastConnect() {
		$this->_playerData['lastConnect'] = l('No info');
		$query = mysql_query("SELECT MAX(eventTime) AS eventTime
					FROM ".DB_PREFIX."_Events_Connects
					WHERE playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 0) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['lastConnect'] = $result['eventTime'];
			mysql_free_result($query);
		}
	}

	/**
	 * get the max connection time
	 * if we have the information
	 *
	 * @return void
	 */
	private function _getMaxConnectTime() {
		$this->_playerData['maxTime'] = l('No info');
		$query = mysql_query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(time))) AS tTime
					FROM ".DB_PREFIX."_Events_StatsmeTime
					WHERE playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 0) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['maxTime'] = $result['tTime'];
			mysql_free_result($query);
		}
	}

	/**
	 * get the average ping
	 * if we have the information
	 *
	 * @return void
	 */
	private function _getAvgPing() {
		$this->_playerData['avgPing'] = l('No info');
		$query = mysql_query("SELECT ROUND(SUM(ping) / COUNT(ping), 1) AS av_ping
					FROM ".DB_PREFIX."_Events_StatsmeLatency
					WHERE playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 0) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['avgPing'] = $result['av_ping'];
			mysql_free_result($query);
		}
	}

	/**
	 * get the rank by given ORDER
	 *
	 * @param $mode string The mode on which order the rank is based
	 *
	 * @return void
	 */
	private function _getRank($mode) {
		switch($mode) {
			case 'rankPoints':
			default:
				$query = mysql_query("SELECT count(*) AS rank
							FROM ".DB_PREFIX."_Players
							WHERE active = '1'
								AND hideranking = '0'
								AND kills >= '1'
								AND game = '".mysql_escape_string($this->_game)."'
								AND skill >
							(SELECT skill FROM ".DB_PREFIX."_Players
								WHERE playerId = '".mysql_escape_string($this->playerId)."')");
		}
		if(mysql_num_rows($query) > 0) {
			$result = mysql_fetch_assoc($query);
			// the result gives the rows which are "above" the searched row
			// we have to add 1 to get the position
			$this->_playerData[$mode] = $result['rank']+1;
			mysql_free_result($query);
		}
	}

	/**
	 * get the teamkills for this player and game
	 *
	 * @return void
	 */
	private function _getTeamkills() {
		$this->_playerData['teamkills'] = l('No info');
		$query = mysql_query("SELECT COUNT(*) tk
				FROM ".DB_PREFIX."_Events_Teamkills
				LEFT JOIN ".DB_PREFIX."_Servers ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Teamkills.serverId
				WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
					AND killerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 0) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['teamkills'] = $result['tk'];
		}
	}

	/**
	 * get the weapon accuracy
	 * if we have the info
	 *
	 * @return void
	 */
	private function _getWeaponaccuracy() {
		$this->_playerData['accuracy'] = l('No info');
		$query = mysql_query("SELECT
					IFNULL(ROUND((SUM(".DB_PREFIX."_Events_Statsme.hits)
						/ SUM(".DB_PREFIX."_Events_Statsme.shots) * 100), 1), 0.0) AS accuracy
					FROM ".DB_PREFIX."_Events_Statsme
				LEFT JOIN ".DB_PREFIX."_Servers ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Statsme.serverId
				WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
					AND playerId='".mysql_escape_string($this->playerId)."'");
		if(mysql_num_rows($query) > 0) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['accuracy'] = $result['accuracy'];
			mysql_free_result($query);
		}
	}

	/**
	 * get the last 10 aliases
	 *
	 * @return void
	 */
	private function _getAliasTable() {
		$this->_playerData['aliases'] = array();
		$query = mysql_query("SELECT name, lastuse, numuses, kills,
								  deaths, IFNULL(kills / deaths,'-') AS kpd,suicides
							  FROM ".DB_PREFIX."_PlayerNames
							  WHERE playerId='".mysql_escape_string($this->playerId)."'
							  ORDER BY lastuse DESC
							  LIMIT 10");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['aliases'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the player action table
	 *
	 * @return void
	 */
	private function _getActions() {
		$this->_playerData['actions'] = array();
		$query = mysql_query("SELECT ".DB_PREFIX."_Actions.description,
						COUNT(".DB_PREFIX."_Events_PlayerActions.id) AS obj_count,
						COUNT(".DB_PREFIX."_Events_PlayerActions.id) * ".DB_PREFIX."_Actions.reward_player AS obj_bonus
					FROM ".DB_PREFIX."_Actions
					LEFT JOIN ".DB_PREFIX."_Events_PlayerActions ON
						".DB_PREFIX."_Events_PlayerActions.actionId = ".DB_PREFIX."_Actions.id
					LEFT JOIN ".DB_PREFIX."_Servers ON
						".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_PlayerActions.serverId
					WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
						AND ".DB_PREFIX."_Events_PlayerActions.playerId=".mysql_escape_string($this->playerId)."
					GROUP BY ".DB_PREFIX."_Actions.id
					ORDER BY obj_count DESC");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['actions'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the player player actions
	 *
	 * @return void
	 */
	private function _getPlayerPlayerActions() {
		$this->_playerData['playerPlayerActions'] = array();
		$query = mysql_query("SELECT ".DB_PREFIX."_Actions.description,
						COUNT(".DB_PREFIX."_Events_PlayerPlayerActions.id) AS obj_count,
						COUNT(".DB_PREFIX."_Events_PlayerPlayerActions.id) * ".DB_PREFIX."_Actions.reward_player AS obj_bonus
					FROM ".DB_PREFIX."_Actions
					LEFT JOIN ".DB_PREFIX."_Events_PlayerPlayerActions ON
						".DB_PREFIX."_Events_PlayerPlayerActions.actionId = ".DB_PREFIX."_Actions.id
					LEFT JOIN ".DB_PREFIX."_Servers ON
						".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_PlayerPlayerActions.serverId
					WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
						AND ".DB_PREFIX."_Events_PlayerPlayerActions.playerId=".mysql_escape_string($this->playerId)."
					GROUP BY ".DB_PREFIX."_Actions.id
					ORDER BY obj_count DESC");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['playerPlayerActions'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get how much and which team the player was in
	 *
	 * @return void
	 */
	private function _getTeamSelection() {
		$this->_playerData['teamSelection'] = array();

		$queryTjoins = mysql_query("SELECT COUNT(*) AS tj
							FROM ".DB_PREFIX."_Events_ChangeTeam
							WHERE playerId=".mysql_escape_string($this->playerId)."");
		$result = mysql_fetch_assoc($queryTjoins);
		$numteamjoins = $result['tj'];

		$query = mysql_query("SELECT IFNULL(".DB_PREFIX."_Teams.name, ".DB_PREFIX."_Events_ChangeTeam.team) AS name,
					COUNT(".DB_PREFIX."_Events_ChangeTeam.id) AS teamcount,
					COUNT(".DB_PREFIX."_Events_ChangeTeam.id) / $numteamjoins * 100 AS percent
				FROM ".DB_PREFIX."_Events_ChangeTeam
				LEFT JOIN ".DB_PREFIX."_Teams ON
					".DB_PREFIX."_Events_ChangeTeam.team=".DB_PREFIX."_Teams.code
				LEFT JOIN ".DB_PREFIX."_Servers ON
					".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_ChangeTeam.serverId
				WHERE ".DB_PREFIX."_Teams.game='".mysql_escape_string($this->_game)."'
					AND ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
					AND ".DB_PREFIX."_Events_ChangeTeam.playerId=".mysql_escape_string($this->playerId)."
					AND (hidden <>'1' OR hidden IS NULL)
				GROUP BY ".DB_PREFIX."_Events_ChangeTeam.team
				ORDER BY teamcount DESC");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['teamSelection'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the weapon usage for the current player
	 *
	 * @return void
	 */
	private function _getweaponUsage() {
		$this->_playerData['weaponUsage'] = array();
		$query = mysql_query("SELECT ".DB_PREFIX."_Events_Frags.weapon,
						".DB_PREFIX."_Weapons.name,
						IFNULL(".DB_PREFIX."_Weapons.modifier, 1.00) AS modifier,
						COUNT(".DB_PREFIX."_Events_Frags.weapon) AS kills,
						COUNT(".DB_PREFIX."_Events_Frags.weapon) / ".$this->_playerData['kills']." * 100 AS percent
					FROM ".DB_PREFIX."_Events_Frags
						LEFT JOIN ".DB_PREFIX."_Weapons ON
							".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Frags.weapon
						LEFT JOIN ".DB_PREFIX."_Servers ON
							".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
					WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
						AND ".DB_PREFIX."_Events_Frags.killerId='".mysql_escape_string($this->playerId)."'
						AND (".DB_PREFIX."_Weapons.game='".mysql_escape_string($this->_game)."' OR ".DB_PREFIX."_Weapons.weaponId IS NULL)
					GROUP BY ".DB_PREFIX."_Events_Frags.weapon
					ORDER BY kills DESC");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['weaponUsage'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the weapon stats
	 * if we have the info in the db
	 *
	 * @return void
	 */
	private function _getWeaponStats() {
		$this->_playerData['weaponStats'] = array();
		$query = mysql_query("SELECT ".DB_PREFIX."_Events_Statsme.weapon AS smweapon,
					".DB_PREFIX."_Weapons.name,
					SUM(".DB_PREFIX."_Events_Statsme.kills) AS smkills,
					SUM(".DB_PREFIX."_Events_Statsme.hits) AS smhits,
					SUM(".DB_PREFIX."_Events_Statsme.shots) AS smshots,
					SUM(".DB_PREFIX."_Events_Statsme.headshots) AS smheadshots,
					SUM(".DB_PREFIX."_Events_Statsme.deaths) AS smdeaths,
					SUM(".DB_PREFIX."_Events_Statsme.damage) AS smdamage,
					IFNULL(
						(
							(
								SUM(".DB_PREFIX."_Events_Statsme.damage) / SUM(".DB_PREFIX."_Events_Statsme.hits)
							)
						), '-'
					) as smdhr,
					SUM(".DB_PREFIX."_Events_Statsme.kills)
						/
						IF(
							(
							SUM(".DB_PREFIX."_Events_Statsme.deaths)=0
							), 1,
							(SUM(".DB_PREFIX."_Events_Statsme.deaths))
						) as smkdr,
					(SUM(".DB_PREFIX."_Events_Statsme.hits) / SUM(".DB_PREFIX."_Events_Statsme.shots) * 100) as smaccuracy,
					IFNULL(((SUM(".DB_PREFIX."_Events_Statsme.shots) / SUM(".DB_PREFIX."_Events_Statsme.kills))), '-') as smspk
				FROM ".DB_PREFIX."_Events_Statsme
					LEFT JOIN ".DB_PREFIX."_Servers ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Statsme.serverId
					LEFT JOIN ".DB_PREFIX."_Weapons ON ".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Statsme.weapon
				WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
					AND ".DB_PREFIX."_Events_Statsme.PlayerId=".mysql_escape_string($this->playerId)."
				GROUP BY ".DB_PREFIX."_Events_Statsme.weapon
				ORDER BY smaccuracy DESC");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['weaponStats'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the weapon target
	 * if we have the information in db
	 *
	 * @return void
	 */
	private function _getWeaponTarget() {
		$this->_playerData['weaponTarget'] = array();
		$query = mysql_query("SELECT ".DB_PREFIX."_Events_Statsme2.weapon AS smweapon,
					".DB_PREFIX."_Weapons.name,
					SUM(".DB_PREFIX."_Events_Statsme2.head) AS smhead,
					SUM(".DB_PREFIX."_Events_Statsme2.chest) AS smchest,
					SUM(".DB_PREFIX."_Events_Statsme2.stomach) AS smstomach,
					SUM(".DB_PREFIX."_Events_Statsme2.leftarm) AS smleftarm,
					SUM(".DB_PREFIX."_Events_Statsme2.rightarm) AS smrightarm,
					SUM(".DB_PREFIX."_Events_Statsme2.leftleg) AS smleftleg,
					SUM(".DB_PREFIX."_Events_Statsme2.rightleg) AS smrightleg
				FROM ".DB_PREFIX."_Events_Statsme2
				LEFT JOIN ".DB_PREFIX."_Servers ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Statsme2.serverId
				LEFT JOIN ".DB_PREFIX."_Weapons ON ".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Statsme2.weapon
				WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
					AND ".DB_PREFIX."_Events_Statsme2.PlayerId=".mysql_escape_string($this->playerId)."
				GROUP BY ".DB_PREFIX."_Events_Statsme2.weapon
				ORDER BY smhead DESC, smweapon DESC");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['weaponTarget'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the map performance
	 *
	 * @return void
	 */
	private function _getMaps() {
		$this->_playerData['maps'] = array();

		$query = mysql_query("SELECT IF(map='', '(Unaccounted)', map) AS map,
			SUM(killerId=".mysql_escape_string($this->playerId).") AS kills,
			SUM(victimId=".mysql_escape_string($this->playerId).") AS deaths,
			IFNULL(SUM(killerId=".mysql_escape_string($this->playerId).") / SUM(victimId=".mysql_escape_string($this->playerId)."), '-') AS kpd,
			CONCAT(SUM(killerId=".mysql_escape_string($this->playerId).")) / ".mysql_escape_string($this->_playerData['kills'])." * 100 AS percentage
		FROM ".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
		WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."' AND killerId='".mysql_escape_string($this->playerId)."'
			OR victimId='".mysql_escape_string($this->playerId)."'
		GROUP BY map
		ORDER BY kills DESC, percentage DESC");

		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['maps'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the kill stats table
	 *
	 * @return void
	 */
	private function _getPlayerKillStats() {
		$this->_playerData['killstats'] = array();

		//there might be a better way to do this, but I could not figure one out.
		mysql_query("DROP TABLE IF EXISTS ".DB_PREFIX."_".$this->playerId."_Frags_Kills");
		mysql_query("CREATE TEMPORARY TABLE ".DB_PREFIX."_".$this->playerId."_Frags_Kills
						(playerId INT(10),kills INT(10),deaths INT(10)) DEFAULT CHARSET=utf8");
		mysql_query("INSERT INTO ".DB_PREFIX."_".$this->playerId."_Frags_Kills
						(playerId,kills)
					   SELECT victimId, killerId
					   FROM ".DB_PREFIX."_Events_Frags
					   LEFT JOIN ".DB_PREFIX."_Servers ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
					   WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
						   AND killerId = ".mysql_escape_string($this->playerId)."");

		mysql_query("INSERT INTO ".DB_PREFIX."_".$this->playerId."_Frags_Kills (playerId,deaths)
						SELECT killerId,victimId
						FROM ".DB_PREFIX."_Events_Frags
						LEFT JOIN ".DB_PREFIX."_Servers ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
					WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
						AND victimId = ".mysql_escape_string($this->playerId)."
		");

		$query = mysql_query("SELECT ".DB_PREFIX."_Players.lastName AS name,
					".DB_PREFIX."_Players.active,
					".DB_PREFIX."_Players.playerId,
					Count(".DB_PREFIX."_".$this->playerId."_Frags_Kills.kills) AS kills,
					Count(".DB_PREFIX."_".$this->playerId."_Frags_Kills.deaths) AS deaths,
					".DB_PREFIX."_".$this->playerId."_Frags_Kills.playerId as victimId,
					IFNULL(Count(".DB_PREFIX."_".$this->playerId."_Frags_Kills.kills)/Count(".DB_PREFIX."_".$this->playerId."_Frags_Kills.deaths),
					IFNULL(FORMAT(Count(".DB_PREFIX."_".$this->playerId."_Frags_Kills.kills), 2), '-')) AS kpd
				FROM ".DB_PREFIX."_".$this->playerId."_Frags_Kills
					INNER JOIN ".DB_PREFIX."_Players ON ".DB_PREFIX."_".$this->playerId."_Frags_Kills.playerId = ".DB_PREFIX."_Players.playerId
				WHERE ".DB_PREFIX."_Players.hideranking = 0
				GROUP BY ".DB_PREFIX."_".$this->playerId."_Frags_Kills.playerId
				HAVING Count(".DB_PREFIX."_".$this->playerId."_Frags_Kills.kills) >= ".mysql_escape_string($this->_option['killLimit'])."
				ORDER BY kills DESC, deaths DESC
				LIMIT 10");

		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['killstats'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the rol selection data
	 *
	 * @return void
	 */
	private function _getRoleSelection() {
		$this->_playerData['roleSelection'] = array();

		$queryRoles = mysql_query("SELECT COUNT(*) AS rj FROM
									".DB_PREFIX."_Events_ChangeRole
									WHERE playerId=".mysql_escape_string($this->playerId)."");
		$result = mysql_fetch_assoc($queryRoles);
		$numrolejoins = $result['rj'];
		mysql_free_result($queryRoles);
		if(!empty($numrolejoins)) {
			$query = mysql_query("
					SELECT
						IFNULL(".DB_PREFIX."_Roles.name, ".DB_PREFIX."_Events_ChangeRole.role) AS name,
						COUNT(".DB_PREFIX."_Events_ChangeRole.id) AS rolecount,
						COUNT(".DB_PREFIX."_Events_ChangeRole.id) / $numrolejoins * 100 AS percent,
						".DB_PREFIX."_Roles.code AS rolecode
					FROM
						".DB_PREFIX."_Events_ChangeRole
					LEFT JOIN ".DB_PREFIX."_Roles ON
						".DB_PREFIX."_Events_ChangeRole.role=".DB_PREFIX."_Roles.code
					LEFT JOIN ".DB_PREFIX."_Servers ON
						".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_ChangeRole.serverId
					WHERE
						".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
						AND ".DB_PREFIX."_Events_ChangeRole.playerId=".mysql_escape_string($this->playerId)."
						AND (hidden <>'1' OR hidden IS NULL)
					GROUP BY
						".DB_PREFIX."_Events_ChangeRole.role
					ORDER BY `name`
					LIMIT 10");
		}

	}
}

?>
