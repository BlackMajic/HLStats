<?php
/**
 * $Id: index.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/install/index.php $
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

/**
 * make get vars save
 *
 * @param string $text
 * @return string
 */
function sanitize($text) {
	return htmlentities(strip_tags($text), ENT_QUOTES, "UTF-8");
}

// do not report NOTICE warnings
error_reporting(E_ALL ^ E_NOTICE);

session_name('hlstatsInstall');
session_set_cookie_params(1800);
session_start();


//// process the single steps
if(isset($_POST['saveDBSettings'])) {
	// check for required values
	$dbHost = sanitize($_POST['dbHost']);
	$dbName = sanitize($_POST['dbName']);
	$dbUser = sanitize($_POST['dbUser']);
	$dbPass = sanitize($_POST['dbPass']);
	$dbPre = sanitize($_POST['dbPrefix']);

	if($dbHost != "" && $dbName != "" && $dbUser != "" && $dbPass != "") {
		// check if the data is valid
		$db_con = @mysql_connect($dbHost,$dbUser,$dbPass);
		$db_sel = @mysql_select_db($dbName,$db_con);

		if($db_con && $db_sel) {
			// save the data into $_SESSION
			$_SESSION['db']['dbHost'] = $dbHost;
			$_SESSION['db']['dbName'] = $dbName;
			$_SESSION['db']['dbUser'] = $dbUser;
			$_SESSION['db']['dbPass'] = $dbPass;
			$_SESSION['db']['dbPrefix'] = $dbPre;
			$_SESSION['db']['status'] = true;

			$return['db']['success'] = true;
		}
		else {
			$return['db']['error'] = true;
			$return['db']['status'] = "2";
		}
	}
	else {
		$return['db']['error'] = true;
		$return['db']['status'] = "1";
	}
}

if(isset($_POST['saveAdminSettings'])) {
	// check for required values
	$aName = sanitize($_POST['adminName']);
	$aPass = sanitize($_POST['adminPass']);
	$aMail = sanitize($_POST['adminMail']);

	if($aName != "" && $aPass != "" ) {
		// save the data into $_SESSION
		$_SESSION['admin']['adminName'] = $aName;
		$_SESSION['admin']['adminPass'] = $aPass;
		$_SESSION['admin']['adminMail'] = $aMail;

		$return['admin']['success'] = true;
	}
	else {
		$return['admin']['error'] = true;
		$return['admin']['status'] = "1";
	}
}

if(isset($_POST['saveSetSettings'])) {
	// check for required values
	$setDays = sanitize($_POST['setDeleteDays']);

	if($setDays != "") {
		$_SESSION['set']['setDeleteDays'] = $setDays;
		$_SESSION['set']['setMode'] = $_POST['setMode'];
		$_SESSION['set']['setBots'] = $_POST['setBots'];

		if(trim($_POST['setIP']) != "") {
			$_SESSION['set']['setPort'] = $_POST['setPort'];
		}
		else {
			$_SESSION['set']['setPort'] = "27500";
		}

		$_SESSION['set']['setIP'] = $_POST['setIP'];

		$return['set']['success'] = true;
	}
	else {
		$return['set']['error'] = true;
		$return['set']['status'] = "1";
	}
}

