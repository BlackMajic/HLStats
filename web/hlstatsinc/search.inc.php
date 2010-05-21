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

//require("hlstatsinc/search-class.inc.php");

pageHeader(
	array(l("Search")),
	array(l("Search")=>"")
);

$sr_query = false;
$sr_type = 'player';
$sr_game = false;

if(!empty($_GET["q"])) {
	$sr_query = sanitize($_GET["q"]);
	$sr_query = urldecode($sr_query);
}

if(!empty($_GET["st"])) {
	if(validateInput($_GET["st"],'nospace') === true) {
		$sr_type = $_GET["st"];
	}
}

if(!empty($_GET["game"])) {
	if(validateInput($_GET["game"],'nospace') === true) {
		$sr_game = $_GET["game"];
	}
}

$remoteSearch = false;
// check if we have asearch request via get
if(!empty($sr_query) && !empty($sr_type) && !empty($sr_game)) {
	$remoteSearch = true;
}

// get the game list
$gamesArr = array();
$query = mysql_query("SELECT code, name FROM ".DB_PREFIX."_Games WHERE hidden='0' ORDER BY name");
while ($result = mysql_fetch_assoc($query)) {
	$gamesArr[$result['code']] = $result['name'];
}

$searchResults = false;
$queryStr = false;

if(isset($_POST['submit']['search']) || $remoteSearch === true) {

	if($remoteSearch === false) {
		$sr_query = trim($_POST['search']['input']);
		$sr_game = trim($_POST['search']['game']);
		$sr_type = trim($_POST['search']['area']);
	}

	if(!empty($sr_query)) {
		$andgame = "";
		if ($sr_game !== "---") {
			$andgame = "AND ".DB_PREFIX."_Games.name = '".mysql_escape_string($sr_game)."'";
		}

		switch($sr_type) {
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
							".DB_PREFIX."_Clans.tag LIKE '%".mysql_escape_string($sr_query)."%'
							OR ".DB_PREFIX."_Clans.name LIKE '%".mysql_escape_string($sr_query)."%'
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
						".DB_PREFIX."_PlayerNames.name LIKE '%".mysql_escape_string($sr_query)."%'
						".$andgame."
					ORDER BY `name`";
			break;
		}
		if(!empty($queryStr)) {
			$searchResults = array();
			$query = mysql_query($queryStr);

			if(mysql_num_rows($query) > 0) {
				while($result = mysql_fetch_assoc($query)) {
					$searchResults[$result['gamename']][] = $result;
				}
			}
		}
	}
}


//@todo: search for uniq id
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
		<b><?php echo l('In'); ?></b>:<br />
		<select name="search[area]">
			<option value="player" <?php if($sr_type == "player") echo 'selected="1"'; ?>><?php echo l('Player names'); ?></option>
			<option value="clan" <?php if($sr_type == "clan") echo 'selected="1"'; ?>><?php echo l('Clan names'); ?></option>
		</select><br />
		<br />
		<b><?php echo l('Game'); ?></b>:<br />
		<select name="search[game]">
			<option value="---"><?php echo l('All'); ?></option>
			<?php
			foreach($gamesArr as $k=>$v) {
				$selected = '';
				if($sr_game == $k) $selected = 'selected="1"';
				echo '<option value="',$k,'" ',$selected,'>',$v,'</option>';
			}
			?>
		</select><br />
		<br />
		<button type="submit" name="submit[search]" title="<?php echo l('Find Now'); ?>">
			<?php echo l('Find Now'); ?>
		</button><br />
		<br />
	</form>
	<?php
		if(is_array($searchResults) && !empty($searchResults)) {
			echo '<ul>';
			foreach($searchResults as $gn=>$entry) {
				echo '<li><b>',$gn,'</b>';
				if(!empty($entry)) {
					echo '<ul>';
					foreach($entry as $e) {
						echo '<li><a href="index.php?mode=playerinfo&player=',$e['playerId'],'">',makeSavePlayerName($e['name']),'</a></li>';
					}
					echo '</ul>';
				}
				echo '</li>';
			}
			echo '</ul>';
		}
		elseif(is_array($searchResults)) {
			echo '<b>',l('Nothing found'),'</b>';
		}
	?>
</div>
