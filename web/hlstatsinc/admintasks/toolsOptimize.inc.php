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
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo $task->title; ?></b><p>

<?php
	$upgrade = false;
	if(!empty($_GET['upgrade'])) {
		if($_GET['upgrade'] == "yes") {
			$upgrade = true;
		}
	}
	if ($upgrade === true) {
		$result = mysql_query("SHOW TABLES");

		echo "Upgrading all tables to MyISAM format:<ul>\n";
		while (list($table) = mysql_fetch_array($result)) {
			echo "<li>$table ... ";
			mysql_query("ALTER TABLE $table TYPE=MYISAM");
			echo "OK\n";
		}
		echo "</ul>\n";

		echo "Done.<p>";
?>
Back to <a href="index.php?mode=admin&task=toolsOptimize">Optimize Database</a><p>
<?php
	}
	else {
?>

Optimizing tables...<?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table><p>

<?php
		flush();

		$result = mysql_query("SHOW TABLES");
		$dbtables = '';

		while (list($table) = mysql_fetch_array($result))
		{
			if ($dbtables) $dbtables .= ", ";
			$dbtables .= $table;
		}

		$tableOptimize = new Table(
			array(
				new TableColumn(
					"Table",
					"Table",
					"width=30&sort=no"
				),
				new TableColumn(
					"Op",
					"Operation",
					"width=12&sort=no"
				),
				new TableColumn(
					"Msg_type",
					"Msg. Type",
					"width=12&sort=no"
				),
				new TableColumn(
					"Msg_text",
					"Message",
					"width=46&sort=no"
				)
			),
			"Table",
			"Table",
			"Msg_type",
			false,
			9999
		);

		$result = mysql_query("OPTIMIZE TABLE $dbtables");

		$tableOptimize->draw($result, mysql_num_rows($result), 80);
?>
<p>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">

<tr>
	<td><?php echo $g_options["font_normal"]; ?>Analyzing tables...<?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table><p>

<?php
		$tableAnalyze = new Table(
			array(
				new TableColumn(
					"Table",
					"Table",
					"width=30&sort=no"
				),
				new TableColumn(
					"Op",
					"Operation",
					"width=12&sort=no"
				),
				new TableColumn(
					"Msg_type",
					"Msg. Type",
					"width=12&sort=no"
				),
				new TableColumn(
					"Msg_text",
					"Message",
					"width=46&sort=no"
				)
			),
			"Table",
			"Table",
			"Msg_type",
			false,
			9999
		);

		$result = mysql_query("ANALYZE TABLE $dbtables");

		$tableAnalyze->draw($result, mysql_num_rows($result), 80);
?>
<p>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">

<tr>
	<td><?php echo $g_options["font_normal"]; ?><a href="index.php?mode=admin&task=toolsOptimize&upgrade=yes&<?php echo strip_tags(SID)?>"><?php echo l('Click here'); ?></a> <?php echo l('if you get "table handler does not support check/repair" above'); ?>".<?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table>
<?php
	}
?>