// the final process
// create db and settings here.
if(isset($_POST['saveAllSettings'])) {
	// creat db connection
	$db_con = mysql_connect($_SESSION['db']['dbHost'],$_SESSION['db']['dbUser'],$_SESSION['db']['dbPass']) OR die("Unable to connect to DB Server !");
	$db_sel = mysql_select_db($_SESSION['db']['dbName'],$db_con) OR die("Unable to connect to DB Server !");

	// read the hlstats.sql file
	$hlSql = file_get_contents("sql_files/hlstats.sql");
	// since we cant run multiple sql commands within mysql_query
	// // we have to split up
	$hlSql = trim($hlSql);

	// replace the prefix
	$hlSql = str_replace("#DB_PREFIX#",$_SESSION['db']['dbPrefix'],$hlSql);

	$queries = explode(";",$hlSql);
	foreach ($queries as $query) {
	    $query = trim($query);
	    if($query != "") {
	        $run = mysql_query(trim($query));
            if($run != true) {
                break;
            }
	    }
	}
	if($run === true) {
		// continue
		// update admin password
		mysql_query("TRUNCATE ".$_SESSION['db']['dbPrefix']."_Users");
		$query = mysql_query("INSERT INTO ".$_SESSION['db']['dbPrefix']."_Users
								SET `username` = '".$_SESSION['admin']['adminName']."',
									`password` = '".md5($_SESSION['admin']['adminPass'])."',
									`acclevel` = '100',
									`playerId` = 0");
		if($query != false) {
			// change hlstats.conf.inc.php file
			$configFile = "conf.sample.php";
			$fh = fopen($configFile,"r");
			$configContent = fread($fh,filesize($configFile));
			fclose($fh);

			// replace values
			$sArr = array("#DB_NAME#","#DB_USER#","#DB_PASS#","#DB_HOST#","#DELETE_DAYS#","#MODE#","#HIDE_BOTS#","#DB_PREFIX#");
			$rArr = array($_SESSION['db']['dbName'],$_SESSION['db']['dbUser'],$_SESSION['db']['dbPass'],$_SESSION['db']['dbHost'],
							$_SESSION['set']['setDeleteDays'],$_SESSION['set']['setMode'],$_SESSION['set']['setBots'],$_SESSION['db']['dbPrefix']);
			$configContent = str_replace($sArr,$rArr,$configContent);

			$hlConfigFile = "../hlstatsinc/hlstats.conf.inc.php";
			$fh = fopen($hlConfigFile,"w");
			if($fh != false)  {
				fputs($fh,$configContent);

				// create the hlstats.conf file
				$configFile = "conf.sample.pl.php";
				$fhC = fopen($configFile,"r");
				$configContent = fread($fhC,filesize($configFile));
				fclose($fhC);

				// replace values
				$sArr = array("#DB_NAME#","#DB_USER#","#DB_PASS#","#DB_HOST#","#DELETE_DAYS#","#MODE#","#HLS_PORT#","#HLS_IP#","#DB_PREFIX#","#ADMIN_MAIL#");
				$rArr = array($_SESSION['db']['dbName'],$_SESSION['db']['dbUser'],$_SESSION['db']['dbPass'],$_SESSION['db']['dbHost'],
							$_SESSION['set']['setDeleteDays'],$_SESSION['set']['setMode'],$_SESSION['set']['setPort'],$_SESSION['set']['setIP'],$_SESSION['db']['dbPrefix'],
							$_SESSION['admin']['adminMail']);
				$configContent = str_replace($sArr,$rArr,$configContent);

				$hlConfigFile = "hlstats.conf";
				$fhC = fopen($hlConfigFile,"w");
				if($fhC != false)  {
					fputs($fhC,$configContent);

					$return['final']['success'] = true;

					// lock the installer !!
					rename("./htaccess.php","./.htaccess");
				}
				else {
					$return['final']['error'] = true;
					$return['final']['status'] = "4";
				}
				fclose($fhC);
			}
			else {
				$return['final']['error'] = true;
				$return['final']['status'] = "3";
			}
			fclose($fh);
		}
		else {
			$return['final']['error'] = true;
			$return['final']['status'] = "2";
		}
	}
	else {
		$return['final']['error'] = true;
		$return['final']['status'] = "1";
	}
}


header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<title>HLstats Installer</title>
	<style type="text/css">
		body {
			padding: 0;
			margin: 0;
			color: #666666;
			background-color: #fff;
			font-family: Arial,Verdana,Helvetica,sans-serif;
			font-size: 12px;
		}
		a {
			color: #666666;
			text-decoration: underline;
		}
		a:hover {
			color: #666666;
			text-decoration: none;
		}

		td,input,select {
			font-family: Arial,Verdana,Helvetica,sans-serif;
			font-size: 12px;
		}

		input,select {
			border: 1px solid #cccccc;
		}

		#mainBox {
			margin: 40px auto;
			width: 700px;
			border: 1px solid #cccccc;
		}
		#headBox {
			background-color: #eeeeee;
			height: 20px;
			border-bottom: 1px solid #ccc;
		}
		.headBoxEntry {
			float: left;
			font-size: 14px;
			font-weight: bold;
			padding: 2px;
		}
		.headBoxEntryOver {
			float: left;
			font-size: 14px;
			font-weight: bold;
			padding: 2px;
			background-color: #ccc;
		}
		.headSeperator {
			float: left;
			width: 2px;
			height: 20px;
			background-color: #ccc;
			margin: 0px 2px 0px 2px;
		}

		#contentBox {
			padding: 2px 10px 2px 20px;
		}

		#footBox {
			background-color: #eeeeee;
			height: 15px;
			border-top: 1px solid #ccc;
			font-size: 10px;
			text-align: center;
		}

		.headline {
			font-weight: bold;
			font-size: 16px;
			border-bottom: 1px solid #838382;
			margin: 10px;
		}

		.error {
			border: 1px solid red;
			margin: 5px;
			padding: 2px;
		}
		.success {
			border: 1px solid green;
			margin: 5px;
			padding: 5px;
			background-color: #6be16b;
		}
	</style>
