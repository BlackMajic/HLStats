<?php
/**
 * maps overview file
 * display the overall maps stats for this game
 * @package HLStats
 * @author Johannes 'Banana' Keßler
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

/**
 * the initial row color
 * @global string $rcol
 * @name $rcol
 */
$rcol = "row-dark";

/**
 * the maps array which holds the data to display and the page count
 * @global array $maps
 * @name $maps
 */
$maps['data'] = array();
$maps['pages'] = array();

/**
 * the current page to display
 * @global int $page
 * @name $page
 */
$page = 1;
if (isset($_GET["page"])) {
	$check = validateInput($_GET['page'],'digit');
	if($check === true) {
		$page = $_GET['page'];
	}
}

/**
 * the current element to sort by for the query
 * @global string $sort
 * @name $sort
 */
$sort = 'kills';
if (isset($_GET["sort"])) {
	$check = validateInput($_GET['sort'],'nospace');
	if($check === true) {
		$sort = $_GET['sort'];
	}
}

/**
 * the default next sort order
 * @global string $newSort
 * @name $newSort
 */
$newSort = "ASC";
/**
 * the default sort order for the query
 * @global string $sortorder
 * @name $sortorder
 */
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

/**
 * query to get the total kills count for this game
 * @global string $queryKillsCount
 * @name $queryKillsCount
 */
$queryKillsCount = mysql_query("SELECT COUNT(*) as kc
	FROM ".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players
		ON ".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE ".DB_PREFIX."_Players.game = '".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0");
$result = mysql_fetch_assoc($queryKillsCount);
/**
 * get the total kill count for this game
 * @global int $totalkills
 * @name $totalkills
 */
$totalkills = $result['kc'];
mysql_free_result($queryKillsCount);

if(!empty($totalkills)) {
	/**
	 * query to get the data from the db with the given options
	 * @global string $queryStr
	 * @name $queryStr
	 */
	$queryStr = "SELECT SQL_CALC_FOUND_ROWS
		IF(".DB_PREFIX."_Events_Frags.map='', '(Unaccounted)', ".DB_PREFIX."_Events_Frags.map) AS map,
		COUNT(".DB_PREFIX."_Events_Frags.map) AS kills
	FROM ".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE ".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0
	GROUP BY ".DB_PREFIX."_Events_Frags.map
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
			$result['percent'] = $result['kills']/$totalkills*100;
			$maps['data'][] = $result;
		}
	}

	/**
	 * query to get the total rows which would be fetched without the LIMIT
	 * works only if the $queryStr has SQL_CALC_FOUND_ROWS
	 * @global string $query
	 * @name $query
	 */
	$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
	$result = mysql_fetch_assoc($query);
	$maps['pages'] = (int)ceil($result['rows']/50);
	mysql_freeresult($query);

}

pageHeader(
	array($gamename, l("Map Statistics")),
	array($gamename=>"%s?game=$game", l("Map Statistics")=>"")
);
?>
<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?game=$game"; ?>"><?php echo l('Back to game overview'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1>
		<?php echo l("Map Statistics"); ?> |
		<?php echo l('From a total of'); ?> <b><?php echo $totalkills; ?></b> <?php echo l('kills'); ?> (<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?>)
	</h1>
</div>

<?php
$tblMaps = new Table(
	array(
		new TableColumn(
			"map",
			"Map Name",
			"width=25&align=center&link=" . urlencode("mode=mapinfo&amp;map=%k&amp;game=$game")
		),
		new TableColumn(
			"kills",
			"Kills",
			"width=10&align=right"
		),
		new TableColumn(
			"percent",
			"Percentage of Kills",
			"width=50&sort=no&type=bargraph"
		),
		new TableColumn(
			"percent",
			"%",
			"width=10&sort=no&align=right&append=" . urlencode("%")
		)
	),
	"map",
	"kills",
	"map",
	true,
	9999,
	"maps_page",
	"maps_sort",
	"maps_sortorder",
	"maps"
);

$queryKillsCount = mysql_query("SELECT COUNT(*) as kc
	FROM ".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Players.game = '".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0");
$result = mysql_fetch_assoc($queryKillsCount);
$totalkills = $result['kc'];
mysql_free_result($queryKillsCount);

$result = mysql_query("
	SELECT
		IF(".DB_PREFIX."_Events_Frags.map='', '(Unaccounted)', ".DB_PREFIX."_Events_Frags.map) AS map,
		COUNT(".DB_PREFIX."_Events_Frags.map) AS kills,
		COUNT(".DB_PREFIX."_Events_Frags.map) / ".mysql_escape_string($totalkills)." * 100 AS percent
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0
	GROUP BY
		".DB_PREFIX."_Events_Frags.map
	ORDER BY
		".$tblMaps->sort." ".$tblMaps->sortorder.",
		".$tblMaps->sort2." ".$tblMaps->sortorder."");
?>
<p>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%"><?php echo $g_options["font_normal"]; ?><?php echo l('From a total of'); ?> <b><?php echo $totalkills; ?></b> <?php echo l('kills'); ?> (<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?>)<?php echo $g_options["fontend_normal"]; ?></td>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?><?php echo l('Back to'); ?> <a href="<?php echo "index.php?game=$game"; ?>"><?php echo $gamename; ?></a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table>
</p>
<?php $tblMaps->draw($result, mysql_num_rows($result), 90);
?>
