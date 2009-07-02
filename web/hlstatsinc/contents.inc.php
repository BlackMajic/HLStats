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

$queryAllGames = mysql_query("SELECT code,name FROM ".DB_PREFIX."_Games WHERE hidden='0' ORDER BY name ASC");

$num_games = mysql_num_rows($queryAllGames);
if(!empty($_GET['game'])) {
	if(validateInput($_GET['game'],'nospace')) {
		$query = mysql_query("SELECT code,name FROM ".DB_PREFIX."_Games WHERE hidden='0'");
		$result = mysql_fetch_assoc($query);
		if(!empty($result)) {
			$game = $result['code'];
			$gamename = $result['name'];
			include(INCLUDE_PATH . "/game.inc.php");
		}
		else {
			error("No such game.");
		}
	}
	else {
		die('Wrong input');
	}
}
elseif ($num_games == 1) {
	$query = mysql_query("SELECT code,name FROM ".DB_PREFIX."_Games WHERE hidden='0'");
	$result = mysql_fetch_assoc($query);
	if(!empty($result)) {
		$game = $result['code'];
		$gamename = $result['name'];
		include(INCLUDE_PATH . "/game.inc.php");
	}
	else {
		error("No such game.");
	}
}
else {
	//@todo
	include(INCLUDE_PATH . "/games.inc.php");
}
?>
