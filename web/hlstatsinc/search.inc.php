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
$queryStr = false;

if(isset($_POST['submit']['search'])) {
	$term = trim($_POST['search']['input']);

	if(!empty($term)) {
		$andgame = "";
		if ($_POST['search']['game'] !== "---") {
			$andgame = "AND ".DB_PREFIX."_Games.name = '".mysql_escape_string($_POST['search']['game'])."'";
		}

		switch($_POST['search']['area']) {
			case 'clan':
				$queryStr = "SELECT
						".DB_PREFIX."_Clans.clanId,
						".DB_PREFIX."_Clans.tag,
						".DB_PREFIX."_Clans.name,
						".DB_PREFIX."_Games.name AS gamename
					FROM ".DB_PREFIX."_Clans
					LEFT JOIN ".DB_PREFIX."_Games ON
						".DB_PREFIX."_Games.code = ".DB_PREFIX."_Clans.game
					WHERE ".DB_PREFIX."_Games.hidden='0' AND
						(
							".DB_PREFIX."_Clans.tag LIKE '%".mysql_escape_string($term)."%'
							OR ".DB_PREFIX."_Clans.name LIKE '%".mysql_escape_string($term)."%'
						)
						".$andgame."
					ORDER BY `name`";
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
			break;
		}
		if(!empty($queryStr)) {
			$query = mysql_query($queryStr);

			if(mysql_num_rows($query) > 0) {
				while($result = mysql_fetch_assoc($query)) {
					$searchResults[$result['gamename']][] = $result;
				}
			}
		}
	}
}
else {
	$_POST = array();
	$_POST['search']['area'] = '';
	$_POST['search']['game'] = '';
}
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
			<option value="player" <?php if($_POST['search']['area'] == "player") echo 'selected="1"'; ?>><?php echo l('Player names'); ?></option>
			<option value="clan" <?php if($_POST['search']['area'] == "clan") echo 'selected="1"'; ?>><?php echo l('Clan names'); ?></option>
		</select><br />
		<br />
		<?php echo l('Game'); ?>
		<select name="search[game]">
			<option value="---"><?php echo l('All'); ?></option>
			<?php
			foreach($gamesArr as $k=>$v) {
				$selected = '';
				if($_POST['search']['game'] == $k) $selected = 'selected="1"';
				echo '<option value="',$k,'" ',$selected,'>',$v,'</option>';
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
