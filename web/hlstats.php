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
/*
if (!get_magic_quotes_gpc()) {
	die("HLStats requires <b>magic_quotes_gpc</b> to be <i>enabled</i>. Check your php.ini or refer to the PHP manual for more information.");
}
*/
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
	error_reporting(8191);
	ini_set('display_errors',false);
}

// load config
require('hlstatsinc/hlstats.conf.php');

/**
 * load required stuff
 * general classes like tablle class
 */
require(INCLUDE_PATH . "/functions.inc.php");
require(INCLUDE_PATH . "/classes.inc.php");


// set utf-8 header
// we have to save all the stuff with utf-8 to make it work !!
header("Content-type: text/html; charset=UTF-8");

////
//// Initialisation
////

define("VERSION", "development version");

$db_con = mysql_connect(DB_ADDR,DB_USER,DB_PASS);
$db_sel = mysql_select_db(DB_NAME,$db_con);

/**
 * load the options
 */
$g_options = array();
$g_options = getOptions();

if(empty($g_options)) {
	error('Failed to load options.');
}

// set scripturl if not set in options
if(empty($g_options['scripturl'])) {
	$g_options["scripturl"] = 'hlstats.php';
}


////
//// Main
////
$modes = array(
	"players",
	"clans",
	"weapons",
	"maps",
	"actions",
	"claninfo",
	"playerinfo",
	"weaponinfo",
	"mapinfo",
	"actioninfo",
	"playerhistory",
	"search",
	"admin",
	"help",
	"livestats",
	"playerchathistory"
);

$mode = 'contents';
if(!empty($_GET["mode"])) {
	if(in_array($_GET["mode"], $modes) && validateInput($_GET['mode'],'nospace') === true ) {
		$mode = $_GET['mode'];
	}
}

// process the logout
if(!empty($_GET['logout'])) {
	if(validateInput($_GET['logout'],'digit') === true && $_GET['logout'] == "1") {
		// destroy session and cookie

		setcookie("authusername", '', mktime(12,0,0,1, 1, 1990));
		setcookie("authpassword", '', mktime(12,0,0,1, 1, 1990));
		setcookie("authsavepass", '', mktime(12,0,0,1, 1, 1990));
		setcookie("authsessionStart", '', mktime(12,0,0,1, 1, 1990));

		$_COOKIE = array();
		$_SESSION = array();
	}
}

$game = '';
if(isset($_GET['game'])) {
	$check = validateInput($_GET['game'],'nospace');
	if($check === true) {
		$game = $_GET['game'];

		$query = mysql_query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='".mysql_escape_string($game)."'");
		if(mysql_num_rows($query) < 1) {
			error("No such game '$game'.");
		}
		else {
			$result = mysql_fetch_assoc($query);
			$gamename = $result['name'];
		}
	}
}

include(INCLUDE_PATH . "/".$mode.".inc.php");

pageFooter();
mysql_close($db_con);
?>
