<?php
/**
 * clans overview file
 * display complete clans overview
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
$clans['data'] = array();
$clans['pages'] = array();

// the current page to display
$page = 1;
if (isset($_GET["page"])) {
	$check = validateInput($_GET['page'],'digit');
	if($check === true) {
		$page = $_GET['page'];
	}
}

// the current element to sort by for the query
$sort = 'skill';
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

// minimum mebers count to show
$minmembers = 2;
if (isset($_GET["minmembers"])) {
	$check = validateInput($_GET['minmembers'],'digit');
	if($check === true)
		$minmembers = $_GET["minmembers"];
}

// query to get the data from the db with the given options
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
	LEFT JOIN ".DB_PREFIX."_Players
		ON ".DB_PREFIX."_Players.clan=".DB_PREFIX."_Clans.clanId
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

// query to get the total rows which would be fetched without the LIMIT
// works only if the $queryStr has SQL_CALC_FOUND_ROWS
$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
$result = mysql_fetch_assoc($query);
$clans['pages'] = (int)ceil($result['rows']/50);
mysql_freeresult($query);

pageHeader(
	array($gamename, l("Clan Rankings")),
	array($gamename=>"index.php?game=$game", l("Clan Rankings")=>"")
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
		<?php
		echo '<tr><td colspan="8" align="right">';
			if($clans['pages'] > 1) {
				for($i=1;$i<=$clans['pages'];$i++) {
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
		?>
		<tr>
			<th class="<?php echo $rcol; ?>"><?php echo l('Rank'); ?></th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'name','sortorder'=>$newSort)); ?>">
					<?php echo l('Name'); ?>
				</a>
				<?php if($sort == "name") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'tag','sortorder'=>$newSort)); ?>">
					<?php echo l('Tag'); ?>
				</a>
				<?php if($sort == "tag") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'skill','sortorder'=>$newSort)); ?>">
					<?php echo l('Points'); ?>
				</a>
				<?php if($sort == "skill") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'nummembers','sortorder'=>$newSort)); ?>">
					<?php echo l('Members'); ?>
				</a>
				<?php if($sort == "nummembers") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'kills','sortorder'=>$newSort)); ?>">
					<?php echo l('Kills'); ?>
				</a>
				<?php if($sort == "kills") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'deaths','sortorder'=>$newSort)); ?>">
					<?php echo l('Deaths'); ?>
				</a>
				<?php if($sort == "deaths") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'kpd','sortorder'=>$newSort)); ?>">
					<?php echo l('Kills per Death'); ?>
				</a>
				<?php if($sort == "kpd") { ?>
				<img src="hlstatsimg/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
		</tr>
		<?php
		if(!empty($clans['data'])) {
			if($page > 1) {
				$rank = ($page - 1) * (50 + 1);
			}
			else {
				$rank = 1;
			}
			foreach($clans['data'] as $k=>$entry) {
				toggleRowClass($rcol);

				echo '<tr>',"\n";

				echo '<td class="',$rcol,'">';
				echo $rank+$k;
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo '<a href="index.php?mode=claninfo&amp;clan=',$entry['clanId'],'&amp;game=',$game,'">';
				echo $entry['name'];
				echo '</a>';
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['tag'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['skill'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['nummembers'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['kills'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['deaths'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo number_format((int)$entry['kpd'],1);
				echo '</td>',"\n";

				echo '</tr>';
			}
			echo '<tr><td colspan="8" align="right">';
			if($clans['pages'] > 1) {
				for($i=1;$i<=$clans['pages'];$i++) {
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
			echo '<tr><td colspan="8">',l('No data recorded'),'</td></tr>';
		}
	?>
	</table>
</div>
