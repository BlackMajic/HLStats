<?php
/**
 * main admin file
 * @package HLStats
 * @author Johannes 'Banana' Keßler
 * @copyright Johannes 'Banana' Keßler
 */

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


$selTask = false;
$selGame = false;

if(!empty($_GET["task"])) {
	if(validateInput($_GET["task"],'nospace') === true) {
		$selTask = $_GET["task"];
	}
}

if(!empty($_GET["admingame"])) {
	if(validateInput($_GET["admingame"],'nospace') === true) {
		$selGame = $_GET["admingame"];
	}
}

session_set_cookie_params(43200); // 8 hours
session_name("hlstats-session");
session_start();
session_regenerate_id(true);

$auth = false;
require('class/admin.class.php');
$adminObj = new Admin();
$auth = $adminObj->getAuthStatus();

// process the logout
if(!empty($_GET['logout'])) {
	if(validateInput($_GET['logout'],'digit') === true && $_GET['logout'] == "1") {
		$adminObj->doLogout();
		header('Location: index.php');
	}
}

if($auth === true) {
	if(!empty($selTask)) {
		if(file_exists(getcwd().'/hlstatsinc/admintasks/'.$selTask.'.inc.php')) {
			require('hlstatsinc/admintasks/'.$selTask.'.inc.php');
		}
		else {
			require('hlstatsinc/admintasks/overview.php');
		}
	}
	else { // show overview
		require('hlstatsinc/admintasks/overview.php');
	}
}
else {
	require('hlstatsinc/admintasks/login.php');
}
?>