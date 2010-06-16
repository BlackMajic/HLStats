<?php
/**
 * single action overview file
 * display the action listing sorted by action count for each player
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

// the players array which holds the data to display and the page count
$players['data'] = array();
$players['pages'] = array();


// the action identifier which is needed to load the data
$action = false;
if(!empty($_GET["action"])) {
	if(validateInput($_GET["action"],'nospace') === true) {
		$action = $_GET["action"];
	}
	else {
		die("No action specified.");
	}
}

// the current page to display
$page = 1;
if (isset($_GET["page"])) {
	$check = validateInput($_GET['page'],'digit');
	if($check === true) {
		$page = $_GET['page'];
	}
}

// the current element to sort by for the query
$sort = 'obj_count';
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

// query to get the full action name
$queryActionName = mysql_query("SELECT description FROM ".DB_PREFIX."_Actions
					WHERE code='".mysql_escape_string($action)."'
						AND game='".mysql_escape_string($game)."'");
if (mysql_num_rows($query) != 1) {
	$act_name = ucfirst($action);
}
else {
	$result = mysql_fetch_assoc($query);
	//the full action name
	$act_name = $result["description"];
}
mysql_free_result($query);

// query to get the total total action count
$queryCount = mysql_query("SELECT
		COUNT(".DB_PREFIX."_Events_PlayerActions.Id) AS tc
	FROM ".DB_PREFIX."_Events_PlayerActions, ".DB_PREFIX."_Players, ".DB_PREFIX."_Actions
	WHERE ".DB_PREFIX."_Actions.code = '".mysql_escape_string($action)."' AND
		".DB_PREFIX."_Players.game = '".mysql_escape_string($game)."' AND
		".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_PlayerActions.playerId AND
		".DB_PREFIX."_Events_PlayerActions.actionId = ".DB_PREFIX."_Actions.id");
$result = mysql_fetch_assoc($queryCount);
// the toral action count for this specific action
$totalact = $result['tc'];

if(!empty($totalact)) {
	// query to get the data from the db with the given options
	$queryStr = "SELECT SQL_CALC_FOUND_ROWS
			".DB_PREFIX."_Events_PlayerActions.playerId,
			".DB_PREFIX."_Players.lastName AS playerName,
			COUNT(".DB_PREFIX."_Events_PlayerActions.id) AS obj_count,
			COUNT(".DB_PREFIX."_Events_PlayerActions.id) * ".DB_PREFIX."_Actions.reward_player AS obj_bonus
		FROM ".DB_PREFIX."_Events_PlayerActions, ".DB_PREFIX."_Players, ".DB_PREFIX."_Actions
		WHERE ".DB_PREFIX."_Actions.code = '".mysql_escape_string($action)."' AND
			".DB_PREFIX."_Players.game = '".mysql_escape_string($game)."' AND
			".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Events_PlayerActions.playerId AND
			".DB_PREFIX."_Events_PlayerActions.actionId = ".DB_PREFIX."_Actions.id AND
			".DB_PREFIX."_Players.hideranking<>'1'
		GROUP BY ".DB_PREFIX."_Events_PlayerActions.playerId
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

	// query to get the total rows which would be fetched without the LIMIT
	$query = mysql_query("SELECT FOUND_ROWS() AS 'rows'");
	$result = mysql_fetch_assoc($query);
	$players['pages'] = (int)ceil($result['rows']/50);
	mysql_freeresult($query);

}

pageHeader(
	array($gamename, l("Action Details"), $act_name),
	array(
		$gamename => "index.php?game=$game",
		l("Action Statistics") => "index.php?mode=actions&amp;game=$game",
		l("Action Details")=>""
	),
	$act_name
);

?>
<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?mode=actions&amp;game=$game"; ?>"><?php echo l('Back to Action Statistics'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1>
		<?php echo l('From a total of'); ?> <b><?php echo intval($totalact); ?></b> <?php echo l('achievements'); ?> (<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?>)
	</h1>
	<table cellpadding="0" cellspacing="0" border="1" width="100%">
		<tr>
			<th class="<?php echo $rcol; ?>"><?php echo l('Rank'); ?></th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'playerId','sortorder'=>$newSort)); ?>">
					<?php echo l('Player'); ?>
				</a>
				<?php if($sort == "playerId") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'obj_count','sortorder'=>$newSort)); ?>">
					<?php echo l('Achieved'); ?>
				</a>
				<?php if($sort == "obj_count") { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/<?php echo $sortorder; ?>.gif" alt="Sorting" width="7" height="7" />
				<?php } ?>
			</th>
			<th class="<?php echo $rcol; ?>">
				<a href="index.php?<?php echo makeQueryString(array('sort'=>'obj_bonus','sortorder'=>$newSort)); ?>">
					<?php echo l('Skill Bonus Total'); ?>
				</a>
				<?php if($sort == "obj_bonus") { ?>
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
				echo '<a href="index.php?mode=playerinfo&amp;player=',$entry['playerId'],'">';
				echo '<img src="hlstatsimg/player.gif" width="16" height="16" /> ',makeSavePlayerName($entry['playerName']);
				echo '</a>';
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['obj_count'];
				echo '</td>',"\n";

				echo '<td class="',$rcol,'">';
				echo $entry['obj_bonus'];
				echo '</td>',"\n";

				echo '</tr>';
			}
			echo '<tr><td colspan="4" align="right">';
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
			echo '<tr><td colspan="4">',l('No data recorded'),'</td></tr>';
		}
	?>
	</table>
</div>
