<?php
/**
 * search page
 * search for a player, clans in one or more games
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

require("hlstatsinc/search-class.inc.php");

pageHeader(
	array(l("Search")),
	array(l("Search")=>"")
);

$sr_query = '';
$sr_type = 'player';
$sr_game = '';

if(!empty($_GET["q"])) {
	$sr_query = sanitize($_GET["q"]);
}

if(!empty($_GET["q"])) {
	if(validateInput($_GET["st"],'nospace') === true) {
		$sr_type = $_GET["st"];
	}
}

if(!empty($_GET["game"])) {
	if(validateInput($_GET["game"],'nospace') === true) {
		$sr_game = $_GET["game"];
	}
}

// get the game list
$gamesArr = array();
$query = mysql_query("SELECT code, name FROM ".DB_PREFIX."_Games WHERE hidden='0' ORDER BY name");
while ($result = mysql_fetch_assoc($query)) {
	$gamesArr[$result['code']] = $result['name'];
}

$searchResults = array();

if(isset($_POST['submit']['search'])) {
	$term = trim($_POST['search']['input']);

	if(!empty($term)) {
		$andgame = "";
		if ($_POST['search']['game'] !== "---") {
			$andgame = "AND ".DB_PREFIX."_Players.game = '".mysql_escape_string($_POST['search']['game'])."'";
		}

		switch($_POST['search']['area']) {
			case 'clan':
			break;

			case 'player':
			default:
				$queryStr = "SELECT ".DB_PREFIX."_PlayerNames.playerId,
						".DB_PREFIX."_PlayerNames.name,
						".DB_PREFIX."_Games.name AS gamename
					FROM ".DB_PREFIX."_PlayerNames
					LEFT JOIN ".DB_PREFIX."_Players ON
						".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerNames.playerId
					LEFT JOIN ".DB_PREFIX."_Games ON
						".DB_PREFIX."_Games.code = ".DB_PREFIX."_Players.game
					WHERE ".DB_PREFIX."_Games.hidden='0' AND
						".DB_PREFIX."_PlayerNames.name LIKE '%".mysql_escape_string($term)."%'
						".$andgame."
					ORDER BY `name`";
				$query = mysql_query($queryStr);


				if(mysql_num_rows($query) > 0) {
					while($result = mysql_fetch_assoc($query)) {
						$searchResults[$result['gamename']][] = $result;
					}
				}
			break;
		}
	}

	/*
	if ($this->type == "player") {

		$table = new Table(
			array(
				new TableColumn(
					"playerId",
					"ID",
					"width=5align=right"
				),
				new TableColumn(
					"name",
					"Name",
					"width=65&icon=player&link=" . urlencode($link_player)
				),
				new TableColumn(
					"gamename",
					"Game",
					"width=30"
				)
			),
			"playerId",
			"name",
			"playerId",
			false,
			50,
			"page",
			"sort",
			"sortorder",
			"results",
			"asc"
		);

		if ($this->game)
			$andgame = "AND ".DB_PREFIX."_Players.game='" . $this->game . "'";
		else
			$andgame = "";

		$query = mysql_query("
			SELECT
				".DB_PREFIX."_PlayerNames.playerId,
				".DB_PREFIX."_PlayerNames.name,
				".DB_PREFIX."_Games.name AS gamename
			FROM
				".DB_PREFIX."_PlayerNames
			LEFT JOIN ".DB_PREFIX."_Players ON
				".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerNames.playerId
			LEFT JOIN ".DB_PREFIX."_Games ON
				".DB_PREFIX."_Games.code = ".DB_PREFIX."_Players.game
			WHERE
				".DB_PREFIX."_Games.hidden='0' AND
				".DB_PREFIX."_PlayerNames.name LIKE '%".mysql_escape_string($sr_query)."%'
				".$andgame."
			ORDER BY
				".$table->sort." ".$table->sortorder.",
				".$table->sort2." ".$table->sortorder."
			LIMIT ".$table->startitem.",".$table->numperpage."
		");

		$queryCount = mysql_query("
			SELECT
				COUNT(*) AS pn
			FROM
				".DB_PREFIX."_PlayerNames
			LEFT JOIN ".DB_PREFIX."_Players ON
				".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerNames.playerId
			WHERE
				".DB_PREFIX."_PlayerNames.name LIKE '%".mysql_escape_string($sr_query)."%'
				".$andgame."");
		$result = mysql_fetch_assoc($queryCount);
		$numitems = $result['pn'];


		$table->draw($query, $numitems, 90);
	}
	elseif ($this->type == "uniqueid")
	{
		$table = new Table(
			array(
				new TableColumn(
					"uniqueId",
					$this->uniqueid_string,
					"width=15&align=right"
				),
				new TableColumn(
					"lastName",
					"Name",
					"width=50&icon=player&link=" . urlencode($link_player)
				),
				new TableColumn(
					"gamename",
					"Game",
					"width=30"
				),
				new TableColumn(
					"playerId",
					"ID",
					"width=5&align=right"
				)
			),
			"playerId",
			"lastName",
			"uniqueId",
			false,
			50,
			"page",
			"sort",
			"sortorder",
			"results",
			"asc"
		);

		if ($this->game)
			$andgame = "AND ".DB_PREFIX."_PlayerUniqueIds.game='" . $this->game . "'";
		else
			$andgame = "";

		$query = mysql_query("
			SELECT
				".DB_PREFIX."_PlayerUniqueIds.uniqueId,
				".DB_PREFIX."_PlayerUniqueIds.playerId,
				".DB_PREFIX."_Players.lastName,
				".DB_PREFIX."_Games.name AS gamename
			FROM
				".DB_PREFIX."_PlayerUniqueIds
			LEFT JOIN ".DB_PREFIX."_Players ON
				".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerUniqueIds.playerId
			LEFT JOIN ".DB_PREFIX."_Games ON
				".DB_PREFIX."_Games.code = ".DB_PREFIX."_PlayerUniqueIds.game
			WHERE
				".DB_PREFIX."_Games.hidden='0' AND
				".DB_PREFIX."_PlayerUniqueIds.uniqueId LIKE '%".mysql_escape_string($sr_query)."%'
				".$andgame."
			ORDER BY
				".$table->sort." ".$table->sortorder.",
				".$table->sort2." ".$table->sortorder."
			LIMIT ".$table->startitem.",".$table->numperpage."");

		$queryCount = mysql_query("
			SELECT
				COUNT(*) AS pu
			FROM
				".DB_PREFIX."_PlayerUniqueIds
			LEFT JOIN ".DB_PREFIX."_Players ON
				".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerUniqueIds.playerId
			WHERE
				".DB_PREFIX."_PlayerUniqueIds.uniqueId LIKE '%".mysql_escape_string($sr_query)."%'
				".$andgame."
		");
		$result = mysql_fetch_assoc($queryCount);
		$numitems = $result['pu'];

		$table->draw($query, $numitems, 90);
	}
	elseif ($this->type == "clan")
	{
		$table = new Table(
			array(
				new TableColumn(
					"tag",
					"Tag",
					"width=15"
				),
				new TableColumn(
					"name",
					"Name",
					"width=50&icon=clan&link=" . urlencode($link_clan)
				),
				new TableColumn(
					"gamename",
					"Game",
					"width=30"
				),
				new TableColumn(
					"clanId",
					"ID",
					"width=5&align=right"
				)
			),
			"clanId",
			"name",
			"tag",
			false,
			50,
			"page",
			"sort",
			"sortorder",
			"results",
			"asc"
		);

		if ($this->game)
			$andgame = "AND ".DB_PREFIX."_Clans.game='" . $this->game . "'";
		else
			$andgame = "";

		$query = mysql_query("
			SELECT
				".DB_PREFIX."_Clans.clanId,
				".DB_PREFIX."_Clans.tag,
				".DB_PREFIX."_Clans.name,
				".DB_PREFIX."_Games.name AS gamename
			FROM
				".DB_PREFIX."_Clans
			LEFT JOIN ".DB_PREFIX."_Games ON
				".DB_PREFIX."_Games.code = ".DB_PREFIX."_Clans.game
			WHERE
				".DB_PREFIX."_Games.hidden='0' AND
				(
					".DB_PREFIX."_Clans.tag LIKE '%".mysql_escape_string($sr_query)."%'
					OR ".DB_PREFIX."_Clans.name LIKE '%".mysql_escaoe_string($sr_query)."%'
				)
				$andgame
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder
			LIMIT $table->startitem,$table->numperpage
		");

		$queryCount = mysql_query("
			SELECT COUNT(*) AS cc
			FROM
				".DB_PREFIX."_Clans
			WHERE
				(
					tag LIKE '%".mysql_escape_string($sr_query)."%'
					OR name LIKE '%".mysql_escape_string($sr_query)."%'
				)
				".$andgame."
		");
		$result = mysql_fetch_assoc($queryCount);
		$numitems = $result['cc'];

		$table->draw($query, $numitems, 90);
	}
	*/
}

