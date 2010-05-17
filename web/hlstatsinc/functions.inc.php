<?php
/**
 * gloabl functions file
 * functions which can be used everywhere
 * @package HLStats
 * @author Johannes 'Banana' Keßler
 * @copyright Johannes 'Banana' Keßler
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
 * create the steam profile url
 * eg. STEAM_0:0:123456
 * Steam_community_number = (Last_part_of_steam_id * 2) + 76561197960265728 + Second_to_last_part_of_steam_id
 * this needs bcmath support in PHP
 * http://www.php.net/manual/en/book.bc.php
 * @param string $steamId
 * @return string $ret
 */
function getSteamProfileUrl($steamId) {
	$ret = $steamId;
	if(!empty($steamId) && strstr($steamId,'STEAM_') && function_exists('bcadd')) {
		$t = explode(':',$steamId);
		$s = bcadd('76561197960265728',$t[2]*2);
		$s = bcadd($s,$t[1]);
		$ret = '<a href="http://steamcommunity.com/profiles/'.$s.'" target="_blank">'.$steamId.'</a>';
	}

	return $ret;
}

/**
 * toggle the color/css class for each row
 *
 * @param string $col The curren css class
 * @return string The new color
 */
function toggleRowClass(&$col) {
	if($col === "row-light") {
		$col = "row-dark";
	}
	else {
		$col = "row-light";
	}

	return $col;
}

/**
 * make var failsave
 *
 * @param string $text
 * @return string
 */
function sanitize($text) {
	return htmlentities(strip_tags($text), ENT_QUOTES, "UTF-8");
}

/**
 * make a save player name and keeping the special tags and stuff
 *
 * @param string $name The player name as string
 * @return string The escaped player name
 */
function makeSavePlayerName($name) {
	return htmlentities($name, ENT_QUOTES, "UTF-8");
}

/**
 * check if we have a correct ip
 *
 * @param string $ip
 * @return boolean
 */
function checkIP($ip) {
	if(ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $ip)) {
		$check = true;
	}
	else {
		$check = false;
	}

	return $check;
}

/**
 * replace invalid XML chars
 *
 * @param string $string
 * @return string
 */
function makeXMLSave($string) {
	// have to have the same count
	$aSearch = array("&","'","<",">","\"","/","(",")");
	$aReplace = array("","","","","","","","");

	$string = str_replace($aSearch,$aReplace, $string);

	return $string;
}

/**
 * validate if given string is correct
 *
 * @param string $string
 * @param string $mode
 */
function validateInput($string,$mode) {
	$ret = false;
	if(!empty($string) && !empty($mode)) {
		switch ($mode) {
			case 'nospace':
				$pattern = '/[^\p{L}\p{N}\p{P}]/u';
				$value = preg_replace($pattern, '', $string);
				if($string === $value) {
					 $ret = true;
				}
			break;
			case 'digit':
				$pattern = '/[^\p{N}]/u';
				$value = preg_replace($pattern, '', $string);
				if($string === $value) {
					 $ret = true;
				}
			break;

			case 'text':
				$pattern = '/[^\p{L}\p{N}\p{P}]/u';
				$value = preg_replace($pattern, '', $string);
				if($string === $value) {
					 $ret = true;
				}
			break;
		}
	}
	return $ret;
}

/**
 * check and email if valid
 *
 * @param sctring email
 * @return boolean
 * @author  Dave Child 	http://www.ilovejackdaniels.com/
 */
function check_email_address($email) {
	// First, we check that there's one @ symbol, and that the lengths are right
	if (!ereg("[^@]{1,64}@[^@]{1,255}", $email)) {
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		return false;
	}
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
			return false;
		}
	}
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}

/**
 * plain and simple language function
 * check if given string is a key in $lData array
 * if so return it, if not return string
 * of default lang is uses, return string immediately
 *
 * @param string $string
 * @return strin $ret
 */
function l($string) {
	global $lData, $cl;

	if($cl === "en") {
		return $string;
	}

	$ret = $string;
	if(!empty($string)) {
		if(isset($lData[$string])) {
			$ret = $lData[$string];
		}
		elseif(SHOW_DEBUG === true) {
			die($string.' -------is missing !-------');
		}
	}

	return $ret;
}

/**
 * Format an interval value with the requested granularity.
 *
 * @param integer $timestamp The length of the interval in seconds.
 * @param integer $granularity How many different units to display in the string.
 * @return string A string representation of the interval.
 */
