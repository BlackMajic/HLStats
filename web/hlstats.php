<?php
/**
 * $Id: hlstats.php 657 2009-02-20 09:49:57Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstats.php $
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

// Check PHP configuration
if (version_compare(phpversion(), "5.2.6", "<")) {
	die("HLstats requires PHP version 5.2.6 or newer (you are running PHP version " . phpversion() . ").");
}

if (!get_magic_quotes_gpc()) {
	die("HLstats requires <b>magic_quotes_gpc</b> to be <i>enabled</i>. Check your php.ini or refer to the PHP manual for more information.");
}

if (get_magic_quotes_runtime()) {
	die("HLstats requires <b>magic_quotes_runtime</b> to be <i>disabled</i>. Check your php.ini or refer to the PHP manual for more information.");
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
	ini_set('display_errors',false);
}

// load config
require('hlstatsinc/hlstats.conf.inc.php');

/**
 * load required stuff
 *
 * db class
 * general classes like tablle class
 */
require(INCLUDE_PATH . "/db.inc.php");
require(INCLUDE_PATH . "/functions.inc.php");
require(INCLUDE_PATH . "/classes.inc.php");


// set utf-8 header
// we have to save all the stuff with utf-8 to make it work !!
header("Content-type: text/html; charset=UTF-8");


////
//// Initialisation
////

define("VERSION", "1.50");

$db = new DB_mysql();
$g_options = array();
$g_options = getOptions();

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


include(INCLUDE_PATH . "/".$mode.".inc.php");

pageFooter();
?>
