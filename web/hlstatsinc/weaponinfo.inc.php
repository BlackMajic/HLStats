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

$weapon = false;
if(!empty($_GET["weapon"])) {
	if(validateInput($_GET["weapon"],'nospace') === true) {
		$weapon = $_GET["weapon"];
	}
	else {
		error("No weapon specified.");
	}
}

$page = 1;
if (isset($_GET["page"])) {
	$check = validateInput($_GET['page'],'digit');
	if($check === true) {
		$page = $_GET['page'];
	}
}
$sort = 'frags';
if (isset($_GET["sort"])) {
	$check = validateInput($_GET['sort'],'nospace');
	if($check === true) {
		$sort = $_GET['sort'];
	}
}

$newSort = "ASC";
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


$query = mysql_query("SELECT name FROM ".DB_PREFIX."_Weapons
				WHERE code='".mysql_escape_string($weapon)."'
				AND game='".mysql_escape_string($game)."'");
if (mysql_num_rows($query) != 1) {
	$wep_name = ucfirst($weapon);
}
else {
	$result = mysql_fetch_assoc($query);
	$wep_name = $result["name"];
}
mysql_free_result($query);

// get the weapon info
$queryStr = "SELECT SQL_CALC_FOUND_ROWS
		".DB_PREFIX."_Events_Frags.killerId,
		".DB_PREFIX."_Players.lastName AS killerName,
		".DB_PREFIX."_Players.active,
		COUNT(".DB_PREFIX."_Events_Frags.weapon) AS frags
	FROM ".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players
		ON ".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE ".DB_PREFIX."_Events_Frags.weapon='".mysql_escape_string($weapon)."'
		AND ".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0
	GROUP BY ".DB_PREFIX."_Events_Frags.killerId
	ORDER BY ".$sort." ".$sortorder;

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
			$players['data'][] = $result;
		}
	}

	// get the max count for pagination
	$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
	$result = mysql_fetch_assoc($query);
	$players['pages'] = (int)ceil($result['rows']/50);
	mysql_freeresult($query);

$query = mysql_query($queryStr);

// get the total kills
$queryCount = mysql_query("
	SELECT
		COUNT(DISTINCT ".DB_PREFIX."_Events_Frags.killerId) AS wc,
		SUM(".DB_PREFIX."_Events_Frags.weapon='$weapon') AS tc
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Events_Frags.weapon='$weapon'
		AND ".DB_PREFIX."_Players.game='$game'
		AND ".DB_PREFIX."_Players.hideranking = 0
");
$result = mysql_fetch_assoc($queryCount);
$numitems = $result['wc'];
$totalkills = $result['tc'];
mysql_free_result($queryCount);

pageHeader(
	array($gamename, l("Weapon Details"), htmlspecialchars($wep_name)),
	array(
		$gamename => "index.php?game=$game",
		l("Weapon Statistics") => "index.php?mode=weapons&amp;game=$game",
		l("Weapon Details")=>""
	),
	$wep_name
);
?>

<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?game=$game&amp;mode=weapons"; ?>"><?php echo l('Back to Weapon Statistics'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l("Weapon Details"); ?> |
		<?php echo l("From a total of"); ?> <b><?php echo intval($totalkills); ?></b> <?php echo l('kills'); ?>
		(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)
	</h1>
	<img src="<?php echo $g_options["imgdir"]; ?>/weapons/<?php echo $game; ?>/<?php echo $weapon; ?>.png" alt="<?php echo $wep_name; ?>" title="<?php echo $wep_name; ?>"border="0" />
	<table cellpadding="0" cellspacing="0" border="1" width="100%">
		<tr>
			<th class="<?php echo $rcol; ?>"><?php echo l('Rank'); ?></th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'killerName','sortorder'=>$newSort)); ?>">
					<?php echo l('Player'); ?>
				</a>
				<?php if($sort == "killerName") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'frags','sortorder'=>$newSort)); ?>">
					<?php echo $weapon,' ' ,l('Kills'); ?>
				</a>
				<?php if($sort == "frags") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
		</tr>
	<?php
		if(!empty($players['data'])) {
			if($page > 1) {
				$rank = ($page - 1) * (50 + 1);
			}
			else {
				$rank = 1;
			}
			foreach($players['data'] as $k=>$entry) {
				toggleRowClass($rcol);

				echo '<tr>',"\n";

				echo '<td class="',$rcol,'">';
				echo $rank+$k;
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo '<a href="index.php?mode=playerinfo&amp;player=',$entry['killerId'],'">';
				echo '<img src="'.$g_options['imgdir'].'player.gif" width="16" height="16" /> ',$entry['killerName'];
				echo '</a>';
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['frags'];
				echo '</td>',"\n";

				echo '</tr>';
			}
			echo '<tr><td colspan="6" align="right">';
				if($players['pages'] > 1) {
					for($i=1;$i<=$players['pages'];$i++) {
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
		}
		else {
			echo '<tr><td colspan="6">',l('No data recorded'),'</td></tr>';
		}
	?>
	</table>
</div>
