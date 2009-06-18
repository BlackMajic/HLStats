<?php
/**
 * $Id: tools_reset.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstatsinc/admintasks/tools_reset.inc.php $
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
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo $task->title; ?></b><p>

<?php
	if (isset($_POST['confirm'])) {
		$query = "SHOW TABLES LIKE '".DB_PREFIX."_Events_%'";

		$result = $db->query($query);
		if ($db->num_rows() < 1) die("Fatal error: No events tables found with query:<p><pre>$query</pre><p>There may be something wrong with your hlstats database or your version of MySQL.");

		while (list($table) = $db->fetch_row($result))
		{
			$dbtables[] = $table;
		}

		array_push($dbtables,
			DB_PREFIX."_Clans",
			DB_PREFIX."_PlayerNames",
			DB_PREFIX."_PlayerUniqueIds",
			DB_PREFIX."_Players"
		);

		echo "<ul>\n";
		foreach ($dbtables as $dbt) {
			echo "<li>$dbt ... ";
			if ($db->query("TRUNCATE TABLE $dbt", false)) {
				echo "OK\n";
			}
			else {
				$db->query("DELETE FROM $dbt");
				echo "OK\n";
			}
		}

		echo "<li>Clearing awards ... ";
		$db->query("UPDATE ".DB_PREFIX."_Awards SET d_winner_id=NULL, d_winner_count=NULL");
		echo "OK\n";

		echo "</ul>\n";

		echo "Done.<p>";

		// add a last reset row into hlstats_options
		$db->query("UPDATE ".DB_PREFIX."_Options SET value = '".time()."'
						WHERE `keyname` = 'reset_date'");
	}
	else {
		// create last reset date
		if($g_options['reset_date'] == "0") {
			$g_options['reset_date'] = "Never";
		}
		else {
			$g_options['reset_date'] = date("d.m.Y",$g_options['reset_date']);
		}
?>

<form method="post" action="">
<table width="60%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td bgcolor="<?php echo $g_options["table_border"]; ?>">
			<table width="100%" border="0" cellspacing="1" cellpadding="10">

			<tr>
				<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?>
					Are you sure you want to reset all statistics? All players, clans and events will be deleted from the database.
					(All other admin settings will be retained.)<br />
					<br />
					<b>Note</b> You should kill <b>hlstats.pl</b>
					before resetting the stats. You can restart it after they are reset.
					<input type="hidden" name="confirm" value="1">
					<center><input type="submit" value="  Reset Stats  "></center>
					<?php echo $g_options["fontend_normal"]; ?>
				</td>
			</tr>
			<tr>
				<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?>
					The last reset was: <b><?php echo $g_options['reset_date']; ?></b>
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?php
	}
?>
