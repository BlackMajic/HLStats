<?php
/**
 * system class file. used in admin area for common stuff
 * @package HLStats
 * @author Johannes 'Banana' Keßler
 */


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

/**
 * the main system class. Used at admin area
 * @author banana
 * @package HLStats
 *
 */
class System {
	/**
	 * stores the key=>value data validated inputs
	 * @var $_saveFields array
	 */
	private $_saveFields = false;

	/**
	 * set some vars
	 */
	public function __construct() {
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
}
?>