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

///
/// Database Settings
///

// DB_NAME - The name of the database
define("DB_NAME", "hlstats");

// DB_USER - The username to connect to the database as
define("DB_USER", "user");

// DB_PASS - The password for DB_USER
define("DB_PASS", "test");

// DB_ADDR - The address of the database server, in host:port format.
//           (You might also try setting this to e.g. ":/tmp/mysql.sock" to
//           use a Unix domain socket, if your mysqld is on the same box as
//           your web server.)
define("DB_ADDR", "localhost");

// DB_PREFIX - The table prefix. Default is hlstats (the leading _ will comes from the sql file)
define("DB_PREFIX", "hlstats");

// DB_PCONNECT - Set to 1 to use persistent database connections. Persistent
//               connections can give better performance, but may overload
//               the database server. Set to 0 to use non-persistent
//               connections.
define("DB_PCONNECT", 0);


///
/// General Settings
///

// LANGUAGE
//			load the given translation
//			Possible values are the file in the lang folder
//			Use the LANGCODE.ini.php to put in here
//			Default is en.
//			If a translation is not found the en text will be displayed.
define('LANGUAGE','en');

// INCLUDE_PATH - Filesystem path to the hlstatsinc directory. This path can
//                be specified relative to hlstats.php by prepending ./ or
//                ../  If the path begins with a / then it is taken as a
//                full absolute filesystem path. However if the path begins
//                with none of these constructs, PHP will search your
//                include_path (as set in php.ini) (probably NOT the current
//                directory as might be expected!).
//                   Example paths:
//                      1) /usr/local/apache/hlstatsinc
//                           (absolute path)
//                      2) ../hlstatsinc
//                      -) ./hlstatsinc
//                           (paths relative to hlstats.php)
//                      3) hlstats/include
//                           (path relative to include_path)
//                Under Windows, make sure you use forward slash (/) instead
//                of back slash (\).
define("INCLUDE_PATH", "./hlstatsinc");

// DELETEDAYS - How many days the Event History covers. Must match the value
//              of DeleteDays in hlstats.conf.
//              default = 5
define("DELETEDAYS", 5);

// MODE - Sets the player-tracking mode. Must match the value of Mode in
//        hlstats.conf. Possible values:
//           1) "Normal"    - Recommended for public Internet server use.
//                            Players will be tracked by Unique ID.
//           2) "NameTrack" - Useful for shared-PC environments, such as
//                            Internet cafes, etc. Players will be tracked
//                            by nickname. EXPERIMENTAL!
//           3) "LAN"       - Useful for LAN servers where players do not
//                            have a real Unique ID. Players will be tracked
//                            by IP Address.
define("MODE", "Normal");


// hide bot players from stats
// values are 1 or 0
define("HIDE_BOTS", "0");

// the Elo rating system
// developed by HampusW
// 			here you can decide if you want to use this system
//			IMPORTANT: Must match the value of EloRating in hlstats.conf !!
//			Possible values are
//			1) "0"		- Off. Do not use the rating system at all.
//			2) "1"		- Use the system and display it with the ordinary system
//			3) "2"		- Only use the EloRating and show only the new one.
define('ELORATING','0');
?>
