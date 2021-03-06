<?php
/**
 * $Id: tools_game.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstatsinc/admintasks/tools_game.inc.php $
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

	if ($auth->userdata["acclevel"] < 100) die ("Access denied!");
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="hlstatsimg/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo $task->title; ?></b><p>

<?php
	if (isset($_POST['submitAdd'])) {
		if($_POST['newGame'] != "") {
			$inputStr = trim($_POST['newGame']);
			$inputStr = stripslashes($inputStr);
			$inputStrArr = explode("\n",$inputStr);
			$i=0;
			foreach ($inputStrArr as $line) {
				$line = trim($line);
				if(!preg_match("/^#/",$line) && $line != "") {
					$query = mysql_query($line);
					if(!$query) {
						echo("Query Failed: ".$line);
						$i++;
					}
				}
			}
			if($i>0) {
				echo "ERROR while importing this game!";
			}
			else {
				echo "import done !";
			}
		}
		else {
			echo "No data given !";
		}
	}
	elseif(isset($_POST['submitDelete'])) {
		if($_POST['gameToDelete'] != "") {

			// we need first the playids for this game
			$query = mysql_query("SELECT playerId FROM ".DB_PREFIX."_Players WHERE game = '".$_POST['gameToDelete']."'");
			while($result = mysql_fetch_assoc($query)) {
				$players[]= $result['playerId'];
			}
			if(count($players) > 0) {
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

				echo '<ul>';
				foreach($dbtables as $table) {
					echo "<li>";
					if($table == DB_PREFIX.'_Events_Frags' || $table == DB_PREFIX.'_Events_Teamkills') {
						if(mysql_query("DELETE FROM ".$table."
										WHERE killerId IN (".$playerIdString.")
											OR victimId IN (".$playerIdString.")", false)) {
							echo $table." ok.";
						}
						else {
							echo $table." ERROR";
						}
					}
					else {
						if(mysql_query("DELETE FROM ".$table."
										WHERE playerId IN (".$playerIdString.")", false)) {
							echo $table." ok.";
						}
						else {
							echo $table." ERROR";
						}
					}
					echo '</li>';
				}
			}

			$gameTables = array(DB_PREFIX.'_Actions',DB_PREFIX.'_Awards',
									DB_PREFIX.'_Clans',
									DB_PREFIX.'_Roles', DB_PREFIX.'_Servers',
									DB_PREFIX.'_Teams', DB_PREFIX.'_Weapons');
			foreach($gameTables as $gt) {
				echo "<li>";
				if(mysql_query("DELETE FROM ".$gt." WHERE game = '".$_POST['gameToDelete']."'")) {
					echo $gt." ok";
				}
				else {
					echo $gt." ERROR";
				}
				echo "</li>";
			}

			echo "<li>";
			if(mysql_query("DELETE FROM ".DB_PREFIX."_Games WHERE code='".$_POST['gameToDelete']."'")) {
				echo "".DB_PREFIX."_Games ok";
			}
			else {
				echo "hlstast_Games ERROR";
			}
			echo "</li>";

			// delete the players
			echo "<li>";
			if(mysql_query("DELETE FROM ".DB_PREFIX."_Players WHERE playerId IN (".$playerIdString.")")) {
				echo "".DB_PREFIX."_Players ok";
			}
			else {
				echo "".DB_PREFIX."_Players ERROR";
			}
			echo "</li>";
			echo "</ul>";
			echo "done";
		}
	}
	else {
		// get the games from the db
		$query = mysql_query("SELECT code,name FROM ".DB_PREFIX."_Games ORDER BY name");
		while ($result = mysql_fetch_assoc($query)) {
			$gamesArr[$result['code']] = $result['name'];
		}

		// get the available gamesupport files.
		$sqlDir = getcwd()."/install/sql_files";
		if(file_exists($sqlDir)) {
			$addMode = "select";
			// read the gamesupport files
			$files = array();
			$dh = opendir($sqlDir);
			while(false !== ($file = readdir($dh))) {
				if($file[0] =="." || $file[0] =="..") continue;

				if(is_file($sqlDir."/".$file) && strstr($file,"gamesupport_")) {
					$tmp = str_replace(array("gamesupport_",".sql"),"",$file);
					$tmp = str_replace("_"," ",$tmp);
					$files[$file] = $tmp;
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
		else {
			// no game support files found
			// privode upload field
			$addMode = "upload";
		}
?>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td bgcolor="<?php echo $g_options["table_border"]; ?>">
			<table width="100%" border="0" cellspacing="1" cellpadding="10">
				<tr>
					<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?>
						Here you can either add support for a game with a gamesupport_*.sql file or
						remove support for an existing game.<br />
						<b>IF you remove support for a game, all data associated with this game will be deleted !</b><br />
						<br />
						<?php echo $g_options["fontend_normal"]; ?>
					</td>
				</tr>
				<tr>
					<td><?php echo $g_options["font_normal"]; ?>
						<b>Delete support for a game.</b>
						<?php echo $g_options["fontend_normal"]; ?>
					</td>
				</tr>
				<tr>
					<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
						<?php echo $g_options["font_normal"]; ?>
						<b>IMPORTANT:</b><br />
						IF you remove support for a game, all data associated with this game will be deleted !<br />
						<br />
						<form  method="post" action="">
							<select name="gameToDelete">
								<option value="">Please select</option>
								<?php
								foreach($gamesArr as $key=>$value) {
									echo '<option value="'.$key.'">'.$value.'</option>';
								}
								?>
							</select>
							<center><input type="submit" name="submitDelete" value="  DELETE GAME  "></center><br />
							<br />
						</form>
						<?php echo $g_options["fontend_normal"]; ?>
					</td>
				</tr>
				<tr>
					<td><?php echo $g_options["font_normal"]; ?>
						<b>Add support for a game.</b><br />
						After creating a game, you will be able to configure servers, awards, etc. for that game under Game Settings.
						<?php echo $g_options["fontend_normal"]; ?>
					</td>
				</tr>
				<tr>
					<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
						<?php echo $g_options["font_normal"]; ?>
						<form  method="post" action="">
							<?php
							switch ($addMode) {
								case 'upload':
								?>
								<input type="hidden" name="addMode" value="upload" />
								Choose a gamesupport_*.sql file, open it and paste the whole content into the following input field.<br />
								<b>Make sure you know what you are doing, anything wrong can damage your database !</b><br />
								<br />
								<textarea name="newGame" style="width: 100%; height: 200px;"></textarea><br />
								<br />
								<p style="text-align: center;"><input type="submit" name="submitAdd" value="  UPLOAD AND ADD NEW GAME  "></p><br />
								<br />
								<?php
								break;

								case 'select':
								?>
								<input type="hidden" name="addMode" value="select" />
								Choose a gamesupport file from the install directory to install.<br />
								<br />
								<select name="newGame">
									<?php
										if($gameFiles !== false) {
											foreach ($gameFiles as $gameF=>$gameName) {
												echo "<option value='".$gameF."'>".$gameName."</option>\n";
											}
										}
										else {
											echo "<option value=''>No files available</option>";
										}
									?>
								</select>
								<p style="text-align: center;"><input type="submit" name="submitAdd" value=" ADD NEW GAME  "></p><br />
								<?php
								break;

								default:
									// do nothing
							}
							?>
						</form>
						<?php echo $g_options["fontend_normal"]; ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
	}
?>
