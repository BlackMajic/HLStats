<?php
/**
 * HLStats Page Header
 * This file will be inserted at the top of every page generated by HLStats.
 * This file can contain PHP code.
 * @package HLStats
 * @author Johannes 'Banana' Keßler
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>HLStats
		<?php
		foreach ($title as $t) {
			echo " - $t";
		}
		?>
	</title>
	<link rel="stylesheet" href="css/default.css" type="text/css" media="screen" title="no title" charset="utf-8" />
</head>
<body>
<div id="wrap">
	<div id="header">
		<span id="slogan">Home</span>
		<ul>
			<li id="current"><a href="index.php"><span>Content</span></a></li>
			<li id="current"><a href="index.php?mode=search"><span>Search</span></a></li>
			<li id="current"><a href="index.php?mode=help"><span>Help</span></a></li>
		</ul>
	</div>
	<div id="header-logo">
		<div id="logo">HL<span class="red">Stats</span></div>
		<div id="breadcrumb">
			<a href="<?php echo $g_options["siteurl"]; ?>"><?php echo $g_options["sitename"]; ?></a>:
			<a href="index.php">HLStats</a>
			<?php
			foreach ($location as $l=>$url) {
				echo ": ";
				if(!empty($url)) {
					echo "<a href=\"$url\">$l</a>";
				}
				else {
					echo "<b>$l</b>";
				}
			}
			?>
		</div>
	</div>
	<!-- the main content div is in the includes itself -->
	<!-- since there we can decide if we a sidenav or not -->
