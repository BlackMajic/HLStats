<?php
/**
 * $Id: tools_optimize.inc.php 439 2008-04-09 12:29:18Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/admintasks/tools_optimize.inc.php $
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
	if ($upgrade)
	{
		$result = $db->query("SHOW TABLES");

		echo "Upgrading all tables to MyISAM format:<ul>\n";
		while (list($table) = $db->fetch_row($result))
		{
			echo "<li>$table ... ";
			$db->query("ALTER TABLE $table TYPE=MYISAM");
			echo "OK\n";
		}
		echo "</ul>\n";

		echo "Done.<p>";
?>
Back to <a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=tools_optimize">Optimize Database</a><p>
<?php
	}
	else
	{
?>

Optimizing tables...<?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table><p>

<?php
		flush();

		$result = $db->query("SHOW TABLES");

		while (list($table) = $db->fetch_row($result))
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

		$result = $db->query("OPTIMIZE TABLE $dbtables");

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

		$result = $db->query("ANALYZE TABLE $dbtables");

		$tableAnalyze->draw($result, mysql_num_rows($result), 80);
?>
<p>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">

<tr>
	<td><?php echo $g_options["font_normal"]; ?><a href="<?php echo $g_options["scripturl"]; ?>?mode=admin&task=tools_optimize&upgrade=yes&<?php echo strip_tags(SID)?>">Click here</a> if you get "table handler does not support check/repair" above.<?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table>
<?php
	}
?>