#$search = new Search($sr_query, $sr_type, $sr_game);

#$search->drawForm(array("mode"=>"search"));
#if ($sr_query || $sr_query == "0") $search->drawResults();
?>

<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="index.php"><?php echo l('Back to start page'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l('Find a Player or Clan'); ?></h1>
	<form method="post" action="">
		<b><?php echo l('Search For'); ?></b>:<br />
		<input type="text" name="search[input]" value="" /><br />
		<br />
		<b><?php echo l('In'); ?></b>
		<select name="search[area]">
			<option value="player"><?php echo l('Player names'); ?></option>
			<option value="clan"><?php echo l('Clan names'); ?></option>
		</select><br />
		<br />
		<?php echo l('Game'); ?>
		<select name="search[game]">
			<option value="---"><?php echo l('All'); ?></option>
			<?php
			foreach($gamesArr as $k=>$v) {
				echo '<option value="',$k,'">',$v,'</option>';
			}
			?>
		</select><br />
		<br />
		<button type="submit" name="submit[search]" title="<?php echo l('Find Now'); ?>">
			<?php echo l('Find Now'); ?>
		</button>
	</form>
	<?php
		if(!empty($searchResults)) {
			echo '<ul>';
			foreach($searchResults as $gn=>$entry) {
				echo '<li><b>',$gn,'</b>';
				if(!empty($entry)) {
					echo '<ul>';
					foreach($entry as $e) {
						echo '<li><a href="index.php?mode=playerinfo&player=',$e['playerId'],'">',$e['name'],'</a></li>';
					}
					echo '</ul>';
				}
				echo '</li>';
			}
			echo '</ul>';
		}
	?>
</div>
