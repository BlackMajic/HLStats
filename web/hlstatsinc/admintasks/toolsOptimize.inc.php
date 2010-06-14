<?php
/**
 * optimze HLStats database tables
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

// the initial row color
$rcol = "row-dark";

$optimize = false;
$analyze = false;

$upgrade = false;
if(!empty($_GET['upgrade'])) {
	if($_GET['upgrade'] == "yes") {
		$upgrade = true;
	}
}

pageHeader(array(l("Admin"),l('Admin Optimize Database')), array(l("Admin")=>"index.php?mode=admin",l('Admin Optimize Database')=>''));
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
	<h1><?php echo l('Admin Optimize Database'); ?></h1>
	<a href="index.php?mode=admin&amp;task=toolsOptimize&amp;upgrade=yes"><?php echo l('Click here'); ?></a> <?php echo l('if you get "table handler does not support check/repair"'); ?></a>
	<?php
		if ($upgrade === true) {
			$result = mysql_query("SHOW TABLES");

			echo "Upgrading all tables to MyISAM format:<ul>\n";
			flush();
			while (list($table) = mysql_fetch_array($result)) {
				echo "<li>$table ... ";
				mysql_query("ALTER TABLE $table TYPE=MYISAM");
				echo "OK\n";
				flush();
			}
			echo "</ul>\n";

			echo "Done.<p>";
		} else {


			$result = mysql_query("SHOW TABLES");
			$dbtables = '';

			while (list($table) = mysql_fetch_array($result)) {
				if ($dbtables) $dbtables .= ", ";
				$dbtables .= $table;
			}

			$query = mysql_query("OPTIMIZE TABLE $dbtables");
			if(mysql_num_rows($query) > 0) {
				while($result = mysql_fetch_assoc($query)) {
					$optimize[] = $result;
				}
			}
			unset($result);
			mysql_free_result($query);

			$query = mysql_query("OPTIMIZE TABLE $dbtables");
			if(mysql_num_rows($query) > 0) {
				while($result = mysql_fetch_assoc($query)) {
					$analyze[] = $result;
				}
			}
			unset($result);
			mysql_free_result($query);
	?>
	<h2><?php echo l('Optimizing tables...'); ?></h2>
	<table cellpadding="0" cellspacing="0" border="1" width="100%">
		<tr>
			<th class="<?php echo $rcol; ?>">
				<?php echo l('Table'); ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<?php echo l('Operation'); ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<?php echo l('Msg. Type'); ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<?php echo l('Message'); ?>
			</th>
		</tr>
		<?php
		if(!empty($optimize)) {
			foreach($optimize as $k=>$entry) {
				toggleRowClass($rcol);

				echo '<tr>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['Table'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['Op'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['Msg_type'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['Msg_text'];
				echo '</td>',"\n";

				echo '</tr>';
			}
		}
		else {
			echo '<tr><td colspan="5">',l('No data available'),'</td></tr>';
		}
		?>
	</table>
	<h2><?php echo l('Analyzing tables...'); ?></h2>
	<table cellpadding="0" cellspacing="0" border="1" width="100%">
		<tr>
			<th class="<?php echo $rcol; ?>">
				<?php echo l('Table'); ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<?php echo l('Operation'); ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<?php echo l('Msg. Type'); ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<?php echo l('Message'); ?>
			</th>
		</tr>
		<?php
		if(!empty($analyze)) {
			foreach($analyze as $k=>$entry) {
				toggleRowClass($rcol);

				echo '<tr>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['Table'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['Op'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['Msg_type'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['Msg_text'];
				echo '</td>',"\n";

				echo '</tr>';
			}
		}
		else {
			echo '<tr><td colspan="5">',l('No data available'),'</td></tr>';
		}
		?>
	</table>
	<?php } ?>
</div>
