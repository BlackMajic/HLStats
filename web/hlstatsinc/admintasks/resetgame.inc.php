<?php
/**
 * $Id: resetgame.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/admintasks/resetgame.inc.php $
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

	if ($auth->userdata["acclevel"] < 100) die ("Access denied!");

	// process the reset for this game
	if (isset($_POST['submitReset'])) {

		// we need first the playids for this game
		$query = $db->query("SELECT playerId FROM ".DB_PREFIX."_Players WHERE game = '".$gamecode."'");
		while($result = $db->fetch_row($query)) {
			$players[]= $result[0];
		}
		if(count($players) < 1) {
			die("Fatal error: No players found for this game.");
		}
		$playerIdString = implode(",",$players);


		$query = "SHOW TABLES LIKE '".DB_PREFIX."_Events_%'";
		$result = $db->query($query);
		if ($db->num_rows() < 1) {
			die("Fatal error: No events tables found with query:<p><pre>$query</pre><p>
				There may be something wrong with your hlstats database or your version of MySQL.");
		}

		while (list($table) = $db->fetch_row($result)) {
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
			if($dbt == '".DB_PREFIX."_Events_Frags' || $dbt == '".DB_PREFIX."_Events_Teamkills') {
				if ($db->query("DELETE FROM ".$dbt."
									WHERE killerId IN (".$playerIdString.")
										OR victimId IN (".$playerIdString.")", false)) {
					echo "OK\n";
				}
				else {
					echo "Error for Table:".$dbt."\n";
				}

			}
			else {
				if ($db->query("DELETE FROM ".$dbt."
									WHERE playerId IN (".$playerIdString.")", false)) {
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
			if ($db->query("DELETE FROM ".$dbtGame."
								WHERE game = '".$gamecode."'", false)) {

				echo "OK\n";
			}
			else {
				echo "Error for Table:".$dbtGame."\n";
			}
		}

		echo "<li>Clearing awards ... ";
		$db->query("UPDATE ".DB_PREFIX."_Awards SET d_winner_id=NULL, d_winner_count=NULL
					WHERE game = '".$gamecode."'");
		echo "OK\n";

		echo "</ul>\n";

		echo "Done.<p>";
	}
	else {

?>
<table width="75%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>" style="padding: 10px;">
			<?php echo $g_options["font_normal"]; ?>
			Are you sure you want to reset all statistics for game <b><?php echo $gamename;?></b> ? <br />
			<br />
			All players, clans and events will be deleted from the database.<br />
			(All other admin settings will be retained.)<br />
			<br />
			<b>Note</b> You should kill <b>hlstats.pl</b>
			before resetting the stats. You can restart it after they are reset.<br />
			<br />
			<?php echo $g_options["fontend_normal"]; ?>
		</td>
	</tr>
	<tr>
		<td align="center" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>" style="padding: 10px;">
			<input type="submit" name="submitReset" value="  Reset  " class="submit">
		</td>
	</tr>
</table>
<?php
	}
?>
