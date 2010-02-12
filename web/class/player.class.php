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
	 * the options
	 * @var array
	 */
	private $_option = array();

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
	 * set the given option to the given value
	 */
	public function setOption($key,$value) {
		if(!empty($key)) {
			$this->_option[$key] = $value;
		}
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
	}

	/**
	 * load the full information needed for player info page
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

		$this->_getRank('rankPoints');
	}

	public function getPlaytimePerDayData() {
		exit('todo');
		$ret = false;
		$query = mysql_query("SELECT ".DB_PREFIX."_Events_StatsmeTime.*,
				TIME_TO_SEC(".DB_PREFIX."_Events_StatsmeTime.time) as tTime
			FROM ".DB_PREFIX."_Events_StatsmeTime
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_StatsmeTime.serverId
			WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
				AND playerId='".mysql_escape_string($this->playerId)."'");
		while($result = mysql_fetch_assoc($query)) {
			$ret[] = $result;
		}


		return $ret;
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

		if(mysql_num_rows($query) > 0) {
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
		if(mysql_num_rows($query) > 0) {
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
		if(mysql_num_rows($query) > 0) {
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
		if(mysql_num_rows($query) > 0) {
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
		if(mysql_num_rows($query) > 0) {
			$result = mysql_fetch_assoc($query);
			$this->_playerData['avgPing'] = $result['av_ping'];
			mysql_free_result($query);
		}
	}

	/**
	 * get the rank by given ORDER
	 * @param $mode string The mode on which order the rank is based
	 */
	private function _getRank($mode) {
		switch($mode) {
			case 'rankPoints':
			default:
				$query = mysql_query("SELECT count(*) AS rank
							FROM ".DB_PREFIX."_Players
							WHERE active = 1
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
	 * get the weapon stats if we have the info in the db
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
					IFNULL((ROUND((SUM(".DB_PREFIX."_Events_Statsme.damage) / SUM(".DB_PREFIX."_Events_Statsme.hits)), 1)), '-') as smdhr,
					SUM(".DB_PREFIX."_Events_Statsme.kills) / IF((SUM(".DB_PREFIX."_Events_Statsme.deaths)=0), 1, (SUM(".DB_PREFIX."_Events_Statsme.deaths))) as smkdr,
					ROUND((SUM(".DB_PREFIX."_Events_Statsme.hits) / SUM(".DB_PREFIX."_Events_Statsme.shots) * 100), 1) as smaccuracy,
					IFNULL((ROUND((SUM(".DB_PREFIX."_Events_Statsme.shots) / SUM(".DB_PREFIX."_Events_Statsme.kills)), 1)), '-') as smspk
				FROM ".DB_PREFIX."_Events_Statsme
					LEFT JOIN ".DB_PREFIX."_Servers ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Statsme.serverId
					LEFT JOIN ".DB_PREFIX."_Weapons ON ".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Statsme.weapon
				WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'
					AND ".DB_PREFIX."_Events_Statsme.PlayerId=".mysql_escape_string($this->playerId)."
				GROUP BY ".DB_PREFIX."_Events_Statsme.weapon
				ORDER BY smkdr DESC, smweapon DESC");
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$this->_playerData['weaponStats'][] = $result;
			}
			mysql_free_result($query);
		}
	}

	/**
	 * get the weapon target if we have the information in db
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
	 */
	private function _getMaps() {
		$this->_playerData['maps'] = array();

		$query = mysql_query("SELECT IF(map='', '(Unaccounted)', map) AS map,
			SUM(killerId=".mysql_escape_string($this->playerId).") AS kills,
			SUM(victimId=".mysql_escape_string($this->playerId).") AS deaths,
			IFNULL(SUM(killerId=".mysql_escape_string($this->playerId).") / SUM(victimId=".mysql_escape_string($this->playerId)."), '-') AS kpd,
			ROUND(CONCAT(SUM(killerId=".mysql_escape_string($this->playerId).")) / ".mysql_escape_string($this->_playerData['kills'])." * 100, 2) AS percentage
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
}

?>
