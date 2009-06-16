<?php
/**
 * $Id: hlstats.php 625 2008-11-11 10:01:09Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstats.php $
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

// load config
require('hlstatsinc/hlstats.conf.inc.php');

/**
 * load required stuff
 *
 * functions functions.inc.php
 * db class
 * general classes like tablle class
 */
require(INCLUDE_PATH . "/db.inc.php");
require(INCLUDE_PATH . "/functions.inc.php");
require(INCLUDE_PATH . "/classes.inc.php");

// do not report NOTICE warnings
error_reporting(E_ALL ^ E_NOTICE);

// set utf-8 header
// we have to save all the stuff with utf-8 to make it work !!
header("Content-type: text/html; charset=UTF-8");

// Check PHP configuration

if (version_compare(phpversion(), "4.1.0", "<")) {
	error("HLstats requires PHP version 4.1.0 or newer (you are running PHP version " . phpversion() . ").");
}

if (!get_magic_quotes_gpc()) {
	error("HLstats requires <b>magic_quotes_gpc</b> to be <i>enabled</i>. Check your php.ini or refer to the PHP manual for more information.");
}

if (get_magic_quotes_runtime()) {
	error("HLstats requires <b>magic_quotes_runtime</b> to be <i>disabled</i>. Check your php.ini or refer to the PHP manual for more information.");
}


////
//// Initialisation
////

define("VERSION", "1.40");

$db_classname = "DB_" . DB_TYPE;
$db = new $db_classname;

$g_options = getOptions();

// set scripturl if not set in options
if ($g_options["scripturl"] == "") {
	$g_options["scripturl"] = $_SERVER['PHP_SELF'];
}


////
//// Main
////

if($_GET["mode"] == "") {
	$mode = "";
}
else {
	$mode = $_GET["mode"];
}

// process the logout
if(isset($_GET['logout']) && $_GET['logout'] == "1") {
	// destroy session and cookie

	setcookie("authusername", '', mktime(12,0,0,1, 1, 1990));
	setcookie("authpassword", '', mktime(12,0,0,1, 1, 1990));
	setcookie("authsavepass", '', mktime(12,0,0,1, 1, 1990));
	setcookie("authsessionStart", '', mktime(12,0,0,1, 1, 1990));

	$_COOKIE = array();
	$_SESSION = array();
}

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
	"live_stats",
	"playerchathistory"
);

if (!in_array($mode, $modes)) {
	$mode = "contents";
}

include(INCLUDE_PATH . "/".$mode.".inc.php");

pageFooter();
?>