function getInterval($timestamp, $granularity = 2) {
    $seconds = time() - $timestamp;
    $units = array(
        '1 '.l('year').'|:count '.l('years') => 31536000,
        '1 '.l('week').'|:count '.l('weeks') => 604800,
        '1 '.l('day').'|:count '.l('days') => 86400,
        '1 '.l('hour').'|:count '.l('hours') => 3600,
        '1 '.l('min').'|:count '.l('mins') => 60,
        '1 '.l('sec').'|:count '.l('secs') => 1);
    $output = '';
    foreach ($units as $key => $value) {
        $key = explode('|', $key);
        if ($seconds >= $value) {
            $count = floor($seconds / $value);
            $output .= ($output ? ' ' : '');
            $output .= ($count == 1) ? $key[0] : str_replace(':count', $count, $key[1]);
            $seconds %= $value;
            $granularity--;
        }
        if ($granularity == 0) {
            break;
        }
    }

    return $output ? $output : '0 sec';
}

/**
 * parse the lagunage file
 * the parse ini file is too limited...
 *
 * @param string The file to parse
 * @return array The parsed language
 */
function parse_custom_lang_file($file) {
	$ret = array();

	#$lines = file($file, FILE_SKIP_EMPTY_LINES | FILE_TEXT);
	$lines = file($file, FILE_SKIP_EMPTY_LINES);
	foreach($lines as $line) {
		$line = trim($line);
		if(!empty($line)) {
			$ld = explode(" = ",$line);
			if(count($ld) != 2) {
				die('Lang file is corrupt. Please check: '.$file.', '.$line);
			}
			$ret[$ld[0]] = $ld[1];
		}
	}

	return $ret;
}

/**
 * string makeQueryString
 *
 * @param array $params The array with the params to be added (key=>value)
 * @param array $notkeys The keys to be removed
 *
 * @return string $querystring The final querystring for an url
 */
function makeQueryString($params, $notkeys = array()) {
	$querystring = "";

	if (!is_array($notkeys)) $notkeys = array();

	foreach ($_GET as $k=>$v) {
		if(!key_exists($k,$params) && !in_array($k, $notkeys)) {
		//if ($k && $k != $key && !in_array($k, $notkeys)) {
			$querystring .= urlencode($k) . "=" . urlencode($v) . "&amp;";
		}
	}

	foreach($params as $key => $value) {
		$querystring .= urlencode($key) . "=" . urlencode($value)."&amp;";
	}
	$querystring = trim($querystring,"&");

	return $querystring;
}

/**
 * inlcude the header and build the breadcrumb menu
 *
 * @param string $title The page title
 * @param array $location The entries for the breadcrumb
 */
function pageHeader($title, $location) {
	global $g_options;
	include("hlstatsinc/header.inc.php");
}

/**
 * get the formated email link
 * @param string $email
 * @param [int $maxlenght]
 * @return string
 */
function getEmailLink ($email, $maxlength=40) {
	$ret = '';
	$regs = '';

	if (ereg("(.+)@(.+)", $email, $regs)) {
		if (strlen($email) > $maxlength) {
			$email_title = substr($email, 0, $maxlength-3) . "...";
		} else {
			$email_title = $email;
		}

		$email = str_replace("\"", urlencode("\""), $email);
		$email = str_replace("<",  urlencode("<"),  $email);
		$email = str_replace(">",  urlencode(">"),  $email);

		$ret =  "<a href=\"mailto:$email\">"
			. htmlentities($email_title, ENT_COMPAT, "UTF-8") . "</a>";
	}

	return $ret;
}

/**
 * return formatted link
 *
 * @param string url
 * @param int $maxlength
 * @param string $type
 * @param string $target
 * @return string
 */
function getLink ($url, $maxlength=40, $type="http://", $target="_blank") {
	$ret = '';
	$regs = "";
	if (!empty($url) && $url != $type) {
		if (ereg("^$type(.+)", $url, $regs)) {
			$url = $type . $regs[1];
		} else {
			$url = $type . $url;
		}

		if (strlen($url) > $maxlength) {
			$url_title = substr($url, 0, $maxlength-3) . "...";
		} else {
			$url_title = $url;
		}

		$url = str_replace("\"", urlencode("\""), $url);
		$url = str_replace("<",  urlencode("<"),  $url);
		$url = str_replace(">",  urlencode(">"),  $url);

		$ret = "<a href=\"$url\" target=\"$target\">"
			. htmlentities($url_title, ENT_COMPAT, "UTF-8") . "</a>";
	}

	return $ret;
}

/**
 * get the full game name from given gamecode
 * if the game still exists
 *
 * @param string $game The game code
 *
 * @return string The full name of the code
 */
function getGameName($gCode) {
	$gamename = ucfirst($gCode);
	$query = mysql_query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='".mysql_escape_string($gCode)."'");
	if (mysql_num_rows($query) > 0) {
		$result = mysql_fetch_assoc($query);
		$gamename = $result['name'];
	}
	mysql_free_result($query);

	return $gamename;
}

######## THOSE FUNCTIONS BELOW SHOULD BE CHECKED ############
/**
 * @todo functions to be checked
 */

