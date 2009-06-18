<?php
/**
 * $Id: functions.inc.php 654 2009-02-19 15:52:53Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstatsinc/functions.inc.php $
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

//
// void error (string message, [boolean exit])
//
// Formats and outputs the given error message. Optionally terminates script
// processing.
//

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
// string makeQueryString (string key, string value, [array notkeys])
//
// Generates an HTTP GET query string from the current HTTP GET variables,
// plus the given 'key' and 'value' pair. Any current HTTP GET variables
// whose keys appear in the 'notkeys' array, or are the same as 'key', will
// be excluded from the returned query string.
//

function makeQueryString($key, $value, $notkeys = array()) {
	$querystring = "";

	if (!is_array($notkeys))
		$notkeys = array();

	foreach ($_GET as $k=>$v) {
		if ($k && $k != $key && !in_array($k, $notkeys)) {
			$querystring .= urlencode($k) . "=" . urlencode($v) . "&amp;";
		}
	}

	$querystring .= urlencode($key) . "=" . urlencode($value);

	return $querystring;
}


//
// array getOptions (void)
//
// Retrieves HLstats option and style settings from the database.
//

function getOptions()
{
	global $db;

	$result  = $db->query("SELECT keyname, value FROM ".DB_PREFIX."_Options");
	$numrows = $db->num_rows($result);

	if ($numrows)
	{
		while ($rowdata = $db->fetch_row($result))
		{
			$options[$rowdata[0]] = $rowdata[1];
		}
		return $options;
	}
	else
	{
		error("Warning: Could not find any options in table " .
			"<b>".DB_PREFIX."_Options</b>, database <b>" . DB_NAME . "</b>. Check HLstats configuration.");
		return array();
	}
}


//
// void pageHeader (array title, array location)
//
// Prints the page heading.
//

function pageHeader($title, $location)
{
	global $g_options;
	include(INCLUDE_PATH . "/header.inc.php");
}


//
// void pageFooter (void)
//
// Prints the page footer.
//

function pageFooter()
{
	global $g_options;
	include(INCLUDE_PATH . "/footer.inc.php");
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

	if ($sortorder == "asc")
	{
		$sortimg = "sort-ascending.gif";
		$othersortorder = "desc";
	}
	else
	{
		$sortimg = "sort-descending.gif";
		$othersortorder = "asc";
	}

	$arrowstring = $g_options["font_small"]
			. "<a href=\"" . $g_options["scripturl"] . "?"
			. makeQueryString($var_sort, $name, array($var_sortorder));

	if ($sort == $name)
	{
		$arrowstring .= "&amp;$var_sortorder=$othersortorder";
	}
	else
	{
		$arrowstring .= "&amp;$var_sortorder=$sortorder";
	}

	if ($sorthash)
	{
		$arrowstring .= "#$sorthash";
	}

	$arrowstring .= "\" style=\"color: " . $g_options["table_head_text"]
		. "\" title=\"Change sorting order\">"
		. "<font color=\"" . $g_options["table_head_text"] . "\">"
		. "$longname</font></a>";

	if ($sort == $name)
	{
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

function getSelect ($name, $values, $currentvalue="")
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

		$select .= ">$v\n";
	}

	if ($currentvalue && !$gotcval)
	{
		$select .= "\t<option value=\"$currentvalue\" selected>$currentvalue\n";
	}

	$select .= "</select>";

	return $select;
}


//
// string getLink (string url[, int maxlength[, string type[, string target]]])
//

function getLink ($url, $maxlength=40, $type="http://", $target="_blank")
{
	$regs = "";
	if ($url && $url != $type)
	{
		if (ereg("^$type(.+)", $url, $regs))
		{
			$url = $type . $regs[1];
		}
		else
		{
			$url = $type . $url;
		}

		if (strlen($url) > $maxlength)
		{
			$url_title = substr($url, 0, $maxlength-3) . "...";
		}
		else
		{
			$url_title = $url;
		}

		$url = str_replace("\"", urlencode("\""), $url);
		$url = str_replace("<",  urlencode("<"),  $url);
		$url = str_replace(">",  urlencode(">"),  $url);

		return "<a href=\"$url\" target=\"$target\">"
			. htmlentities($url_title, ENT_COMPAT, "UTF-8") . "</a>";
	}
	else
	{
		return "";
	}
}


//
// string getEmailLink (string email[, int maxlength])
//

function getEmailLink ($email, $maxlength=40)
{
	$regs = "";
	if (ereg("(.+)@(.+)", $email, $regs))
	{
		if (strlen($email) > $maxlength)
		{
			$email_title = substr($email, 0, $maxlength-3) . "...";
		}
		else
		{
			$email_title = $email;
		}

		$email = str_replace("\"", urlencode("\""), $email);
		$email = str_replace("<",  urlencode("<"),  $email);
		$email = str_replace(">",  urlencode(">"),  $email);

		return "<a href=\"mailto:$email\">"
			. htmlentities($email_title, ENT_COMPAT, "UTF-8") . "</a>";
	}
	else
	{
		return "";
	}
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
 * check if we have a correct ip
 *
 * @param string $ip
 * @return boolean
 * @author banana
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
 * @author jumpin_banana
 */
function makeXMLSave($string) {
	// have to have the same count
	$aSearch = array("&","'","<",">","\"","/","(",")");
	$aReplace = array("","","","","","","","");

	$string = str_replace($aSearch,$aReplace, $string);

	return $string;
}

/**
 * valide if given string is correct
 *
 * @param string $string
 * @param string $mode
 */
function validateInput($string,$mode) {
	$ret = false;
	if(!empty($string) && !empty($mode)) {
		switch ($mode) {
			case 'nospace':
				$pattern = '/[^\p{L}\p{N}]/u';
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
 * @desc valids an email
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
?>
