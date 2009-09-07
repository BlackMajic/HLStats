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

	session_start();

	class Auth {
		var $ok = false;
		var $error = false;

		var $username, $password, $savepass;
		var $sessionStart, $session;

		var $userdata = array();

		function Auth() {

			if (isset($_POST["authusername"]) && $_POST['authusername'] != "") {

				if(!empty($_POST["authusername"])) {
					$this->username = sanitize($_POST["authusername"]);
				}
				if(!empty($_POST["authpassword"])) {
					$this->password = sanitize($_POST["authpassword"]);
				}
				if(!empty($_POST["authsavepass"])) {
					$this->savepass = sanitize($_POST["authsavepass"]);
				}
				$this->sessionStart = 0;

				# clear POST vars so as not to confuse the receiving page
				$_POST = array();

				$this->session = false;
				$this->checkPass();
			}
			elseif (isset($_COOKIE["authusername"]) && $_COOKIE['authusername'] != "") {

				if(!empty($_COOKIE["authusername"])) {
					$this->username = sanitize($_COOKIE["authusername"]);
				}
				if(!empty($_COOKIE["authpassword"])) {
					$this->password = sanitize($_COOKIE["authpassword"]);
				}
				if(!empty($_COOKIE["authsavepass"])) {
					$this->savepass = sanitize($_COOKIE["authsavepass"]);
				}
				if(!empty($_COOKIE["authsessionStart"])) {
					$this->sessionStart = sanitize($_COOKIE["authsessionStart"]);
				}

				$this->session = true;

				//$this->checkPass();
				$this->checkCookieAuth();
			}
			else {
				$this->ok = false;
				$this->error = false;
				$this->session = false;
				$this->printAuth();
			}
		}

		function checkCookieAuth() {
			$query = mysql_query(" SELECT * FROM ".DB_PREFIX."_Users WHERE username='$this->username'");
			if (mysql_num_rows($query) == 1) {
				$this->userdata = mysql_fetch_assoc($query);
				mysql_free_result($query);

				if (md5($this->username) == $this->password) {
					$this->ok = true;
					$this->error = false;
					if ($this->sessionStart > (time()-3600)) {
						// Valid session, update session time & display the page
						$this->doCookies();
						return true;
					}
					elseif ($this->sessionStart) {
						// A session exists but has expired
						if ($this->savepass) {
							// They selected 'Save my password' so we just
							// generate a new session and show the page.
							$this->doCookies();
							return true;
						}
						else {
							$this->ok = false;
							$this->error = "Your session has expired. Please try again.";
							$this->password = "";

							$this->printAuth();
							return false;
						}
					}
					elseif (!$this->session) {
						// No session and no cookies, but the user/pass was
						// POSTed, so we generate cookies.
						$this->doCookies();
						return true;
					}
					else {
						// No session, user/pass from a cookie, so we force auth
						$this->printAuth();
						return false;
					}
				}
				else {
					// invalid cookie data
					$this->ok = false;
					if ($this->session)	{
						// Cookie without 'Save my password' - not an error
						$this->error = false;
					}
					else {
						$this->error = "The password you supplied is incorrect.";
					}
					$this->password = "";
					$this->printAuth();
				}
			}
			else {
				// The username is wrong
				$this->ok = false;
				$this->error = "Your session has expired. Please try again.";
				$this->password = "";

				$this->printAuth();
				return false;
			}
		}

		function checkPass() {
			$query = mysql_query("SELECT * FROM ".DB_PREFIX."_Users
						WHERE username='$this->username'");

			if (mysql_num_rows($query) == 1) {
				// The username is OK

				$this->userdata = mysql_fetch_assoc($query);
				mysql_free_result($query);

				if (md5($this->password) == $this->userdata["password"]) {
					// The username and the password are OK

					$this->ok = true;
					$this->error = false;

					if ($this->sessionStart > (time()-3600)) {
						// Valid session, update session time & display the page
						$this->doCookies();
						return true;
					}
					elseif ($this->sessionStart) {
						// A session exists but has expired
						if ($this->savepass) {
							// They selected 'Save my password' so we just
							// generate a new session and show the page.
							$this->doCookies();
							return true;
						}
						else {
							$this->ok = false;
							$this->error = "Your session has expired. Please try again.";
							$this->password = "";

							$this->printAuth();
							return false;
						}
					}
					elseif (!$this->session) {
						// No session and no cookies, but the user/pass was
						// POSTed, so we generate cookies.
						$this->doCookies();
						return true;
					}
					else {
						// No session, user/pass from a cookie, so we force auth
						$this->printAuth();
						return false;
					}
				}
				else {
					// The username is OK but the password is wrong

					$this->ok = false;
					if ($this->session)	{
						// Cookie without 'Save my password' - not an error
						$this->error = false;
					}
					else {
						$this->error = "Wrong authentication data.";
					}
					$this->password = "";
					$this->printAuth();
				}
			}
			else {
				// The username is wrong
				$this->ok = false;
				$this->error = "Wrong authentication data.";
				$this->printAuth();
			}
		}

		function doCookies() {
			// this has been rewritten to not store the password in the cookie
			setcookie("authusername", $this->username, time()+31536000, "", "", 0);

			if ($this->savepass) {
				setcookie("authpassword", md5($this->username), time()+31536000, "", "", 0);
			}
			else {
				setcookie("authpassword", md5($this->username), 0, "", "", 0);
			}
			setcookie("authsavepass", $this->savepass, time()+31536000, "", "", 0);
			setcookie("authsessionStart", time(), 0, "", "", 0);
		}

		function printAuth() {
			global $g_options;

			include(INCLUDE_PATH . "/adminauth.inc.php");
			exit();
		}
	}


	class AdminTask {
		var $title = "";
		var $acclevel = 0;
		var $type = "";
		var $description = "";

		function AdminTask ($title, $acclevel, $type="general", $description="") {
			$this->title = $title;
			$this->acclevel = $acclevel;
			$this->type = $type;
			$this->description = $description;
		}
	}


	class EditList
	{
		var $columns;
		var $keycol;
		var $table;
		var $icon;
		var $showid;

		var $errors;
		var $newerror;

		function EditList ($keycol, $table, $icon, $showid=true)
		{
			$this->keycol = $keycol;
			$this->table = $table;
			$this->icon = $icon;
			$this->showid = $showid;
		}

		function update () {

			$okcols = 0;
			$qcols = '';
			$qvals = '';
			foreach ($this->columns as $col) {
				$value = '';
				if(!empty($_POST["new_$col->name"])) {
					$value = $_POST["new_$col->name"];
				}


				if ($value != "") {
					if ($col->type == "ipaddress" && !checkIP($value)) {
						$this->errors[] = "Column '$col->title' requires a valid IP address for new row";
						$this->newerror = true;
						$okcols++;
					}
					else {
						if ($qcols) $qcols .= ", ";
						$qcols .= $col->name;

						if ($qvals) $qvals .= ", ";

						if ($col->type == "password") {
							$qvals .= "MD5('$value')";
						}
						else {
							$qvals .= "'".mysql_escape_string(($value))."'";
						}

						if ($col->type != "select" && $col->type != "hidden" && $value != $col->datasource)
							$okcols++;
					}
				}
				elseif ($col->required) {
					$this->errors[] = "Required column '$col->title' must have a value for new row";
					$this->newerror = true;
				}
			}

			if ($okcols > 0 && !$this->errors) {
				mysql_query("
					INSERT INTO
						$this->table
						(
							$qcols
						)
					VALUES
					(
						$qvals
					)"
				);
				if (mysql_error()) {
					$this->errors[] = "DB Error: " . mysql_error();
				}
			}
			elseif ($okcols == 0) {
				$this->errors = array();
				$this->newerror = false;
			}

			if (!empty($_POST["rows"])) {
				foreach ($_POST["rows"] as $row) {
					$row = stripslashes($row);

					if (!empty($_POST[$row . "_delete"])) {
						mysql_query("
							DELETE FROM
								$this->table
							WHERE
								$this->keycol='" . addslashes($row) . "'
						");
					}
					else {
						$rowerror = false;

						$query = "UPDATE $this->table SET ";
						$i=0;
						foreach ($this->columns as $col) {
							$value = '';
							if(!empty($_POST[$row . "_" . $col->name])) {
								$value = $_POST[$row . "_" . $col->name];
							}


							if ($col->type == "password" && $value == "(encrypted)")
								continue;

							if ($value == "" && $col->required) {
								$this->errors[] = "Required column '$col->title' must have a value for row '$row'";
								$rowerror = true;
							}
							elseif ($col->type == "ipaddress" && !checkIP($value)) {
								$this->errors[] = "Column '$col->title' requires a valid IP address for row '$row'";
								$rowerror = true;
							}

							if ($i > 0) $query .= ", ";

							if ($col->type == "password") {
								$query .= $col->name . "=MD5('$value')";
							}
							else {
								$query .= $col->name . "='".mysql_escape_string(($value))."'";
							}
							$i++;
						}
						$query .= " WHERE $this->keycol='" . addslashes($row) . "'";

						if (!$rowerror) {
							mysql_query($query);
						}
					}
				}
			}

			if ($this->error()) {
				return false;
			}
			else {
				return true;
			}
		}

		function draw ($result)
		{
			global $g_options;
?>
<table width="75%" border="0" cellspacing="0" cellpadding="0">

<tr valign="top" bgcolor="<?php echo $g_options["table_border"]; ?>">
	<td><table width="100%" border="0" cellspacing="1" cellpadding="4">

		<tr valign="bottom">
<?php
			echo "<td bgcolor=\"" . $g_options["table_head_bgcolor"] . "\"></td>";

			if ($this->showid)
			{
?>
			<td align="right" bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>"><?php echo $g_options["font_small"];
				echo "ID";
				echo $g_options["fontend_small"];
?></td>
<?php
			}

			foreach ($this->columns as $col)
			{
				if ($col->type == "hidden") continue;
				echo "<td bgcolor=\"" . $g_options["table_head_bgcolor"] . "\">"
					. $g_options["font_small"] . "<font color=\""
					. $g_options["table_head_text"] . "\">" . l($col->title)
					. "</font>" . $g_options["fontend_small"] . "</td>\n";
			}
?>
			<td align="center" bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>"><?php echo $g_options["font_small"];
				echo l("Delete");
				echo $g_options["fontend_small"];
?></td>
		</tr>

<?php
			while ($rowdata = mysql_fetch_assoc($result))
			{
				echo "\n<tr>\n";
				echo "<td align=\"center\" bgcolor=\"" . $g_options["table_bgcolor1"] . "\">";
				echo "<img src=\"" . $g_options["imgdir"] . "/$this->icon.gif\" width='16' height='16' border='0'></td>\n";

				if ($this->showid)
				{
					echo "<td align=\"right\" bgcolor=\"" . $g_options["table_bgcolor2"] . "\">"
						. $g_options["font_small"] . $rowdata[$this->keycol] . $g_options["fontend_small"]
						. "</td>\n";
				}

				$this->drawfields($rowdata, false, false);
?>
<td align="center" bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>"><input type="checkbox" name="<?php echo $rowdata[$this->keycol]; ?>_delete" value="1"></td>
<?php echo "</tr>\n\n";
			}
?>

<tr>
<?php
			echo "<td bgcolor=\"" . $g_options["table_bgcolor1"] . "\" align=\"center\">"
				. $g_options["font_small"] . "new" . $g_options["fontend_small"] . "</td>\n";

			if ($this->showid)
				echo "<td bgcolor=\"" . $g_options["table_bgcolor2"] . "\" align=\"right\">"
					. $g_options["font_small"] . "&nbsp;" . $g_options["fontend_small"] . "</td>\n";

			if ($this->newerror)
			{
				$this->drawfields($_POST, true, true);
			}
			else
			{
				$this->drawfields(array(), true);
			}

			echo "<td bgcolor=\"" . $g_options["table_bgcolor1"] . "\"></td>\n";
?>
</tr>

		</table></td>
</tr>

</table><p>
<?php
		}


		function drawfields ($rowdata=array(), $new=false, $stripslashes=false) {
			global $g_options;

			$i=0;
			foreach ($this->columns as $col) {
				if ($new) {
					$keyval = "new";
					if(!empty($rowdata["new_$col->name"])) {
						$rowdata[$col->name] = $rowdata["new_$col->name"];
					}
					if ($stripslashes) {
						if(isset($rowData[$col->name])) {
							$rowdata[$col->name] = stripslashes($rowdata[$col->name]);
						}
					}
				}
				else {
					$keyval = $rowdata[$this->keycol];
					if ($stripslashes) $keyval = stripslashes($keyval);
				}

				if ($col->type != "hidden") {
					echo "<td bgcolor=\"" . $g_options["table_bgcolor1"] . "\">";
				}

				if ($i == 0 && !$new) {
					echo "<input type=\"hidden\" name=\"rows[]\" value=\"" . htmlspecialchars($keyval) . "\">";
				}

				if ($col->maxlength < 1)
					$col->maxlength = "";

				switch ($col->type) {
					case "select":
						unset($coldata);

						if (ereg(";", $col->datasource))
						{
							// for manual datasource in format "key/value;key/value" or "key;key"
							foreach (explode(";", $col->datasource) as $v)
							{
								if (ereg("/", $v))
								{
									list($a, $b) = explode("/", $v);
									$coldata[$a] = $b;
								}
								else
								{
									$coldata[$v] = $v;
								}
							}
						}
						else
						{
							// for SQL datasource in format "table.column/keycolumn/where"
							list($col_table, $col_col) = explode(".", $col->datasource);
							list($col_col, $col_key, $col_where) = explode("/", $col_col);
							if ($col_where) $col_where = "WHERE $col_where";
							$col_result = mysql_query("SELECT $col_key, $col_col FROM $col_table $col_where ORDER BY $col_key");
							$coldata = array();
							while (list($k, $v) = mysql_fetch_array($col_result))
							{
								$coldata[$k] = $v;
							}
						}

						if ($col->width) $width = " style=\"width:" . $col->width*5 . "px\"";
						else $width = "";

						echo "<select name=\"" . $keyval . "_$col->name\"$width>\n";

						if (!$col->required)
						{
							echo "<option value=\"\">\n";
						}

						$gotcval = false;

						foreach ($coldata as $k=>$v) {
							$selected = "";
							if(!empty($rowdata[$col->name])) {
								if ($rowdata[$col->name] == $k) {
									$selected = " selected";
									$gotcval = true;
								}
							}

							echo "<option value=\"$k\"$selected>",l($v),"\n";
						}

						if (!$gotcval) {
							if(!empty($rowdata[$col->name])) {
								echo "<option value=\"",$rowdata[$col->name],"\" selected>",l($rowdata[$col->name]),"\n";
							}

						}

						echo "</select>";
						break;

					case "checkbox":
						$selectedval = "1";
						$value = '';
						if(!empty($rowdata[$col->name])) {
							$value = $rowdata[$col->name];
						}


						if ($value == $selectedval) $selected = " checked";
						else $selected = "";

						echo "<center><input type=\"checkbox\" name=\"" . $keyval
							. "_$col->name\" value=\"$selectedval\"$selected></center>";
						break;

					case "hidden":
						echo "<input type=\"hidden\" name=\"" . $keyval
							. "_$col->name\" value=\"" . htmlspecialchars($col->datasource) . "\">";
						break;

					default:
						$value='';
						if ($col->datasource != "" && !isset($rowdata[$col->name])) {
							$value = $col->datasource;
						}
						else {
							if(!empty($rowdata[$col->name])) {
								$value = $rowdata[$col->name];
							}
						}

						echo "<input type=\"text\" name=\"" . $keyval
							. "_$col->name\" size=$col->width "
							. "value=\"" . htmlspecialchars($value) . "\" class=\"textbox\""
							. " maxlength=\"$col->maxlength\">";
				}

				if ($col->type != "hidden")
				{
					echo "</td>\n";
				}

				$i++;
			}
		}

		function error()
		{
			if (is_array($this->errors))
			{
				return implode("<p>\n\n", $this->errors);
			}
			else
			{
				return false;
			}
		}
	}

	class EditListColumn
	{
		var $name;
		var $title;
		var $width;
		var $required;
		var $type;
		var $datasource;
		var $maxlength;

		function EditListColumn ($name, $title, $width=20, $required=false, $type="text", $datasource="", $maxlength=0)
		{
			$this->name = $name;
			$this->title = $title;
			$this->width = $width;
			$this->required = $required;
			$this->type = $type;
			$this->datasource = $datasource;
			$this->maxlength = intval($maxlength);
		}
	}



	class PropertyPage {
		var $table;
		var $keycol;
		var $keyval;
		var $propertygroups = array();

		function PropertyPage ($table, $keycol, $keyval, $groups) {
			$this->table  = $table;
			$this->keycol = $keycol;
			$this->keyval = $keyval;
			$this->propertygroups = $groups;
		}

		function draw ($data) {
			foreach ($this->propertygroups as $group) {
				$group->draw($data);
			}
		}

		function update () {

			$setstrings = array();
			foreach ($this->propertygroups as $group) {
				foreach ($group->properties as $prop) {
					if(!empty($_POST[$prop->name])) {
						$setstrings[] = $prop->name . "='" . $_POST[$prop->name] . "'";
					}
				}
			}

			if(!empty($setstrings)) {
				mysql_query("
					UPDATE
						" . $this->table . "
					SET
						" . implode(",\n", $setstrings) . "
					WHERE
						" . $this->keycol . "='" . $this->keyval . "'
				");
			}
		}
	}

	class PropertyPage_Group
	{
		var $title = "";
		var $properties = array();

		function PropertyPage_Group ($title, $properties) {
			$this->title = $title;
			$this->properties = $properties;
		}

		function draw ($data) {
			global $g_options;
?>
<b><?php echo $this->title; ?></b><br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr valign="top" bgcolor="<?php echo $g_options["table_border"]; ?>">
	<td><table width="100%" border="0" cellspacing="1" cellpadding="4">
<?php
			foreach ($this->properties as $prop) {
				if(key_exists($prop->name,$data)) {
					$prop->draw($data[$prop->name]);
				}
			}
?>
		</table></td>
</tr>

</table><p>
<?php
		}
	}

	class PropertyPage_Property {
		var $name;
		var $title;
		var $type;

		function PropertyPage_Property ($name, $title, $type, $datasource="") {
			$this->name  = $name;
			$this->title = $title;
			$this->type  = $type;
			$this->datasource = $datasource;
		}

		function draw ($value) {
			global $g_options;
?>
<tr valign="middle">
	<td width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
		<?php
		echo $g_options["font_normal"];
		echo $this->title . ":";
		echo $g_options["fontend_normal"];
		?>
	</td>
	<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php
			switch ($this->type) {
				case "textarea":
					echo "<textarea name=\"$this->name\" cols=35 rows=4 wrap=\"virtual\">"
						. htmlspecialchars($value)
						. "</textarea>";
				break;

				case 'checkbox':
					echo '<input type="checkbox" name='.$this->name.' value="0" />';
				break;

				case "select":
					// for manual datasource in format "key/value;key/value" or "key;key"
					foreach (explode(";", $this->datasource) as $v)
					{
						if (ereg("/", $v))
						{
							list($a, $b) = explode("/", $v);
							$coldata[$a] = $b;
						}
						else
						{
							$coldata[$v] = $v;
						}
					}

					echo getSelect($this->name, $coldata, $value);
				break;

				default:
					echo "<input type=\"text\" name=\"$this->name\" size=35 value=\""
						. htmlspecialchars($value)
						. "\" class=\"textbox\">";
					break;
			}
?></td>
</tr>
<?php
		}
	}


	function message ($icon, $msg) {
		global $g_options;
?>
		<table width="60%" border="0" cellspacing="0" cellpadding="0">

		<tr valign="top">
			<td width=40><img src="<?php echo $g_options["imgdir"] . "/$icon"; ?>.gif" width="16" height="16" border="0" hspace="5"></td>
			<td width="100%"><?php
	echo $g_options["font_normal"];
	echo "<b>$msg</b>";
	echo $g_options["fontend_normal"];
?></td>
		</tr>

		</table><p>
<?php
	}




	$auth = new Auth;

	pageHeader(array(l("Admin")), array(l("Admin")=>""));

	$selTask = '';
	$selGame = '';

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


?>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td><?php
	echo $g_options["font_normal"];

	// General Settings
	$admintasks["options"]			= new AdminTask(l("HLStats Options"), 100);
	$admintasks["adminusers"]		= new AdminTask(l("Admin Users"), 100);
	$admintasks["games"]			= new AdminTask(l("Games"), 100);
	$admintasks["clantags"]			= new AdminTask(l("Clan Tag Patterns"), 80);
	$admintasks["plugins"]			= new AdminTask(l("Server Plugins"), 80);

	// Game Settings
	$admintasks["servers"]			= new AdminTask(l("Servers"), 100, "game");
	$admintasks["resetgame"]		= new AdminTask(l("Reset"), 100, "game");
	$admintasks["actions"]			= new AdminTask(l("Actions"), 80, "game");
	$admintasks["teams"]			= new AdminTask(l("Teams"), 80, "game");
	$admintasks["roles"]			= new AdminTask(l("Roles"), 80, "game");
	$admintasks["weapons"]			= new AdminTask(l("Weapons"), 80, "game");
	$admintasks["awardsWeapons"]	= new AdminTask(l("Weapon Awards"), 80, "game");
	$admintasks["awardsActions"]	= new AdminTask(l("Action Awards"), 80, "game");

	// Tools
	$admintasks["toolsEditdetails"] = new AdminTask(l("Edit Player or Clan Details"), 80, "tool",
		l("Edit a player or clan's profile information."));
	$admintasks["toolsAdminevents"] = new AdminTask(l("Admin-Event History"), 80, "tool",
		l("View event history of logged Rcon commands and Admin Mod messages."));
	$admintasks["toolsIpstats"]	= new AdminTask(l("Host Statistics"), 80, "tool",
		l("See which ISPs your players are using."));
	$admintasks["toolsOptimize"]	= new AdminTask(l("Optimize Database"), 100, "tool",
		l("This operation tells the MySQL server to clean up the database tables, optimizing them for better performance. It is recommended that you run this at least once a month."));
	$admintasks["toolsReset"]		= new AdminTask(l("Reset Statistics"), 100, "tool",
				l("Delete all players, clans and events from the database."));
	$admintasks["toolsNews"]		= new AdminTask(l("News at Front page"), 80, "tool",
				l("Write news to the front page."));

	// Sub-Tools
	$admintasks["toolsEditdetailsPlayer"] = new AdminTask(l("Edit Player Details"), 80, "subtool",
			l("Edit a player's profile information."));
	$admintasks["toolsEditdetailsClan"]   = new AdminTask(l("Edit Clan Details"), 80, "subtool",
			l("Edit a clan's profile information."));

	// Show Tool
	$check = false;
	if(!empty($admintasks[$selTask])) {
		if(is_object($admintasks[$selTask])) {
			if($admintasks[$selTask]->type == "tool" || $admintasks[$selTask]->type == "subtool") {
				$check = true;
			}
		}
	}
	if ($check === true) {
		$task = $admintasks[$selTask];
		$code = $selTask;
?>
&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin"><?php echo l('Tools'); ?></a></b><br>
<img src="<?php echo $g_options["imgdir"]; ?>/spacer.gif" width="1" height="8" border="0"><br>

<?php
		include(INCLUDE_PATH . "/admintasks/$code.inc.php");

	}
	else
	{
		// General Settings
?>
&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('General Settings'); ?></b><p>
<?php
		foreach ($admintasks as $code=>$task)
		{
			if ($auth->userdata["acclevel"] >= $task->acclevel && $task->type == "general")
			{
				if ($selTask == $code)
				{
?>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin" name="<?php echo $code; ?>"><?php echo
$task->title; ?></a></b><p>

<form method="POST" action="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=<?php echo $code; ?>#<?php echo $code; ?>">

<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="10%">&nbsp;</td>
	<td width="90%"><?php
	echo $g_options["font_normal"];
	include(INCLUDE_PATH . "/admintasks/".$code.".inc.php");
	echo $g_options["fontend_normal"];
?></td>
</tr>

</table><br><br>
</form>
<?php
				}
				else
				{
?>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/rightarrow.gif" width="6" height="9" border="0" align="middle"
alt="rightarrow.gif"><b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=<?php echo $code; ?>#<?php echo $code;
?>"><?php echo $task->title; ?></a></b><p> <?php
				}
			}
		}


		// Game Settings
?>
&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Game Settings'); ?></b><p>
<?php
		$gamesresult = mysql_query("
			SELECT
				name,
				code
			FROM
				".DB_PREFIX."_Games
		");

		while ($gamedata = mysql_fetch_assoc($gamesresult))
		{
			$gamename = $gamedata["name"];
			$gamecode = $gamedata["code"];

			if ($gamecode == $selGame)
			{
?>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin" name="game_<?php echo $gamecode; ?>"><?php echo $gamename;?></a></b> (<?php echo $gamecode; ?>)<p> <?php
				foreach ($admintasks as $code=>$task)
				{
					if ($auth->userdata["acclevel"] >= $task->acclevel && $task->type == "game")
					{
						if ($selTask == $code)
						{
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&admingame=<?php echo $gamecode; ?>" name="<?php echo $code; ?>"><?php echo $task->title; ?></a></b><p>

<form method="POST" action="<?php echo $g_options["scripturl"]; ?>?mode=admin&admingame=<?php echo $gamecode; ?>&task=<?php echo $code; if(!empty($_GET['advanced'])) { echo "&advanced=1"; } ?>#<?php echo $code; ?>">

<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="10%">&nbsp;</td>
	<td width="90%"><?php
	echo $g_options["font_normal"];
	include(INCLUDE_PATH . "/admintasks/$code.inc.php");
	echo $g_options["fontend_normal"];
?></td>
</tr>

</table><br><br>
</form>
<?php
						}
						else
						{
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/rightarrow.gif" width="6" height="9" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&admingame=<?php echo $gamecode; ?>&task=<?php echo $code; ?>#<?php echo $code; ?>"><?php echo $task->title; ?></a></b><p> <?php
						}
					}
				}
			}
			else
			{
?>
&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/rightarrow.gif" width="6" height="9" border="0" align="middle" alt="rightarrow.gif"><b>&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&admingame=<?php echo $gamecode; ?>#game_<?php echo $gamecode; ?>"><?php echo $gamename;?></a></b> (<?php echo $gamecode; ?>)<p> <?php
			}
		}
	}
	echo "</td>\n";

	if (!$selTask || !$admintasks[$selTask])
	{
		echo "<td width=\"50%\">";
		echo $g_options["font_normal"];
?>
&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Tools'); ?></b>

<ul>
<?php
	foreach ($admintasks as $code=>$task)
	{
		if ($auth->userdata["acclevel"] >= $task->acclevel && $task->type == "tool")
		{
?>	<li><b><a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=<?php echo $code; ?>"><?php echo $task->title; ?></a></b><br>
		<?php echo $task->description; ?><p>
<?php
		}
	}
?>
</ul>
<?php
		echo $g_options["fontend_normal"];
		echo "</td>";
	}
?>
</tr>

</table>