//
// void error (string message, [boolean exit])
//
// Formats and outputs the given error message. Optionally terminates script
// processing.
//
// @todo remove
function error ($message, $exit=true) {
?>
<table border="1" cellspacing="0" cellpadding="5">
<tr>
<td bgcolor="#CC0000"><font face="Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"><b>ERROR</b></font></td>
</tr>
<tr>
<td bgcolor="#FFFFFF"><font face="Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo $message; ?></font></td>
</tr>
</table>
<?php if ($exit) exit;
}


//
// array getOptions (void)
//
// Retrieves HLStats option and style settings from the database.
//

function getOptions() {
	$query  = mysql_query("SELECT keyname, value FROM ".DB_PREFIX."_Options");
	if (mysql_num_rows($query) > 0) {
		while ($rowdata = mysql_fetch_assoc($query))
		{
			$options[$rowdata['keyname']] = $rowdata['value'];
		}
		return $options;
	}
	else
	{
		return array();
	}
}




//
// void getSortArrow (string sort, string sortorder, string name,
//                    string longname, [string var_sort,
//                    string var_sortorder, string sorthash])
//
// Returns the HTML code for a sort arrow <IMG> tag.
//

function getSortArrow ($sort, $sortorder, $name, $longname,
                       $var_sort="sort", $var_sortorder="sortorder",
                       $sorthash="")
{
	global $g_options;

	if ($sortorder == "asc") {
		$sortimg = "sort-ascending.gif";
		$othersortorder = "desc";
	}
	else {
		$sortimg = "sort-descending.gif";
		$othersortorder = "asc";
	}

	$arrowstring = $g_options["font_small"]
			. "<a href=\"index.php?"
			. makeQueryString($var_sort, $name, array($var_sortorder));

	if ($sort == $name) {
		$arrowstring .= "&amp;$var_sortorder=$othersortorder";
	}
	else {
		$arrowstring .= "&amp;$var_sortorder=$sortorder";
	}

	if ($sorthash) {
		$arrowstring .= "#$sorthash";
	}

	$arrowstring .= "\" style=\"color: " . $g_options["table_head_text"]
		. "\" title=\"".l('Change sorting order')."\">"
		. "<font color=\"" . $g_options["table_head_text"] . "\">"
		. $longname."</font></a>";

	if ($sort == $name) {
		$arrowstring .= "&nbsp;<img src=\"" . $g_options["imgdir"] . "/$sortimg\""
			. "width='7' height='7' hspace='4' border='0' align=\"middle\" alt=\"$sortimg\">";
	}

	$arrowstring .= $g_options["fontend_small"];

	return $arrowstring;
}


//
// string getSelect (string name, array values, [string currentvalue])
//
// Returns the HTML for a SELECT box, generated using the 'values' array.
// Each key in the array should be a OPTION VALUE, while each value in the
// array should be a corresponding descriptive name for the OPTION.
//
// The 'currentvalue' will be given the SELECTED attribute.
//

function getSelect ($name, $values, $currentvalue="",$trans=true)
{
	$select = "<select name=\"$name\">\n";

	$gotcval = false;

	foreach ($values as $k=>$v)
	{
		$select .= "\t<option value=\"$k\"";

		if ($k == $currentvalue)
		{
			$select .= " selected";
			$gotcval = true;
		}

		if($trans === true) {
			$select .= ">".l($v)."\n";
		}
		else {
			$select .= ">".$v."\n";
		}
	}

	if ($currentvalue && !$gotcval)
	{
		$select .= "\t<option value=\"$currentvalue\" selected>l($currentvalue)\n";
	}

	$select .= "</select>";

	return $select;
}








//
// array getImage (string filename)
//

function getImage ($filename)
{
	global $g_options;

	$url = $g_options["imgdir"] . $filename;

	if ($g_options["imgpath"])
	{
		$path = $g_options["imgpath"] . $filename;
	}
	else
	{
		// figure out absolute path of image
		$regs = "";
		if (!ereg("^/", $g_options["imgdir"]))
		{
			ereg("(.+)/[^/]+$", $_SERVER["SCRIPT_NAME"], $regs);
			$path = $regs[1] . "/" . $url;
		}
		else
		{
			$path = $url;
		}

		$path = $_SERVER["DOCUMENT_ROOT"] . $path;
	}

	// check if image exists
	$add = "";
	if (file_exists($path . ".gif"))
	{
		$ext = "gif";
	}
	elseif (file_exists($path . ".jpg"))
	{
		$ext = "jpg";
	}
	elseif(file_exists($path.".png")) {
		$ext = "png";
	}
	else
	{
		$ext = "";
	}

	if ($ext)
	{
		$size = getImageSize("$path.$ext");

		return array(
			"url"=>"$url.$ext",
			"path"=>"$path.$ext",
			"width"=>$size[0],
			"height"=>$size[1],
			"size"=>$size[3]
		);
	}
	else
	{
		return false;
	}
}
?>
