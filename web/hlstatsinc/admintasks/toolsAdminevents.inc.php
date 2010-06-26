<?php
/**
 * view any admin events recorded by HLStats
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

// the actions array which holds the data to display and the page count
$adminEvents['data'] = array();
$adminEvents['pages'] = array();

// the current page to display
$page = 1;
if (isset($_GET["page"])) {
	$check = validateInput($_GET['page'],'digit');
	if($check === true) {
		$page = $_GET['page'];
	}
}

// the current element to sort by for the query
$sort = 'eventTime';
if (isset($_GET["sort"])) {
	$check = validateInput($_GET['sort'],'nospace');
	if($check === true) {
		$sort = $_GET['sort'];
	}
}

// the default next sort order
$newSort = "ASC";
// the default sort order for the query
$sortorder = 'DESC';
if (isset($_GET["sortorder"])) {
	$check = validateInput($_GET['sortorder'],'nospace');
	if($check === true) {
		$sortorder = $_GET['sortorder'];
	}

	if($_GET["sortorder"] == "ASC") {
		$newSort = "DESC";
	}
}

// get the admin event history
$queryStr = "SELECT SQL_CALC_FOUND_ROWS
			CONCAT(er.type, ' Rcon') AS `type`,
			er.eventTime,
			CONCAT('\"', command, '\"\nFrom: ', remoteIp, ', password: \"', password, '\"') AS `desc`,
			".DB_PREFIX."_Servers.name,
			er.map
		FROM `".DB_PREFIX."_Events_Rcon` AS er
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId = er.serverId";
$queryStr .= " UNION ALL
			SELECT ea.type AS `type`,
			ea.eventTime,
			IF(playerName != '',
				CONCAT('\"', playerName, '\": ', message),
				message
			) AS `desc`,
			".DB_PREFIX."_Servers.name,
			ea.map
		FROM `".DB_PREFIX."_Events_Admin` AS ea
		LEFT JOIN ".DB_PREFIX."_Servers ON ".DB_PREFIX."_Servers.serverId = ea.serverId";

$queryStr .= " ORDER BY ".$sort." ".$sortorder;

// calculate the limit
if($page === 1) {
	$queryStr .=" LIMIT 0,50";
}
else {
	$start = 50*($page-1);
	$queryStr .=" LIMIT ".$start.",50";
}

$query = mysql_query($queryStr);
if(mysql_num_rows($query) > 0) {
	while($result = mysql_fetch_assoc($query)) {
		$adminEvents['data'][] = $result;
	}
}
mysql_freeresult($query);

// query to get the total rows which would be fetched without the LIMIT
// works only if the $queryStr has SQL_CALC_FOUND_ROWS
$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
$result = mysql_fetch_assoc($query);
$adminEvents['pages'] = (int)ceil($result['rows']/50);
mysql_freeresult($query);

pageHeader(array(l("Admin"),l('Event History')), array(l("Admin")=>"index.php?mode=admin",l('Event History')=>''));
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
	<h1><?php echo l('Event History'); ?></h1>
	<table cellpadding="0" cellspacing="0" border="1" width="100%">
		<tr>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'eventTime','sortorder'=>$newSort)); ?>">
					<?php echo l('Date'); ?>
				</a>
				<?php if($sort == "eventTime") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'type','sortorder'=>$newSort)); ?>">
					<?php echo l('Type'); ?>
				</a>
				<?php if($sort == "type") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>"><?php echo l('Description'); ?></th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'name','sortorder'=>$newSort)); ?>">
					<?php echo l('Server'); ?>
				</a>
				<?php if($sort == "name") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'map','sortorder'=>$newSort)); ?>">
					<?php echo l('Map'); ?>
				</a>
				<?php if($sort == "map") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
		</tr>
	<?php
		if(!empty($adminEvents['data'])) {
			foreach($adminEvents['data'] as $k=>$entry) {
				toggleRowClass($rcol);

				echo '<tr>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['eventTime'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['type'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['desc'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['name'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['map'];
				echo '</td>',"\n";

				echo '</tr>';
			}
			echo '<tr><td colspan="5" align="right">';
			if($adminEvents['pages'] > 1) {
				for($i=1;$i<=$adminEvents['pages'];$i++) {
					if($page == ($i)) {
						echo "[",$i,"]";
					}
					else {
						echo "<a href='index.php?",makeQueryString(array('page'=>$i)),"'>[",$i,"]</a>";
					}
				}
			}
			else {
				echo "[1]";
			}
			echo '</td></tr>';
		}
		else {
			echo '<tr><td colspan="5">',l('No data recorded'),'</td></tr>';
		}
	?>
	</table>
</div>
