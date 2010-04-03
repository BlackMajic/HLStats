<?php
/**
 * clans overview file
 * display conplete clan overview
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
 * the actions array which holds the data to display and the page count
 * @global array $actions
 * @name $actions
 */
$clans['data'] = array();
$clans['pages'] = array();

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
$sort = 'obj_count';
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
 * minimum mebers count to show
 * @gloabl int $minmembers
 * @name $minmembers
 */
$minmembers = 2;
if (isset($_GET["minmembers"])) {
	$check = validateInput($_GET['minmembers'],'digit');
	if($check === true)
		$minmembers = $_GET["minmembers"];
}

/**
 * query to get the data from the db with the given options
 * @global string $queryStr
 * @name $queryStr
 */
$queryStr = "SELECT SQL_CALC_FOUND_ROWS
		".DB_PREFIX."_Clans.clanId,
		".DB_PREFIX."_Clans.name,
		".DB_PREFIX."_Clans.tag,
		COUNT(".DB_PREFIX."_Players.playerId) AS nummembers,
		SUM(".DB_PREFIX."_Players.kills) AS kills,
		SUM(".DB_PREFIX."_Players.deaths) AS deaths,
		ROUND(AVG(".DB_PREFIX."_Players.skill)) AS skill,
		IFNULL(SUM(".DB_PREFIX."_Players.kills)/SUM(".DB_PREFIX."_Players.deaths), '-') AS kpd
	FROM ".DB_PREFIX."_Clans
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.clan=".DB_PREFIX."_Clans.clanId
	WHERE ".DB_PREFIX."_Clans.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0
	GROUP BY ".DB_PREFIX."_Clans.clanId
	HAVING nummembers >= ".mysql_escape_string($minmembers)."
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
		$clans['data'][] = $result;
	}
}
mysql_freeresult($query);

/**
 * query to get the total rows which would be fetched without the LIMIT
 * works only if the $queryStr has SQL_CALC_FOUND_ROWS
 * @global string $query
 * @name $query
 */
$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
$result = mysql_fetch_assoc($query);
$actions['pages'] = (int)ceil($result['rows']/50);
mysql_freeresult($query);

pageHeader(
	array($gamename, l("Clan Rankings")),
	array($gamename=>"%s?game=$game", l("Clan Rankings")=>"")
);
?>

<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?mode=players&amp;game=$game"; ?>"><?php echo l('Back to game overview'); ?></a>
			</li>
		</ul>
		<form method="GET" action="index.php">
			<input type="hidden" name="mode" value="search">
			<input type="hidden" name="game" value="<?php echo $game; ?>">
			<input type="hidden" name="st" value="clan">
			<?php echo l('Find a clan'); ?>:
			<input type="text" name="q" size=20 maxlength=64 class="textbox">
			<input type="submit" value="<?php echo l('Search'); ?>" class="smallsubmit">
		</form>
	</div>
</div>
<div id="main">
	<h1>
		<?php echo l("Clan Rankings"); ?>
	</h1>
	<table cellpadding="0" cellspacing="0" border="1" width="100%">
		<tr>
			<th class="<?php echo $rcol; ?>"><?php echo l('Rank'); ?></th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'code','sortorder'=>$newSort)); ?>">
					<?php echo l('Action'); ?>
				</a>
				<?php if($sort == "code") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
		</tr>
	</table>
</div>

<p>
<form method="GET" action="index.php">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="game" value="<?php echo $game; ?>">
	<input type="hidden" name="st" value="clan">

	<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">
		<tr valign="bottom">
			<td width="75%"><?php echo $g_options["font_normal"]; ?><b>&#149;</b> <?php echo l('Find a clan'); ?>: <input type="text" name="q" size=20 maxlength=64 class="textbox"> <input type="submit" value="<?php echo l('Search'); ?>" class="smallsubmit"><?php echo $g_options["fontend_normal"]; ?></td>
			<td width="25%" align="right" nowrap><?php echo $g_options["font_normal"]; ?><?php echo l('Go to'); ?> <a href="<?php echo "index.php?mode=players&amp;game=$game"; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/player.gif" width="16" height="16" hspace="3" border="0" align="middle" alt="player.gif"><?php echo l('Player Rankings'); ?></a><?php echo $g_options["fontend_normal"]; ?></td>
		</tr>
	</table>
</form>
</p>
<?php
	$table = new Table(
		array(
			new TableColumn(
				"name",
				"Name",
				"width=30&icon=clan&link=" . urlencode("mode=claninfo&amp;clan=%k")
			),
			new TableColumn(
				"tag",
				"Tag",
				"width=15"
			),
			new TableColumn(
				"skill",
				"Points",
				"width=10&align=right"
			),
			new TableColumn(
				"nummembers",
				"Members",
				"width=10&align=right"
			),
			new TableColumn(
				"kills",
				"Kills",
				"width=10&align=right"
			),
			new TableColumn(
				"deaths",
				"Deaths",
				"width=10&align=right"
			),
			new TableColumn(
				"kpd",
				"Kills per Death",
				"width=10&align=right"
			)
		),
		"clanId",
		"skill",
		"kpd",
		true
	);

	$queryClans = mysql_query("
		SELECT
			".DB_PREFIX."_Clans.clanId,
			".DB_PREFIX."_Clans.name,
			".DB_PREFIX."_Clans.tag,
			COUNT(".DB_PREFIX."_Players.playerId) AS nummembers,
			SUM(".DB_PREFIX."_Players.kills) AS kills,
			SUM(".DB_PREFIX."_Players.deaths) AS deaths,
			ROUND(AVG(".DB_PREFIX."_Players.skill)) AS skill,
			IFNULL(SUM(".DB_PREFIX."_Players.kills)/SUM(".DB_PREFIX."_Players.deaths), '-') AS kpd
		FROM
			".DB_PREFIX."_Clans
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.clan=".DB_PREFIX."_Clans.clanId
		WHERE
			".DB_PREFIX."_Clans.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Players.hideranking = 0
		GROUP BY
			".DB_PREFIX."_Clans.clanId
		HAVING
			nummembers >= ".mysql_escape_string($minmembers)."
		ORDER BY
			".$table->sort." ".$table->sortorder.",
			".$table->sort2." ".$table->sortorder.",
			name ASC
		LIMIT ".$table->startitem.",".$table->numperpage."
	");

	$resultquery = mysql_query("
		SELECT
			COUNT(*) AS cc
		FROM
			".DB_PREFIX."_Clans
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.clan=".DB_PREFIX."_Clans.clanId
		WHERE
			".DB_PREFIX."_Clans.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Players.hideranking = 0
		GROUP BY
			".DB_PREFIX."_Clans.clanId
		HAVING
			COUNT(".DB_PREFIX."_Players.playerId) >= ".mysql_escape_string($minmembers)."");
	$result = mysql_fetch_assoc($resultquery);
	$resultCount = $result['cc'];

	$table->draw($queryClans, $resultCount, 90);
?>
<p>
<form method="GET" action="index.php">
<input type="hidden" name="game" value="<?php echo $game; ?>" />
<input type="hidden" name="mode" value="clans" />
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">
<tr valign="bottom">
	<td width="75%"><?php echo $g_options["font_normal"]; ?>
		<b>&#149;</b> <?php echo l('Only show clans with'); ?> <input type="text" name="minmembers" size=4 maxlength=2 value="<?php echo $minmembers; ?>" class="textbox">
		<?php echo l('or more members'); ?>. <input type="submit" value="<?php echo l('Apply'); ?>" class="smallsubmit"><?php echo $g_options["fontend_normal"]; ?>
	</td>
</tr>
</table>
</form>
</p>