</head>
<body>
	<div id="mainBox">
		<div id="headBox">
			<div class="headBoxEntry<?php if($_GET['step'] == "") echo "Over"; ?>">
				<a href="index.php">Start</a>
			</div>
			<div class="headSeperator">&nbsp;</div>
			<div class="headBoxEntry<?php if($_GET['step'] == "1") echo "Over"; ?>">
				<a href="index.php?step=1">Step 1</a>
			</div>
			<div class="headSeperator">&nbsp;</div>
			<div class="headBoxEntry<?php if($_GET['step'] == "2") echo "Over"; ?>">
				<a href="index.php?step=2">Step 2</a>
			</div>
			<div class="headSeperator">&nbsp;</div>
			<div class="headBoxEntry<?php if($_GET['step'] == "3") echo "Over"; ?>">
				<a href="index.php?step=3">Step 3</a>
			</div>
			<div class="headSeperator">&nbsp;</div>
			<div class="headBoxEntry<?php if($_GET['step'] == "4") echo "Over"; ?>">
				<a href="index.php?step=4">Step 4</a>
			</div>
			<div class="headSeperator">&nbsp;</div>
			<span style="clear: both;"></span>
		</div>
		<div id="contentBox">
		<?php
			switch ($_GET['step']) {
				case '1':
				// set default prefix
				if($_SESSION['db']['dbPrefix'] == "") {
					$dbPrefix = "hlstats";
				}
				else {
					$dbPrefix = $_SESSION['db']['dbPrefix'];
				}
				?>
				<div class="headline">Step 1 Database settings</div>
				Input your Database settings for your database which you are going to use for
				hlstats. You should have one already, since this installer can't create one for you.<br />
				If you don't know your db hostname try localhost. If this is not working ask your webhoster
				about the database hostname.<br />
				<br />
				<?php
					if($return['db']['error']) {
						echo "<div class='error'>";
						if($return['db']['status'] == "1") {
							echo "Please provide all the input fields !";
						}
						else if($return['db']['status'] == "2") {
							echo "Please review your DB settings. !<br />";
							echo "No Connection could be made to the db server.";
						}
						echo "</div>";
					}
					elseif($return['db']['success']) {
						echo "<div class='success'>";
						echo "All Data saved and checked. You can continue to the next step.<br /><br />";
						echo "<a href='index.php?step=2'>Continue with Step 2 &#187;</a>";
						echo "</div>";
						$_SESSION['step1'] = "run";
					}
				?>
				<form method="post" action="">
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td width="100px">
							Hostname
						</td>
						<td>
							<input type="text" name="dbHost" value="<?php echo $_SESSION['db']['dbHost']; ?>" />
						</td>
					</tr>
					<tr>
						<td>Database Name</td>
						<td>
							<input type="text" name="dbName" value="<?php echo $_SESSION['db']['dbName']; ?>" />
						</td>
					</tr>
					<tr>
						<td>Database User</td>
						<td>
							<input type="text" name="dbUser" value="<?php echo $_SESSION['db']['dbUser']; ?>" />
						</td>
					</tr>
					<tr>
						<td>Database Passwort</td>
						<td>
							<input type="text" name="dbPass" value="<?php echo $_SESSION['db']['dbPass']; ?>" />
						</td>
					</tr>
					<tr>
						<td>Database prefix</td>
						<td>
							<input type="text" name="dbPrefix" value="<?php echo $dbPrefix; ?>" /> <i>(a underscore will be added automtically)</i>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" name="saveDBSettings" value="Save" />
						</td>
					</tr>
				</table>
				</form>
				<?php
				break;

				case '2':
					if($_SESSION['step1'] == "run") {
				?>
					<div class="headline">Step 2 Admin User settings</div>
					Choose your Admin username and password for your HLstats admin area.<br />
					<br />
					<?php
						if($return['admin']['error']) {
							echo "<div class='error'>";
							if($return['admin']['status'] == "1") {
								echo "Please provide all the input fields !";
							}
							echo "</div>";
						}
						elseif($return['admin']['success']) {
							echo "<div class='success'>";
							echo "All Data saved and checked. You can continue to the next step.<br /><br />";
							echo "<a href='index.php?step=3'>Continue with Step 3 &#187;</a>";
							echo "</div>";
							$_SESSION['step2'] = "run";
						}
					?>
					<form method="post" action="">
						<table cellpadding="2" cellspacing="0" border="0">
							<tr>
								<td width="100px">
									Username:
								</td>
								<td>
									<input type="text" name="adminName" value="<?php echo $_SESSION['admin']['adminName']; ?>" />
								</td>
							</tr>
							<tr>
								<td>
									Password:
								</td>
								<td>
									<input type="text" name="adminPass" value="<?php echo $_SESSION['admin']['adminPass']; ?>" />
								</td>
							</tr>
							<tr>
								<td>
									E-Mail:
								</td>
								<td>
									<input type="text" name="adminMail" value="<?php echo $_SESSION['admin']['adminMail']; ?>" />
									<i>(sending error messages)</i>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="submit" name="saveAdminSettings" value="Save" />
								</td>
							</tr>
						</table>
					</form>
				<?php
					}
					else {
				?>
					<div class="error">
						Please run Step 1 first !
					</div>
				<?php
					}
				break;

				case '3':
					if($_SESSION['step1'] == "run" && $_SESSION['step2'] == "run") {

					if($_SESSION['set']['setDeleteDays'] == "") {
						$_SESSION['set']['setDeleteDays'] = "5";
					}
				?>
					<div class="headline">Step 3 HLstats settings</div>
					Set your HLstats settings right here.<br />
					<br />
					<?php
						if($return['set']['error']) {
							echo "<div class='error'>";
							if($return['set']['status'] == "1") {
								echo "Please provide all the input fields !";
							}
							echo "</div>";
						}
						elseif($return['set']['success']) {
							echo "<div class='success'>";
							echo "All Data saved and checked. You can continue to the next step.<br /><br />";
							echo "<a href='index.php?step=4'>Continue with Step 4 &#187;</a>";
							echo "</div>";
							$_SESSION['step3'] = "run";
						}
					?>
					<form method="post" action="">
						<table cellpadding="2" cellspacing="0" border="0">
							<tr>
								<td width="160px">
									HLstats Daemon Port
								</td>
								<td>
									<input type="text" name="setPort" size="6" value="<?php echo $_SESSION['set']['setPort']; ?>" />
									The port to which the gameserver sends the log data (default is 27500). <br />
									Leave if empty to use the default one.
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td>
									HLstats Daemon IP
								</td>
								<td>
									<input type="text" name="setIP" value="<?php echo $_SESSION['set']['setIP']; ?>" /><br />
									The ip from the box on which the daemon runs.<br />
									Leave it empty for automatic. It could be that your provider forces you to use one !
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td>
									Delete Days:
								</td>
								<td>
									<input type="text" name="setDeleteDays" size="2" value="<?php echo $_SESSION['set']['setDeleteDays']; ?>" />
									How long events for each player should be kept in the database.<br />
									The value can be 0. Which means the data will be kept forever.
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td valign="top">
									Mode:
								</td>
								<td>
									<select name="setMode">
										<option value="Normal" <?php if($_SESSION['set']['setMode'] == "Normal") echo "selected"; ?>>Normal</option>
										<option value="NameTrack" <?php if($_SESSION['set']['setMode'] == "NameTrack") echo "selected"; ?>>NameTrack</option>
										<option value="LAN" <?php if($_SESSION['set']['setMode'] == "LAN") echo "selected"; ?>>LAN</option>
									</select>
									Where does hlstats get its data from ?<br />
									Internet = Normal<br />
									Local area network = LAN<br />
									Tracking by Name = NameTrack
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td width="100px">
									Hide Bots:
								</td>
								<td>
									<select name="setBots">
										<option value="0" <?php if($_SESSION['set']['setBots'] == "0") echo "selected"; ?>>No</option>
										<option value="1" <?php if($_SESSION['set']['setBots'] == "1") echo "selected"; ?>>Yes</option>
									</select>
									Hide Bots from ranking.
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="submit" name="saveSetSettings" value="Save" />
								</td>
							</tr>
						</table>
					</form>
				<?php
					}
					else {
				?>
					<div class="error">
						Please run Step 1 und Step 2 first !
					</div>
				<?php
					}
				break;

				case '4':
					if($_SESSION['step1'] == "run" && $_SESSION['step2'] == "run" && $_SESSION['step3'] == "run") {
				?>
					<div class="headline">Step 4 Settings overview</div>
					<br />
					<?php
						if($return['final']['error']) {
							echo "<div class='error'>";
							if($return['final']['status'] == "1") {
								echo "Failed to import hlstats.sql file !!!";
							}
							elseif($return['final']['status'] == "2") {
								echo "Can't create admin user !!";
							}
							elseif($return['final']['status'] == "3") {
								echo "Can't write to config file !!";
							}
							elseif($return['final']['status'] == "4") {
								echo "Can't write to daemon config file !!";
							}
							echo "</div>";
						}
						elseif($return['final']['success']) {
						?>
							<div class='success'>
								All Data saved and checked.<br />
								The configuration files are saved and ready to use.<br />
								<br />
								The installer has been locked and have to be renamed if you want to run this installer again.<br />
								See docomentation for more details.
								<br />
								<br />
								<b>The configuration file for the daemon has been saved here:</b><br />
								-- <?php echo dirname(__FILE__)."/hlstats.conf"; ?> --<br />
								<br />
								Use this file and move it to the location of your daemon folder and overwrite the
								existing configuration file.
							</div>
						<?php
						}
					?>

					<form method="post" action="">
						<table cellpadding="2" cellspacing="0" border="0">
							<tr>
								<td colspan="2">
									<b>Database Settings</b>
								</td>
							</tr>
							<tr>
								<td width="100px">
									DB Host
								</td>
								<td>
									<?php echo $_SESSION['db']['dbHost']; ?>
								</td>
							</tr>
							<tr>
								<td width="100px">
									DB Name
								</td>
								<td>
									<?php echo $_SESSION['db']['dbName']; ?>
								</td>
							</tr>
							<tr>
								<td width="100px">
									DB User
								</td>
								<td>
									<?php echo $_SESSION['db']['dbUser']; ?>
								</td>
							</tr>
							<tr>
								<td width="100px">
									DB Password
								</td>
								<td>
									<?php echo $_SESSION['db']['dbPass']; ?>
								</td>
							</tr>
							<tr>
								<td>DB prefix</td>
								<td>
									<?php echo $_SESSION['db']['dbPrefix']; ?>_ <i>(a underscore will be added automatically)</i>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<b>Admin Access</b>
								</td>
							</tr>
							<tr>
								<td width="100px">
									Admin username
								</td>
								<td>
									<?php echo $_SESSION['admin']['adminName']; ?>
								</td>
							</tr>
							<tr>
								<td width="100px">
									Admin password
								</td>
								<td>
									<?php echo $_SESSION['admin']['adminPass']; ?>
								</td>
							</tr>
							<tr>
								<td width="100px">
									Admin Mail
								</td>
								<td>
									<?php echo $_SESSION['admin']['adminMail']; ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<b>Settings</b>
								</td>
							</tr>
							<tr>
								<td width="100px">
									Delete Days
								</td>
								<td>
									<?php echo $_SESSION['set']['setDeleteDays']; ?>
								</td>
							</tr>
							<tr>
								<td width="100px">
									Mode
								</td>
								<td>
									<?php echo $_SESSION['set']['setMode']; ?>
								</td>
							</tr>
							<tr>
								<td width="100px">
									Hide Bots
								</td>
								<td>
									<?php echo $_SESSION['set']['setBots']; ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="submit" name="saveAllSettings" value="Save to configuration file and install tables" />
								</td>
							</tr>
						</table>
					</form>
				<?php
					}
					else {
				?>
					<div class="error">
						Please run all the previous steps first !
					</div>
				<?php
					}
				break;

				default:
					// unset session
					session_destroy();
					$_SESSION = array();

					// check if we have access to the config files.
					$fhConfSample = is_readable("conf.sample.php");
					$fhConfDaemon = is_readable("conf.sample.pl.php");
					$fhConfWeb = is_writeable("../hlstatsinc/hlstats.conf.inc.php");

				?>
				<div class="headline">Welcome</div>
				This installer will guide you through the steps to install your copy of HLstats.<br />
				<br />
				This Installer is build to run once. If you repeat the install process all configured data will
				be lost. AND all the stats.....<br />
				<br />
				<div style="background-color: #FFFF88; border: 1px solid #CDEB8B; padding: 3px;">
					<b>The installer needs access to:</b><br />
					<ol>
						<li>
							Config sample files :<br />
							<?php
								if($fhConfSample) {
									echo '<span style="color: green;">conf.sample.php OK</span>';
								}
								else {
									echo '<b>'.dirname(__FILE__).'/conf.sample.php</b> <span style="color: red;">is not readable !</span>';
								}
								echo "<br />";
								if($fhConfDaemon) {
									echo '<span style="color: green;">conf.sample.pl.php OK</span>';
								}
								else {
									echo '<b>'.dirname(__FILE__).'/conf.sample.pl.php</b> <span style="color: red;">is not readable !</span>';
								}

							?>
							</li>
						<li>
							Real config file web :<br />
							<?php
								if($fhConfWeb) {
									echo '<span style="color: green;">hlstats.conf.inc.php OK</span>';
								}
								else {
									echo '<b>'.str_replace("install/","",dirname(__FILE__)).'/include/hlstats.conf.inc.php</b> <span style="color: red;">is not writeable !</span>';
								}
							?>
						</li>
						<li>
							The configuration file for the daemon will be generated
						</li>
					</ol>
					If there is <span style="color: red">something red</span> make sure the webserver has read/write permission to those files.<br />
				</div>
				<br />
				Ok lets start:<br />
				<br />
				<a href="index.php?step=1">Start &#187;</a>
				<?php
			}
		?>
		</div>
		<div id="footBox">
			&copy; 2008 <a href="http://www.hlstats-community.org">HLstats-Community.org</a>
		</div>
	</div>
</body>
</html>
