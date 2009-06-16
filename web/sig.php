<?php
/**
 * $Id: sig.php 591 2008-10-08 07:31:13Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/sig.php $
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
 * general classes like table class
 */
require(INCLUDE_PATH . "/db.inc.php");
require(INCLUDE_PATH . "/functions.inc.php");
require(INCLUDE_PATH . "/classes.inc.php");

// deb class and options
$db_classname = "DB_" . DB_TYPE;
$db = new $db_classname;

$g_options = getOptions();

/**
 * path settings.
 * and sanitize of the get vars
 */
$picPath = dirname(__FILE__)."/signatures/";
if($_GET['style'] != "") {
	$style = sanitize($_GET['style']);
}
else {
	$style = "cs1";
}

// check for playerId
if($_GET['playerId'] != "") {
	$playerId = sanitize($_GET['playerId']);
	$playerId = (int)$playerId;
}
else {
	// no player given
	echo "No playerId given";
	exit();
}

/**
 * check if we are allowed to create a pic
 * if so check if we have jpeg support
 * otherwise return "not available" text
 */
if($g_options['allowSig'] == "1") {
	// check for gd support
	$gdInfo = gd_info();
	if($gdInfo['PNG Support'] && $gdInfo['GIF Read Support'] && $gdInfo['GIF Create Support']) {
		// we are allowed and have gif/png support

		// check if we have to create a new picture
		if(file_exists($picPath."create.stamp")) {
			$fh = fopen($picPath."create.stamp","r");
			$stamp = fread($fh, filesize($picPath."create.stamp"));
			$stamp = (int)$stamp;
			fclose($fh);
			// check the timestamp
			// // valid for a half/hour
			if($stamp < time()) {
				// unlink the file
				// I know this is a dirty hack, but who cares ;-)
				@unlink($picPath."preRender/".$playerId.".png");
				$fh = fopen($picPath."create.stamp","w+");
				fwrite($fh,time()+1800);
				fclose($fh);
			}
		}
		else {
			// we dont hava stamp file yet
			// // create one and continue
			$fh = fopen($picPath."create.stamp","w+");
			fwrite($fh,time()+1800);
			fclose($fh);
		}

		// check if we have already a picture.
		// // if so use this end exit
		if(file_exists($picPath."preRender/".$playerId.".png")) {
			header("Content-type: image/png");
			readfile($picPath."preRender/".$playerId.".png");
			exit();
		}

		// get the player data
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_Players WHERE playerId = '".$playerId."'");
		$playerData = $db->fetch_array($query);
		if($playerData === false) {
			// no player data !
			echo "No data found";
			exit();
		}
		// rank
		$db->query("
			SELECT
				skill,playerId
			FROM
				".DB_PREFIX."_Players
			WHERE
				game='".$playerData['game']."'
			ORDER BY skill DESC
		");
		$ranKnum = 1;
		while ($row = $db->fetch_row()) {
			$statsArr[$row[1]] = $ranKnum;
			$ranKnum++;
		}
		$playerRank = $statsArr[$playerId];
		$playerWholeRank = count($statsArr);

		// server info
		$query = $db->query("SELECT serverId FROM ".DB_PREFIX."_Events_Connects
					WHERE playerId = '".$playerId."' LIMIT 1");
		$serverId = $db->fetch_array($query);
		// no get the server info
		$query = $db->query("SELECT address,port,name FROM ".DB_PREFIX."_Servers WHERE `serverId` = ".$serverId['serverId']."");
		$serverData = $db->fetch_array($query);

		$font = $picPath.'svenings.ttf';
		switch ($style) {

			case 'black':
				// 400x100
				$imgH = imagecreatefrompng($picPath."black.png");
				imagealphablending($imgH, true);
				imagesavealpha($imgH, true);

				// colors
				$foreground = imagecolorallocate($imgH, 255, 255, 255);
				$background = imagecolorallocate($imgH, 10, 227, 236);
			break;

			case 'multi':
				// 400x100
				$imgH = imagecreatefrompng($picPath."multi.png");
				imagealphablending($imgH, true);
				imagesavealpha($imgH, true);

				// colors
				$foreground = imagecolorallocate($imgH, 255, 255, 255);
				$background = imagecolorallocate($imgH, 44, 87, 172);
			break;

			case 'red':
				// 400x100
				$imgH = imagecreatefrompng($picPath."red.png");
				imagealphablending($imgH, true);
				imagesavealpha($imgH, true);

				// colors
				$foreground = imagecolorallocate($imgH, 255, 255, 255);
				$background = imagecolorallocate($imgH, 79, 20, 21);
			break;

			case 'blue':
				// 400x100
				$imgH = imagecreatefrompng($picPath."blue.png");
				imagealphablending($imgH, true);
				imagesavealpha($imgH, true);

				// colors
				$foreground = imagecolorallocate($imgH, 255, 255, 255);
				$background = imagecolorallocate($imgH, 0, 0, 0);
			break;

			case 'green':
			default:
				// 400x100
				$imgH = imagecreatefrompng($picPath."green.png");
				imagealphablending($imgH, true);
				imagesavealpha($imgH, true);

				// colors
				$foreground = imagecolorallocate($imgH, 255, 255, 255);
				$background = imagecolorallocate($imgH, 0, 0, 0);
		}

		// Player Name
		$text = $playerData['lastName'];
		imagettftext($imgH, 12, 0, 22, 33, $background, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));
		imagettftext($imgH, 12, 0, 20, 31, $foreground, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));

		// Rank
		$text = "Rank: ".$playerRank." / ".$playerWholeRank;
		imagettftext($imgH, 10, 0, 22, 50, $background, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));
		imagettftext($imgH, 10, 0, 20, 48, $foreground, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));

		// hlstats url
		$text = $g_options['siteurl'];
		// text info
		/*	0  	lower left corner, X position
			1 	lower left corner, Y position
			2 	lower right corner, X position
			3 	lower right corner, Y position
			4 	upper right corner, X position
			5 	upper right corner, Y position
			6 	upper left corner, X position
			7 	upper left corner, Y position
		*/
		// determine the position of the url and add some padding
		$textInfo = imagettfbbox(8,0,$font,$text);
		$textWitdh = $textInfo[2];
		$textPos = (400-$textWitdh)-10;
		imagettftext($imgH, 8, 0, $textPos+1, 13, $background, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));
		imagettftext($imgH, 8, 0, $textPos, 12, $foreground, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));

		// points
		$text = "HLStats points: ".$playerData['skill'];
		imagettftext($imgH, 9, 0, 22, 62, $background, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));
		imagettftext($imgH, 9, 0, 20, 60, $foreground, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));

		// kills
		$text = "Kills: ".$playerData['kills'];
		imagettftext($imgH, 9, 0, 22, 77, $background, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));
		imagettftext($imgH, 9, 0, 20, 75, $foreground, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));

		// server IP and port etc
		$text = $serverData['name']."\nIP: ".$serverData['address']." ".$serverData['port'];
		$textInfo = imagettfbbox(9,0,$font,$text);
		$textWitdh = $textInfo[2];
		$textPos = (400-$textWitdh)-10;
		imagettftext($imgH, 9, 0, $textPos+1, 41, $background, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));
		imagettftext($imgH, 9, 0, $textPos, 40, $foreground, $font, html_entity_decode($text,ENT_COMPAT,"UTF-8"));

		// display the image
		header("Content-type: image/png");
		imagepng($imgH,$picPath."preRender/".$playerId.".png");
		imagepng($imgH);
		exit();
	}
	else {
		// no jpeg support
		// // we exit here.
		echo "No support for creation a signature.";
		exit();
	}
}
else {
	// we are not allowed to create signatures
	// // we end here.
	echo "Signature creation disabled.";
	exit();
}
?>