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

class Players {

	/**
	 * the current game
	 *
	 * @var string The game
	 */
	private $_game = false;

	/**
	 * the options for queries
	 * @var array The options
	 */
	private $_option = array();

	/**
	 * set some vars and the game
	 * game check is already done
	 *
	 * @param string $game The current game
	 */
	public function __construct($game) {
		if(!empty($game)) {
			$this->_game = $game;
		}
		else {
			new Exception("Game is missing for Players.class");
		}

		// set default values
		$this->setOption('page',1);
		$this->setOption('minkills','1');
	}

	/**
	 * set the options
	 *
	 * @param string $key The key/name for this option
	 * @param string $value The value for this option
	 */
	public function setOption($key,$value) {
		$this->_option[$key] = $value;
	}

	public function getOption($key) {
		$ret = false;

		if(isset($this->_option[$key])) {
			$ret = $this->_option[$key];
		}

		return $ret;
	}

	/**
	 * get the players for the current game
	 * for the players overview page
	 */
	public function getPlayersOveriew() {
		$ret['data'] = array();
		$ret['pages'] = false;

		// construct the query with the given options
		$queryStr = "SELECT SQL_CALC_FOUND_ROWS
				t1.playerId,
				t1.lastName,
				t1.skill,
				t1.oldSkill,
				t1.kills,
				t1.deaths,
				t1.active,
				IFNULL(t1.kills/t1.deaths, '-') AS kpd
			FROM
				".DB_PREFIX."_Players as t1";

		// should we hide the bots
		if(defined('HIDE_BOTS') && HIDE_BOTS == "1") {
			$queryStr .= " INNER JOIN ".DB_PREFIX."_PlayerUniqueIds as t2 ON t1.playerId = t2.playerId";
		}

		$queryStr .= " WHERE
				t1.game='".mysql_escape_string($this->_game)."'
				AND t1.hideranking=0
				AND t1.kills >= '".mysql_escape_string($this->_option['minkills'])."'";

		// should we show all the players or not
		if(isset($this->_option['showall']) && $this->_option['showall'] === "1") {
			$queryStr .= " ";
		}
		else {
			$queryStr .= " AND t1.active = '1'";
		}

		// should we hide the bots
		if(defined('HIDE_BOTS') && HIDE_BOTS == "1") {
			$queryStr .= " AND t2.uniqueID not like 'BOT:%'";
		}

		$queryStr .= " ORDER BY ";
		if(!empty($this->_option['sort']) && !empty($this->_option['sortorder'])) {
			$queryStr .= " ".$this->_option['sort']." ".$this->_option['sortorder']."";
		}
		$queryStr .=" ,t1.lastName ASC";

		// calculate the limit
		if($this->_option['page'] === 1) {
			$queryStr .=" LIMIT 0,50";
		}
		else {
			$start = 50*($this->_option['page']-1);
			$queryStr .=" LIMIT ".$start.",50";
		}

		$query = mysql_query($queryStr);
		if(mysql_num_rows($query) > 0) {
			while($result = mysql_fetch_assoc($query)) {
				$result['kpd'] = number_format($result['kpd'],1,'.','');
				$result['lastName'] = makeSavePlayerName($result['lastName']);
				$pl[] = $result;
			}
			$ret['data'] = $pl;
		}

		// get the max count for pagination
		$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
		$result = mysql_fetch_assoc($query);
		$ret['pages'] = (int)ceil($result['rows']/50);

		return $ret;
	}

	/**
	 * get the player count per day
	 *
	 * @return array An array with connect and disconnect data
	 */
	public function getPlayerCountPerDay() {
		$data = array();

		$query = mysql_query("SELECT DATE_FORMAT(`".DB_PREFIX."_Events_Connects`.`eventTime`,'%Y-%m-%d') AS eventTime,
        				`".DB_PREFIX."_Events_Connects`.`playerId`
						FROM `".DB_PREFIX."_Events_Connects`
						LEFT JOIN `".DB_PREFIX."_Players`
							ON `".DB_PREFIX."_Events_Connects`.`playerId` = `".DB_PREFIX."_Players`.`playerId`
						WHERE `".DB_PREFIX."_Players`.`game` = '".mysql_escape_string($this->_game)."'");
		while ($result = mysql_fetch_assoc($query)) {
            // we group by day
            //$dataArr = explode(" ",$result['eventTime']);
        	$data['connect'][$result['eventTime']][] = $result['playerId'];
        }
        mysql_free_result($query);

		$query = mysql_query("SELECT DATE_FORMAT(`".DB_PREFIX."_Events_Disconnects`.`eventTime`,'%Y-%m-%d') AS eventTime,
						`".DB_PREFIX."_Events_Disconnects`.`playerId`
		                FROM `".DB_PREFIX."_Events_Disconnects`
		                LEFT JOIN `".DB_PREFIX."_Players`
		                	ON `".DB_PREFIX."_Events_Disconnects`.`playerId` = `".DB_PREFIX."_Players`.`playerId`
		                WHERE `".DB_PREFIX."_Players`.`game` = '".mysql_escape_string($this->_game)."'");
        while ($result = mysql_fetch_assoc($query)) {
            // we group by day
            //$dataArr = explode(" ",$result['eventTime']);
        	$data['disconnect'][$result['eventTime']][] = $result['playerId'];
        }
        mysql_free_result($query);

		return $data;
	}

	/**
	 * get the most time online
	 *
	 * @return array The data
	 */
	public function getMostTimeOnline() {
		exit();
		$data = array();

		$query = mysql_query("SELECT ".DB_PREFIX."_Events_StatsmeTime.*,
					TIME_TO_SEC(".DB_PREFIX."_Events_StatsmeTime.time) as tTime
					FROM ".DB_PREFIX."_Events_StatsmeTime
					LEFT JOIN ".DB_PREFIX."_Servers
						ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_StatsmeTime.serverId
					WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'");
		var_dump("SELECT ".DB_PREFIX."_Events_StatsmeTime.*,
					TIME_TO_SEC(".DB_PREFIX."_Events_StatsmeTime.time) as tTime
					FROM ".DB_PREFIX."_Events_StatsmeTime
					LEFT JOIN ".DB_PREFIX."_Servers
						ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_StatsmeTime.serverId
					WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($this->_game)."'");

		while($result = mysql_fetch_assoc($query)) {
			$onlineArr[$result['playerId']][] = $result;
		}
		mysql_free_result($query);
	}
}
?>
