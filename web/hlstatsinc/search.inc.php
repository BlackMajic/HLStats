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

require(INCLUDE_PATH . "/search-class.inc.php");

pageHeader(
	array(l("Search")),
	array(l("Search")=>"")
);

$sr_query = '';
$sr_type = 'player';
$sr_game = '';

if(!empty($_GET["q"])) {
	$sr_query = sanitize($_GET["q"]);
}

if(!empty($_GET["q"])) {
	if(validateInput($_GET["st"],'nospace') === true) {
		$sr_type = $_GET["st"];
	}
}

if(!empty($_GET["game"])) {
	if(validateInput($_GET["game"],'nospace') === true) {
		$sr_game = $_GET["game"];
	}
}

$search = new Search($sr_query, $sr_type, $sr_game);

$search->drawForm(array("mode"=>"search"));
if ($sr_query || $sr_query == "0") $search->drawResults();
?>
