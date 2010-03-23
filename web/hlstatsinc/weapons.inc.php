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

$rcol = "row-dark";
$weapons['data'] = array();
$weapons['pages'] = array();

$page = 1;
if (isset($_GET["page"])) {
	$check = validateInput($_GET['page'],'digit');
	if($check === true) {
		$page = $_GET['page'];
	}
}
$sort = 'kills';
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

// get the data
// @todo to improve the spead of this query..
$killCount = mysql_query("SELECT COUNT(".DB_PREFIX."_Players.playerId) kc
	FROM ".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE ".DB_PREFIX."_Players.game = '".mysql_escape_string($game)."'");
$result = mysql_fetch_assoc($killCount);
$totalkills = $result['kc'];
mysql_free_result($killCount);

if(!empty($totalkills)) {
	// @todo to improve this query for speed
	$queryStr = "SELECT SQL_CALC_FOUND_ROWS
			".DB_PREFIX."_Events_Frags.weapon,
			".DB_PREFIX."_Weapons.modifier AS modifier,
			".DB_PREFIX."_Weapons.name,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) AS kills
		FROM ".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Weapons
			ON ".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Frags.weapon
		LEFT JOIN ".DB_PREFIX."_Players
			ON ".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
		WHERE ".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Players.hideranking = 0
		GROUP BY ".DB_PREFIX."_Events_Frags.weapon
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

			$weapons['data'][] = $result;
		}
	}

	// get the max count for pagination
	$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
	$result = mysql_fetch_assoc($query);
	$weapons['pages'] = (int)ceil($result['rows']/50);
	mysql_freeresult($query);
}

pageHeader(
	array($gamename, l("Weapon Statistics")),
	array($gamename=>"%s?game=$game", l("Weapon Statistics")=>"")
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
	<h1><?php echo l("Weapon Statistics"); ?></h1>
	<table cellpadding="0" cellspacing="0" border="1" width="100%">
		<tr>
			<th class="<?php echo $rcol; ?>"><?php echo l('Rank'); ?></th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'weapon','sortorder'=>$newSort)); ?>">
					<?php echo l('Weapon'); ?>
				</a>
				<?php if($sort == "weapon") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'name','sortorder'=>$newSort)); ?>">
					<?php echo l('Name'); ?>
				</a>
				<?php if($sort == "name") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'modifier','sortorder'=>$newSort)); ?>">
					<?php echo l('Points Modifier'); ?>
				</a>
				<?php if($sort == "modifier") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'kills','sortorder'=>$newSort)); ?>">
					<?php echo l('Kills'); ?>
				</a>
				<?php if($sort == "kills") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>"><?php echo l('Percent'); ?></th>
		</tr>
		<?php
		if(!empty($weapons['data'])) {
			if($page > 1) {
				$rank = ($page - 1) * (50 + 1);
			}
			else {
				$rank = 1;
			}
			foreach($weapons['data'] as $k=>$entry) {
				toggleRowClass($rcol);

				echo '<tr>',"\n";

				echo '<td class="',$rcol,'">';
				echo $rank+$k;
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo '<img src="'.$g_options['imgdir'].'weapons/',$game,'/',$entry['weapon'],'.png" alt="',$entry['name'],'" title="',$entry['name'],'" />';
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['name'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['modifier'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['kills'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo '<div class="percentBar"><div class="barContent" style="width:',number_format($entry['percent'],0),'px"></div></div>',"\n";
				echo '</td>',"\n";
			}
		}
		else {
			echo '<tr><td colspan="4">',l('No data recorded'),'</td></tr>';
		}
	?>
	</table>
</div>
<?php
/*
$tblWeapons = new Table(
	array(
		new TableColumn(
			"weapon",
			"Weapon",
			"width=21&type=weaponimg&align=center&link=" . urlencode("mode=weaponinfo&amp;weapon=%k&amp;game=$game")
		),
		new TableColumn(
			"modifier",
			"Points Modifier",
			"width=10&align=right"
		),
		new TableColumn(
			"kills",
			"Kills",
			"width=12&align=right"
		),
		new TableColumn(
			"percent",
			"Percentage of Kills",
			"width=40&sort=no&type=bargraph"
		),
		new TableColumn(
			"percent",
			"%",
			"width=12&sort=no&align=right&append=" . urlencode("%")
		)
	),
	"weapon",
	"kills",
	"weapon",
	true,
	9999,
	"weap_page",
	"weap_sort",
	"weap_sortorder"
);
	 */
?>
