<?php
/**
 * add and delete gamesupport
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

$return = false;

if (isset($_POST['sub']['newgame'])) {
	$newGame = trim($_POST['newGame']);
	if(!empty($newGame)) {
		// read the gamesupport_file
		$check = validateInput($newGame,'nospace');
		if(file_exists("hlstatsinc/sql_files/".$newGame) && $check === true) {
			$sqlContent = file_get_contents("hlstatsinc/sql_files/".$_POST['newGame']);
			$sqlContent = str_replace(array("\n","\t","\r"),array("\n"),$sqlContent);

			// replace the table prefix with the constant
			$sqlContent = str_replace("++DB_PREFIX++",DB_PREFIX,$sqlContent);
			$sqlContentArr = explode("\n",$sqlContent);

			$i=0;
			foreach ($sqlContentArr as $line) {
				$line = trim($line);
				if(!preg_match("/^#/",$line) && $line != "") {
					$query = mysql_query($line);
					if(!$query) {
						echo("Query Failed: ".$line);
						$i++;
						break;
					}
				}
			}

			if($i>0) {
				$return['status'] = "1";
				$return['msg'] = l("ERROR while importing this game").' !';
			}
			else {
				header('Location: index.php?mode=admin&task=games');
			}
		}
	}
}
elseif(isset($_POST['sub']['deleteGame'])) {
	$gametodelete = trim($_POST['gameToDelete']);
	if(!empty($gametodelete)) {

		// we need first the playids for this game
		$players = array();
		$query = mysql_query("SELECT playerId FROM ".DB_PREFIX."_Players
								WHERE game = '".mysql_escape_string($gametodelete)."'");
		while($result = mysql_fetch_assoc($query)) {
			$players[]= $result['playerId'];
		}
		if(!empty($players)) {
			#die("Fatal error: No players found for this game.");
			$playerIdString = implode(",",$players);
			$query = mysql_query("SHOW TABLES LIKE '".DB_PREFIX."_Events_%'");
			if (mysql_num_rows($query) < 1) {
				die("Fatal error: No events tables found with query:<p><pre>$query</pre><p>
					There may be something wrong with your hlstats database or your version of MySQL.");
			}

			$dbtables = array();
			while (list($table) = mysql_fetch_array($query)) {
				$dbtables[] = $table;
			}

			foreach($dbtables as $table) {
				if($table == '".DB_PREFIX."_Events_Frags' || $table == '".DB_PREFIX."_Events_Teamkills') {
					mysql_query("DELETE FROM `".$table."`
									WHERE killerId IN (".$playerIdString.")
										OR victimId IN (".$playerIdString.")");
				}
				else {
					mysql_query("DELETE FROM `".$table."`
									WHERE playerId IN (".$playerIdString.")");
				}
			}
		}

		$gameTables = array(DB_PREFIX.'_Actions',DB_PREFIX.'_Awards',
								DB_PREFIX.'_Clans',
								DB_PREFIX.'_Roles', DB_PREFIX.'_Servers',
								DB_PREFIX.'_Teams', DB_PREFIX.'_Weapons');
		foreach($gameTables as $gt) {
			$do = mysql_query("DELETE FROM ".$gt." WHERE game = '".$_POST['gameToDelete']."'");
			if($do === false) {
				echo $gt,' ',l("ERROR");
			}
		}

		mysql_query("DELETE FROM `".DB_PREFIX."_Games`
						WHERE code='".mysql_escape_string($gametodelete)."'");

		// delete the players
		if(!empty($players)) {
			mysql_query("DELETE FROM ".DB_PREFIX."_Players WHERE playerId IN (".$playerIdString.")");
		}

		header('Location: index.php?mode=admin&task=games');
	}
}
else {
	// get the games from the db
	$query = mysql_query("SELECT code,name
							FROM `".DB_PREFIX."_Games`
							ORDER BY `name`");
	$gamesArr = array();
	while ($result = mysql_fetch_assoc($query)) {
		$gamesArr[$result['code']] = $result['name'];
	}

	// get the available gamesupport files.
	$sqlDir = "hlstatsinc/sql_files";
	if(file_exists($sqlDir)) {
		// read the gamesupport files
		$files = array();
		$dh = opendir($sqlDir);
		while(false !== ($file = readdir($dh))) {
			if($file[0] =="." || $file[0] =="..") continue;

			if(is_file($sqlDir."/".$file) && strstr($file,"gamesupport_")) {
				$tmp = str_replace(array("gamesupport_",".sql"),"",$file);
				$tmpArr = explode("__",$tmp);
				if(!array_key_exists($tmpArr[1],$gamesArr)) {
					// show only games which are not already installed
					$tmp = str_replace("_"," ",$tmpArr[0]);
					$files[$file] = $tmp;
				}
			}
		}
		closedir($dh);

		if(count($files) > 0) {
			ksort($files);
			$gameFiles = $files;
		}
		else {
			$gameFiles = false;
		}
	}
}

pageHeader(array(l("Admin"),l('Gamesupport')), array(l("Admin")=>"index.php?mode=admin",l('Gamesupport')=>''));
?>
<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?mode=admin"; ?>"><?php echo l('Back to admin overview'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l('Gamesupport'); ?></h1>
	<h2><?php echo l('Add support for a game'); ?></h2>
	<?php
		if(!empty($return)) {
			if($return['status'] === "1") {
				echo '<div class="error">',$return['msg'],'</div>';
			}
			elseif($return['status'] === "2") {
				echo '<div class="success">',$return['msg'],'</div>';
			}
		}
	?>
	<p>
		<?php echo l('After creating a game, you will be able to configure servers, awards, etc. for that game under Game Settings'); ?><br />
		<br />
		<?php echo l('Choose a gamesupport file from the install directory to install'); ?>.<br />
		<?php echo l('The list contains only games which are not installed yet'); ?>.
	</p>
	<form method="post" action="">
		<select name="newGame">
			<?php
				if($gameFiles !== false) {
					foreach ($gameFiles as $gameF=>$gameName) {
						echo "<option value='".$gameF."'>".$gameName."</option>\n";
					}
				}
				else {
					echo "<option value=''>",l('No files available'),"</option>";
				}
			?>
		</select>
		<button type="submit" name="sub[newgame]" title="<?php echo l('Add game'); ?>">
			<?php echo l('Add game'); ?>
		</button>
	</form><br />
	<br />
	<h2><?php echo l('Delete support for a game'); ?></h2>
	<p>
		<b><?php echo l('IMPORTANT'); ?>:</b><br />
		<?php echo l('IF you remove support for a game, all data associated with this game will be deleted'); ?> !
	</p>
	<form  method="post" action="">
		<select name="gameToDelete">
			<option value=""><?php echo l('Please select'); ?></option>
			<?php
			foreach($gamesArr as $key=>$value) {
				echo '<option value="'.$key.'">'.$value.'</option>';
			}
			?>
		</select>
		<button type="submit" name="sub[deleteGame]" title="<?php echo l('DELETE GAME'); ?>">
			<?php echo l('DELETE GAME'); ?>
		</button>
	</form>
</div>
