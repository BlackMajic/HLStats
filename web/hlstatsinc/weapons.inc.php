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
		$playersObj->setOption("sort",$_GET['sort']);
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
$killCount = mysql_query("
	SELECT
		COUNT(".DB_PREFIX."_Players.playerId) kc
	FROM
		".DB_PREFIX."_Events_Frags
	LEFT JOIN ".DB_PREFIX."_Players ON
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
	WHERE
		".DB_PREFIX."_Players.game = '".mysql_escape_string($game)."'");
$result = mysql_fetch_assoc($killCount);
$totalkills = $result['kc'];
mysql_free_result($killCount);

if(!empty($totalkills)) {
	$queryStr = "
		SELECT SQL_CALC_FOUND_ROWS
			".DB_PREFIX."_Events_Frags.weapon,
			".DB_PREFIX."_Weapons.modifier AS modifier,
			".DB_PREFIX."_Weapons.name,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) AS kills,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) / ".mysql_escape_string($totalkills)." * 100 AS percent
		FROM
			".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Weapons ON
			".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Frags.weapon
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_Frags.killerId
		WHERE
			".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Weapons.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Players.hideranking = 0
		GROUP BY
			".DB_PREFIX."_Events_Frags.weapon
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
			var_dump($result);
			$result['percent'] = number_format($result['kpd'],1,'.','');
			$weapons[] = $result;
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
			<th class="<?php echo toggleRowClass($rcol); ?>"><?php echo l('Rank'); ?></th>
			<th class="<?php echo toggleRowClass($rcol); ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'weapon','sortorder'=>$newSort)); ?>">
					<?php echo l('Weapon'); ?>
				</a>
				<?php if($sort == "weapon") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo toggleRowClass($rcol); ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'modifier','sortorder'=>$newSort)); ?>">
					<?php echo l('Points Modifier'); ?>
				</a>
				<?php if($sort == "modifier") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
		</tr>
	</table>
	<?php
		if(!empty($weapons['data'])) {
		}
		else {
			echo l('No data recorded');
		}
	?>
</div>
<?php
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
?>



<p>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%"><?php echo $g_options["font_normal"]; ?><?php echo l('From a total of'); ?> <b><?php echo $totalkills; ?></b> <?php echo l('kills'); ?> (<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?>)<?php echo $g_options["fontend_normal"]; ?></td>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?><?php echo l('Back to'); ?> <a href="<?php echo "index.php?game=$game"; ?>"><?php echo $gamename; ?></a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table>
</p>
<?php $tblWeapons->draw($result, mysql_num_rows($result), 90); ?>
