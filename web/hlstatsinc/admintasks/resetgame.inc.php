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

	if ($auth->userdata["acclevel"] < 100) die ("Access denied!");

	// process the reset for this game
	if (isset($_POST['submitReset'])) {

		// we need first the playids for this game
		$players = array();
		$query = mysql_query("SELECT playerId FROM ".DB_PREFIX."_Players WHERE game = '".$gamecode."'");
		while($result = mysql_fetch_assoc($query)) {
			$players[]= $result['playerId'];
		}
		if(empty($players)) {
			die("Fatal error: No players found for this game.");
		}
		$playerIdString = implode(",",$players);

		// get the servers for this game
		$serversArr = array();
		$query = mysql_query("SELECT serverId FROM ".DB_PREFIX."_Servers WHERE game = '".$gamecode."'");
		while($result = mysql_fetch_assoc($query)) {
			$serversArr[]= $result['serverId'];
		}
		if(empty($serversArr)) {
			die("Fatal error: No players found for this game.");
		}
		$serversArrString = implode(",",$serversArr);


		$query = mysql_query("SHOW TABLES LIKE '".DB_PREFIX."_Events_%'");
		if (mysql_num_rows($query) < 1) {
			die("Fatal error: No events tables found with query:<p><pre>$query</pre><p>
				There may be something wrong with your hlstats database or your version of MySQL.");
		}

		while (list($table) = mysql_fetch_array($query)) {
			$dbtables[] = $table;
		}

		array_push($dbtables,
			"".DB_PREFIX."_PlayerNames",
			"".DB_PREFIX."_PlayerUniqueIds",
			"".DB_PREFIX."_Players"
		);

		echo "<ul>\n";
		foreach ($dbtables as $dbt) {
			echo "<li>$dbt ... ";
			if($dbt == DB_PREFIX.'_Events_Frags' || $dbt == DB_PREFIX.'_Events_Teamkills') {
				if (mysql_query("DELETE FROM ".$dbt."
									WHERE killerId IN (".$playerIdString.")
										OR victimId IN (".$playerIdString.")")) {
					echo "OK\n";
				}
				else {
					echo "Error for Table:".$dbt."\n";
				}

			}
			elseif($dbt == DB_PREFIX.'_Events_Admin' || $dbt == DB_PREFIX.'_Events_Rcon') {
				if (mysql_query("DELETE FROM ".$dbt."
									WHERE serverId IN (".$serversArrString.")")) {
					echo "OK\n";
				}
				else {
					echo "Error for Table:".$dbt."\n";
				}
			}
			else {
				if (mysql_query("DELETE FROM ".$dbt."
									WHERE playerId IN (".$playerIdString.")")) {
					echo "OK\n";
				}
				else {
					echo "Error for Table:".$dbt."\n";
				}
			}
		}

		// now the tables which we can delete by gamecode
		$dbtablesGamecode [] = "".DB_PREFIX."_Clans";

		foreach ($dbtablesGamecode as $dbtGame) {
			echo "<li>$dbtGame ... ";
			if (mysql_query("DELETE FROM ".$dbtGame."
								WHERE game = '".$gamecode."'")) {

				echo "OK\n";
			}
			else {
				echo "Error for Table:".$dbtGame."\n";
			}
		}

		echo "<li>Clearing awards ... ";
		mysql_query("UPDATE ".DB_PREFIX."_Awards SET d_winner_id=NULL, d_winner_count=NULL
					WHERE game = '".$gamecode."'");
		echo "OK\n";

		echo "</ul>\n";

		echo l("Done"),"<p>";
	}
	else {
?>
<table width="75%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>" style="padding: 10px;">
			<?php echo $g_options["font_normal"]; ?>
			<?php echo l('Are you sure you want to reset all statistics for game'); ?> <b><?php echo $gamename;?></b> ? <br />
			<br />
			<?php echo l('All players, clans and events will be deleted from the database'); ?>.<br />
			<?php echo l('(All other admin settings will be retained)'); ?><br />
			<br />
			<b><?php echo l('Note'); ?></b> <?php echo l('You should kill'); ?> <b>hlstats.pl</b>
			<?php echo l('before resetting the stats. You can restart it after they are reset'); ?>.<br />
			<br />
			<?php echo $g_options["fontend_normal"]; ?>
		</td>
	</tr>
	<tr>
		<td align="center" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>" style="padding: 10px;">
			<input type="submit" name="submitReset" value=" <?php echo l('Reset'); ?>  " class="submit">
		</td>
	</tr>
</table>
<?php
	}
?>
