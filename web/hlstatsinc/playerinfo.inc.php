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

// Player Details
$player = '';
$uniqueid = '';
$killLimit = 5;
$mode = false;
$pl_name = '';
$pl_urlname = '';

if(!empty($_GET["player"])) {
	if(validateInput($_GET["player"],'digit') === true) {
		$player = $_GET["player"];
	}
}
if(!empty($_GET["uniqueid"])) {
	if(validateInput($_GET["uniqueid"],'digit') === true) {
		$uniqueid  = $_GET["uniqueid"];
		$mode = true;
	}
}
if(!empty($_GET['killLimit'])) {
	if(validateInput($_GET['killLimit'],'digit') === true) {
		$killLimit = $_GET['killLimit'];
	}
}
/*
@todo: remove
if (!$player && $uniqueid) {
	if (!$game) {
		header("Location: index.php?mode=search&st=uniqueid&q=$uniqueid");
		exit;
	}

	$query = mysql_query("SELECT playerId FROM ".DB_PREFIX."_PlayerUniqueIds
		WHERE uniqueId='".mysql_escape_string($uniqueid)."'
			AND game='".mysql_escape_string($game)."'
	");

	if (mysql_num_rows($query) > 1) {
		header("Location: index.php?mode=search&st=uniqueid&q=$uniqueid&game=$game");
		exit;
	}
	elseif (mysql_num_rows($query) < 1) {
		error("No players found matching uniqueId '$uniqueid'");
	}
	else {
		$result = mysql_fetch_assoc($query);
		$player = $result['playerId'];
	}
	mysql_free_result($query);
}
elseif (!$player && !$uniqueid) {
	error("No player ID specified.");
}
*/

require('class/player.class.php');
$playerObj = new Player($player,$mode,$game);
if($playerObj === false) {
	die('No such player');
}
$playerObj->loadFullInformation();

/*
if(defined('ELORATING') && ELORATING === "1") {
	$queryPlayer = mysql_query("
		SELECT
			".DB_PREFIX."_Players.lastName,
			".DB_PREFIX."_Players.clan,
			".DB_PREFIX."_Players.fullName,
			".DB_PREFIX."_Players.email,
			".DB_PREFIX."_Players.homepage,
			".DB_PREFIX."_Players.icq,
			".DB_PREFIX."_Players.game,
			".DB_PREFIX."_Players.skill,
			".DB_PREFIX."_Players.oldSkill,
			".DB_PREFIX."_Players.kills,
			ROUND(".DB_PREFIX."_Players.rating) as rating,
			ROUND(SQRT(".DB_PREFIX."_Players.rd2)) as rd,
			".DB_PREFIX."_Players.deaths,
			IFNULL(kills/deaths, '-') AS kpd,
			".DB_PREFIX."_Players.suicides,
			CONCAT(".DB_PREFIX."_Clans.tag, ' ', ".DB_PREFIX."_Clans.name) AS clan_name
		FROM
			".DB_PREFIX."_Players
		LEFT JOIN ".DB_PREFIX."_Clans ON
			".DB_PREFIX."_Clans.clanId = ".DB_PREFIX."_Players.clan
		WHERE
			playerId='$player'
	");
	if (mysql_num_rows($queryPlayer) != 1)
		error("No such player '$player'.");
}
elseif(defined('ELORATING') && ELORATING === "2") {
	//@todo
}
else {
*/

/*
$queryPlayer = mysql_query("
		SELECT
			".DB_PREFIX."_Players.lastName,
			".DB_PREFIX."_Players.clan,
			".DB_PREFIX."_Players.fullName,
			".DB_PREFIX."_Players.email,
			".DB_PREFIX."_Players.homepage,
			".DB_PREFIX."_Players.icq,
			".DB_PREFIX."_Players.game,
			".DB_PREFIX."_Players.skill,
			".DB_PREFIX."_Players.oldSkill,
			".DB_PREFIX."_Players.kills,
			".DB_PREFIX."_Players.deaths,
			IFNULL(kills/deaths, '-') AS kpd,
			".DB_PREFIX."_Players.suicides,
			CONCAT(".DB_PREFIX."_Clans.tag, ' ', ".DB_PREFIX."_Clans.name) AS clan_name
		FROM
			".DB_PREFIX."_Players
		LEFT JOIN ".DB_PREFIX."_Clans ON
			".DB_PREFIX."_Clans.clanId = ".DB_PREFIX."_Players.clan
		WHERE
			playerId='".mysql_escape_string($player)."'
	");
	if (mysql_num_rows($queryPlayer) != 1)
		error("No such player '$player'.");
//}

$playerdata = mysql_fetch_assoc($queryPlayer);
mysql_free_result($queryPlayer);
*/
/*
$pl_name = $playerdata["lastName"];
if (strlen($pl_name) > 10) {
	$pl_shortname = substr($pl_name, 0, 8) . "...";
}
else {
	$pl_shortname = $pl_name;
}
*/

//$pl_name = ereg_replace(" ", "&nbsp;", htmlspecialchars($playerObj->getParam('name')));
$pl_name = makeSavePlayerName($playerObj->getParam('name'));
$pl_urlname = urlencode($playerObj->getParam('lastName'));


// get the game name
// if it fails we use the game code which is stored in the player table
$game = $playerObj->getParam("game");
$query = mysql_query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='".mysql_escape_string($game)."'");
if (mysql_num_rows($query) != 1) {
	$gamename = ucfirst($game);
}
else {
	$result = mysql_fetch_assoc($query);
	$gamename = $result['name'];
}
mysql_free_result($query);

// show header
pageHeader(
	array($gamename, l("Player Details"), $pl_name),
	array(
		$gamename => "index.php?game=$game",
		l("Player Rankings") => "index.php?mode=players&game=$game",
		l("Player Details")=>""
	),
	$pl_name
);

$rcol = "row-dark";
?>

<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="index.php?mode=playerhistory&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/history.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="history.gif"><?php echo('Event History'); ?></a>
			</li>
			<li>
				<a href="index.php?mode=playerchathistory&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/history.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="history.gif"><?php echo l('Chat History'); ?></a>
			</li>
			<li>
				<a href="index.php?mode=search&st=player&q=<?php echo $pl_urlname; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/search.gif" width="16" height="16" hspace="3" border="0" align="middle" alt="search.gif"><?php echo l('Find other players with the same name'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l('Player Profile'); ?> / <?php echo l('Statistics Summary'); ?></h1>
	<table border="1" cellspacing="0" cellpadding="4" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th>
			   <?php echo l("Member of Clan"); ?>
			</th>
			<td>
				<?php if ($playerObj->getParam("clan")) { ?>
					<a href="index.php?mode=claninfo&clan=<?php echo $playerObj->getParam("clan"); ?>">
					<img src="<?php echo $g_options['imgdir']; ?>/clan.gif" width="16" height="16" hspace="4"
							border="0" align="middle" alt="clan.gif"> />
					<?php echo htmlspecialchars($playerObj->getParam("clan_name")); ?>
					</a>
				<?php }	else {
					echo l('None');
				}
				?>
			</td>
			<th align="right"><?php	echo l("Points"); ?></th>
			<td>
				<?php echo $playerObj->getParam("skill");?>

				<?php if($playerObj->getParam('skill') > $playerObj->getParam('oldSkill')) { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/skill_up.gif" width='16' height='16' hspace='4'
					border='0' align="middle" alt="skill_up.gif" />
				<?php } elseif ($playerObj->getParam('skill') < $playerObj->getParam('oldSkill')) { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/skill_down.gif" width='16' height='16' hspace='4'
					border='0' align="middle" alt="skill_down" />
				<?php } else { ?>
				<img src="<?php echo $g_options["imgdir"]; ?>/skill_stay.gif" width='16' height='16' hspace='4'
					border='0' align="middle" alt="skill_stay.gif" />
				<?php } ?>
			 </td>
		</tr>
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l("Real Name"); ?></th>
			<td>
			   <?php
				if ($playerObj->getParam("fullName")) {
					echo "<b>" . htmlspecialchars($playerObj->getParam("fullName")) . "</b>";
				} else {
					echo l("Unknown");
				}
			   ?>
			</td>
			<th align="right"><?php echo l("Rank"); ?></th>
			<td>
				<b><?php echo $playerObj->getParam('rankPoints'); ?></b>
				(<?php echo l('ordered by Points'); ?>)
			</td>
		</tr>
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l("E-mail Address"); ?></th>
			<td>
			   <?php
				$email = getEmailLink($playerObj->getParam("email"));
				if (!empty($email)) {
					echo $email;
				} else {
					echo l("Unknown");
				}
			   ?>
			</td>
			<th><?php echo l("Kills"); ?></th>
			<td><?php echo $playerObj->getParam("kills"); ?></td>
		</tr>
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l("Home Page"); ?></th>
			<td>
				<?php
				$url = getLink($playerObj->getParam("homepage"));
				if (!empty($url)) {
					echo $url;
				} else {
					echo l("Not specified");
				}
			   ?>
			</td>
			<th><?php echo l("Deaths"); ?></th>
			<td><?php echo $playerObj->getParam("deaths"); ?></td>
		</tr>
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l("ICQ Number"); ?></th>
			<td>
			   <?php
				if ($playerObj->getParam("icq")) {
					echo "<a href=\"http://www.icq.com/"
						. urlencode($playerObj->getParam("icq")) . "\" target=\"_blank\">"
						. htmlspecialchars($playerObj->getParam("icq")) . "</a>";
				} else {
					echo l("Not specified");
				}
			   ?>
			</td>
			<th><?php echo l("Suicides"); ?></th>
			<td><?php echo $playerObj->getParam("suicides"); ?></td>
		</tr>
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l("Player ID"); ?></th>
			<td><?php echo $player; ?></td>
			<th><?php echo l("Kills per Death"); ?></th>
			<td><?php echo $playerObj->getParam("kpd"); ?></t>
		</tr>
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th>
			   <?php if (MODE == "LAN") {
					echo l("IP Addresses");
				} else {
					echo l("Unique ID(s)");
				}
			   ?>
			</th>
			<td>
			   <?php
				if (MODE == "NameTrack") {
					echo l("Unknown");
				} else {
					echo $playerObj->getParam('uniqueIds');
				}
			   ?>
			</td>
			<th><?php echo l("Teammate Kills"); ?>*</th>
			<td><?php echo $playerObj->getParam("teamkills"); ?></td>
		</tr>
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l("Last Connect"); ?>*</th>
			<td><?php echo $playerObj->getParam('lastConnect'); ?></td>
			<th><?php echo l("Weapon Accuracy"); ?></th>
			<td><?php echo $playerObj->getParam("accuracy"); ?>%</td>
		</tr>
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l("Total Connection Time"); ?>*</th>
			<td><?php echo $playerObj->getParam('maxTime'); ?></td>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l("Average Ping"); ?></th>
			<td><?php echo $playerObj->getParam('avgPing'); ?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>

	<?php
	$aliases = $playerObj->getParam('aliases');
	if(!empty($aliases)) { ?>
	<a name="aliases"></a>
	<h1>
		<?php echo l('Aliases'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#aliases"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Name'); ?></th>
			<th><?php echo l('Used'); ?></th>
			<th><?php echo l('Last Use'); ?></th>
			<th><?php echo l('Kills'); ?></th>
			<th><?php echo l('Deaths'); ?></th>
			<th><?php echo l('Kills per Death'); ?></th>
			<th><?php echo l('Suicides'); ?></th>
		</tr>
		<?php
		foreach ($aliases as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td>',makeSavePlayerName($entry['name']),'</td>';
			echo '<td>',$entry['numuses'],'</td>';
			echo '<td>',$entry['lastuse'],'</td>';
			echo '<td>',$entry['kills'],'</td>';
			echo '<td>',$entry['deaths'],'</td>';
			echo '<td>',$entry['kpd'],'</td>';
			echo '<td>',$entry['suicides'],'</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php }

	$actions = $playerObj->getParam('actions');
	if(!empty($actions)) { ?>
	<a name="playeractions"></a>
	<h1>
		<?php echo l('Player Actions'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#playeractions"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Action'); ?></th>
			<th><?php echo l('Achieved'); ?></th>
			<th><?php echo l('Points Bonus'); ?></th>
		</tr>
		<?php
		foreach ($actions as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td>',$entry['description'],'</td>';
			echo '<td>',$entry['obj_count'],'</td>';
			echo '<td>',$entry['obj_bonus'],'</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php }

	$playerPlayerActions = $playerObj->getParam('playerPlayerActions');
	if(!empty($playerPlayerActions)) { ?>
	<a name="playerplayeractions"></a>
	<h1>
		<?php echo l('Player-Player Actions'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#playerplayeractions"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Action'); ?></th>
			<th><?php echo l('Achieved'); ?></th>
			<th><?php echo l('Points Bonus'); ?></th>
		</tr>
		<?php
		foreach ($playerPlayerActions as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td>',$entry['description'],'</td>';
			echo '<td>',$entry['obj_count'],'</td>';
			echo '<td>',$entry['obj_bonus'],'</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php }

	$teamSelection = $playerObj->getParam('teamSelection');
	if(!empty($teamSelection)) { ?>
	<a name="teams"></a>
	<h1>
		<?php echo l('Team Selection'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#teams"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Team'); ?></th>
			<th><?php echo l('Joined'); ?></th>
			<th><?php echo l('Percentage of times'); ?></th>
		</tr>
		<?php
		foreach ($teamSelection as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td>',$entry['name'],'</td>';
			echo '<td>',$entry['teamcount'],'</td>';
			echo '<td>',number_format($entry['percent'],2),'%</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php }

	$weaponUsage = $playerObj->getParam('weaponUsage');
	if(!empty($weaponUsage)) { ?>
	<a name="weaponusage"></a>
	<h1>
		<?php echo l('Weapon Usage'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#weaponusage"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Weapon'); ?></th>
			<th><?php echo l('Points Modifier'); ?></th>
			<th><?php echo l('Kills'); ?></th>
			<th><?php echo l('Percentage of Kills'); ?></th>
		</tr>
		<?php
		foreach ($weaponUsage as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td align="center"><img src="hlstatsimg/weapons/',$game,'/',$entry['weapon'],'.png" alt="',$entry['weapon'],'" title="',$entry['weapon'],'" /></td>';
			echo '<td>',$entry['modifier'],'</td>';
			echo '<td>',$entry['kills'],'</td>';
			echo '<td>',number_format($entry['percent'],2),'%</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php }

	$weaponStats = $playerObj->getParam('weaponStats');
	if(!empty($weaponStats)) { ?>
	<a name="weaponstats"></a>
	<h1>
		<?php echo l('Weapon Stats'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#weaponstats"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Weapon'); ?></th>
			<th><?php echo l('Shots'); ?></th>
			<th><?php echo l('Hits'); ?></th>
			<th><?php echo l('Damage'); ?></th>
			<th><?php echo l('Head Shots'); ?></th>
			<th><?php echo l('Kills'); ?></th>
			<th><?php echo l('Deaths'); ?></th>
			<th><?php echo l('Kills per Death'); ?></th>
			<th><?php echo l('Accuracy'); ?></th>
			<th><?php echo l('Damage per Hit'); ?></th>
			<th><?php echo l('Shots per Kill'); ?></th>
		</tr>
		<?php
		foreach ($weaponStats as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td align="center"><img src="hlstatsimg/weapons/',$game,'/',$entry['smweapon'],'.png" alt="',$entry['smweapon'],'" title="',$entry['smweapon'],'" /></td>';
			echo '<td>',$entry['smshots'],'</td>';
			echo '<td>',$entry['smhits'],'</td>';
			echo '<td>',$entry['smdamage'],'</td>';
			echo '<td>',$entry['smheadshots'],'</td>';
			echo '<td>',$entry['smkills'],'</td>';
			echo '<td>',$entry['smdeaths'],'</td>';
			echo '<td>',number_format($entry['smkdr'],1),'</td>';
			echo '<td>',$entry['smaccuracy'],'</td>';
			echo '<td>',$entry['smdhr'],'</td>';
			echo '<td>',$entry['smspk'],'</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php }

	$weaponTarget = $playerObj->getParam('weaponTarget');
	if(!empty($weaponTarget)) { ?>
	<a name="weapontarget"></a>
	<h1>
		<?php echo l('Weapon Target'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#weapontarget"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Weapon'); ?></th>
			<th><?php echo l('Head'); ?></th>
			<th><?php echo l('Chest'); ?></th>
			<th><?php echo l('Stomach'); ?></th>
			<th><?php echo l('Left Arm'); ?></th>
			<th><?php echo l('Right Arm'); ?></th>
			<th><?php echo l('Left Leg'); ?></th>
			<th><?php echo l('Right Leg'); ?></th>
		</tr>
		<?php
		foreach ($weaponTarget as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td align="center"><img src="hlstatsimg/weapons/',$game,'/',$entry['smweapon'],'.png" alt="',$entry['smweapon'],'" title="',$entry['smweapon'],'" /></td>';
			echo '<td>',$entry['smhead'],'</td>';
			echo '<td>',$entry['smchest'],'</td>';
			echo '<td>',$entry['smstomach'],'</td>';
			echo '<td>',$entry['smleftarm'],'</td>';
			echo '<td>',$entry['smrightarm'],'</td>';
			echo '<td>',$entry['smleftleg'],'</td>';
			echo '<td>',$entry['smrightleg'],'</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php }



	if($g_options['showChart'] == "1") {
		$query = mysql_query("SELECT
				".DB_PREFIX."_Events_StatsmeTime.*,
				TIME_TO_SEC(".DB_PREFIX."_Events_StatsmeTime.time) as tTime
			FROM
				".DB_PREFIX."_Events_StatsmeTime
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_StatsmeTime.serverId
			WHERE
				".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'
				AND playerId='".mysql_escape_string($player)."'");
		$eventsArr = array();
		while($result = mysql_fetch_assoc($query)) {
			$eventsArr[] = $result;
		}
		if(!empty($eventsArr)) {
		    // group by day
		    foreach ($eventsArr as $entry) {
		    	$dateArr = explode(" ",$entry['eventTime']);
				$eventsGruped[$dateArr[0]][] = $entry;
		    }

		    // create the xml data for the flash
	        $timeLineData['xml'] = "<?xml version='1.0' encoding='UTF-8'?>";
	        $timeLineData['xml'] .= "<chart>";

	        // first we create the data for x
	        $timeLineData['xml'] .= "<series>";
	        foreach ($eventsGruped as $day=>$val) {
	        	$timeLineData['xml'] .= "<value xid='".$day."'>".$day."</value>";
	        }
	        $timeLineData['xml'] .= "</series>";

	        // now we create the graphs
	        $timeLineData['xml'] .= "<graphs>";

	        foreach ($eventsGruped as $day=>$events) {
	        	$totalTime = 0;
	            foreach ($events as $eventArr) {
					$totalTime += $eventArr['tTime']; // seconds
	            }
	            if($totalTime >= 3600) {
	            	$hours = intval($totalTime / 3600);

	            	$secondsLeft = $totalTime - ($hours * 3600);
	            	$minutes = intval($secondsLeft / 60);

	            	$seconds = $secondsLeft - ($minutes * 60);

	            	$value = $hours."h ".$minutes."m ".$seconds."s";
	            }
	            else {
	            	$minutes = intval($totalTime / 60);
	            	$seconds = $totalTime - ($minutes * 60);

	            	$value = $minutes."m ".$seconds."s";
	            }
	        	$timeLineData['xml'] .= "<graph gid='".$day."' title='".$value."'>";
	        	$timeLineData['xml'] .= "<value xid='".$day."' description='Playertime'>".$totalTime."</value>";
	        	$timeLineData['xml'] .= "</graph>";
	        }

	        $timeLineData['xml'] .= "</graphs>";

	        $timeLineData['xml'] .= "</chart>";

	        $timeLineData['height'] = 250;
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%" align="left">
		<a name="aliases"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b> <?php echo l('Playtime per day'); ?></b> (<?php echo l('hover over the bars to get more information'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<div style="text-align: center; display: block;" id="flash_timeline">
	        <div id="playerTimeline">
				<?php echo $g_options["font_normal"]; ?>
				<b><?php echo l('You need to upgrade your flash player'); ?></b><br />
				<a href="http://www.adobe.com/go/getflashplayer" target="_blank"><?php echo l('Get Flashplayer'); ?></a>
				<?php echo $g_options["fontend_normal"];?>
			</div>
			<script type="text/javascript">
				// <![CDATA]
				var so = new SWFObject("hlstatsinc/amcharts/column/amcolumn.swf?<?php echo time(); ?>", "playerTimeline", "600", "<?php echo $timeLineData["height"]; ?>", "8", "<?php echo $g_options['body_bgcolor']; ?>");
				so.addVariable("path", "hlstatsinc/amcharts/column/");
				so.addVariable("settings_file", escape("hlstatsinc/amcharts/column/settings_playertime.xml"));
				so.addVariable("chart_data", "<?php echo $timeLineData['xml']; ?>");
				so.addVariable("additional_chart_settings", "<settings><text_color><?php echo $g_options["body_text"]; ?></text_color></settings>");
				so.addVariable("preloader_color", "<?php echo $g_options["body_text"]; ?>");
				so.write("playerTimeline");
				// ]]
			</script>
		</div>
	</div>
	</td>
</tr>
</table>
<?php
		}
	}

	flush();
	$tblRoles = new Table(
		array(
			new TableColumn(
				"name",
				"Role",
				"width=35&type=roleimg"
			),
			new TableColumn(
				"rolecount",
				"Joined",
				"width=10&align=right&append=+times"
			),
			new TableColumn(
				"percent",
				"Percentage of Times",
				"width=40&sort=no&type=bargraph"
			),
			new TableColumn(
				"percent",
				"%",
				"width=10&sort=no&align=right&append=" . urlencode("%")
			)
		),
		"name",
		"rolecount",
		"name",
		true,
		9999,
		"roles_page",
		"roles_sort",
		"roles_sortorder",
		"roles"
	);

	$queryRoles = mysql_query("SELECT COUNT(*) AS rj FROM ".DB_PREFIX."_Events_ChangeRole WHERE playerId=".mysql_escape_string($player)."");
	$result = mysql_fetch_assoc($queryRoles);
	$numrolejoins = $result['rj'];
	mysql_free_result($queryRoles);

	$query = mysql_query("
		SELECT
			IFNULL(".DB_PREFIX."_Roles.name, ".DB_PREFIX."_Events_ChangeRole.role) AS name,
			COUNT(".DB_PREFIX."_Events_ChangeRole.id) AS rolecount,
			COUNT(".DB_PREFIX."_Events_ChangeRole.id) / $numrolejoins * 100 AS percent,
			".DB_PREFIX."_Roles.code AS rolecode
		FROM
			".DB_PREFIX."_Events_ChangeRole
		LEFT JOIN ".DB_PREFIX."_Roles ON
			".DB_PREFIX."_Events_ChangeRole.role=".DB_PREFIX."_Roles.code
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_ChangeRole.serverId
		WHERE
			".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Events_ChangeRole.playerId=".mysql_escape_string($player)."
			AND (hidden <>'1' OR hidden IS NULL)
		GROUP BY
			".DB_PREFIX."_Events_ChangeRole.role
		ORDER BY
			".$tblRoles->sort." ".$tblRoles->sortorder.",
			".$tblRoles->sort2." ".$tblRoles->sortorder."
	");

	$numitems = mysql_num_rows($query);

	if ($numitems > 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="roles"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Role Selection'); ?></b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblRoles->draw($query, $numitems, 100);
	?>
	</div></td>
</tr>
</table><p>

<?php
	}


	flush();
	$tblMaps = new Table(
		array(
			new TableColumn(
				"map",
				"Map Name",
				"width=25&align=center&link=" . urlencode("mode=mapinfo&map=%k&game=$game")
			),
			new TableColumn(
				"kpd",
				"Kills per Death",
				"width=10&align=right"
			),
			new TableColumn(
				"kills",
				"Kills",
				"width=10&align=right"
			),
			new TableColumn(
				"percentage",
				"Percentage of Kills",
				"width=30&sort=no&type=bargraph"
			),
			new TableColumn(
				"percentage",
				"%",
				"width=10&sort=no&align=right&append=" . urlencode("%")
			),
			new TableColumn(
				"deaths",
				"Deaths",
				"width=10&align=right"
			)
		),
		"map",
		"kpd",
		"kills",
		true,
		9999,
		"maps_page",
		"maps_sort",
		"maps_sortorder",
		"maps"
	);

	$query = mysql_query("
		SELECT
			IF(map='', '(Unaccounted)', map) AS map,
			SUM(killerId=".mysql_escape_string($player).") AS kills,
			SUM(victimId=".mysql_escape_string($player).") AS deaths,
			IFNULL(SUM(killerId=".mysql_escape_string($player).") / SUM(victimId=".mysql_escape_string($player)."), '-') AS kpd,
			ROUND(CONCAT(SUM(killerId=".mysql_escape_string($player).")) / ".mysql_escape_string($realkills)." * 100, 2) AS percentage
		FROM
			".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
		WHERE
			".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."' AND killerId='".mysql_escape_string($player)."'
			OR victimId='".mysql_escape_string($player)."'
		GROUP BY
			map
		ORDER BY
			".$tblMaps->sort." ".$tblMaps->sortorder.",
			".$tblMaps->sort2." ".$tblMaps->sortorder."
	");
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="maps"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Map Performance'); ?></b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last') ;?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblMaps->draw($query, mysql_num_rows($query), 100);
	?>
	</div></td>
</tr>
</table><p>
<?php
	flush();
	$tblPlayerKillStats = new Table(
		array(
			new TableColumn(
				"name",
				"Victim",
        "width=35&icon=player&link=" . urlencode("mode=playerinfo&player=%k")
			),
			new TableColumn(
				"kills",
				"Times Killed",
        "width=20&align=right"
			),
			new TableColumn(
				"deaths",
				"Deaths by",
        "width=20&align=right"
			),
			new TableColumn(
				"kpd",
				"Kills per Death",
        "width=20&align=right"
			)
		),
		"victimId",
		"kills",
		"deaths",
		true,
		9999,
		"playerkills_page",
		"playerkills_sort",
		"playerkills_sortorder",
		"playerkills"
	);


	//there might be a better way to do this, but I could not figure one out.
	 mysql_query("DROP TABLE IF EXISTS ".DB_PREFIX."_Frags_Kills");
	 mysql_query("
		CREATE TEMPORARY TABLE ".DB_PREFIX."_Frags_Kills
		(
			playerId INT(10),
			kills INT(10),
			deaths INT(10)
		) DEFAULT CHARSET=utf8
	");
	 mysql_query("
			INSERT INTO
				".DB_PREFIX."_Frags_Kills
				(
					playerId,
					kills
				)
					SELECT
						victimId,
						killerId
					FROM
						".DB_PREFIX."_Events_Frags
					LEFT JOIN ".DB_PREFIX."_Servers ON
						".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
					WHERE
						".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."' AND killerId = ".mysql_escape_string($player)."
	");

	 mysql_query("
			INSERT INTO
				".DB_PREFIX."_Frags_Kills
				(
					playerId,
					deaths
				)
					SELECT
						killerId,
						victimId
					FROM
						".DB_PREFIX."_Events_Frags
					LEFT JOIN ".DB_PREFIX."_Servers ON
						".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
					WHERE
						".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'
						AND victimId = ".mysql_escape_string($player)."
		");

		$query = mysql_query("
				SELECT
					".DB_PREFIX."_Players.lastName AS name,
					".DB_PREFIX."_Players.active,
					Count(".DB_PREFIX."_Frags_Kills.kills) AS kills,
					Count(".DB_PREFIX."_Frags_Kills.deaths) AS deaths,
					".DB_PREFIX."_Frags_Kills.playerId as victimId,
					IFNULL(Count(".DB_PREFIX."_Frags_Kills.kills)/Count(".DB_PREFIX."_Frags_Kills.deaths),
						IFNULL(FORMAT(Count(".DB_PREFIX."_Frags_Kills.kills), 2), '-')) AS kpd
				FROM
					".DB_PREFIX."_Frags_Kills
				INNER JOIN
					".DB_PREFIX."_Players
				ON
					".DB_PREFIX."_Frags_Kills.playerId = ".DB_PREFIX."_Players.playerId
				WHERE
					".DB_PREFIX."_Players.hideranking = 0
				GROUP BY
					".DB_PREFIX."_Frags_Kills.playerId
				HAVING
					Count(".DB_PREFIX."_Frags_Kills.kills) >= ".mysql_escape_string($killLimit)."
				ORDER BY
	            ".$tblPlayerKillStats->sort." ".$tblPlayerKillStats->sortorder.",
	            ".$tblPlayerKillStats->sort2." ".$tblPlayerKillStats->sortorder."
				LIMIT 10
		");

	$numitems = mysql_num_rows($query);


?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td width="50%">
  	<a name="playerkills"></a>
	<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Player Kill Statistics'); ?> (<?php echo $killLimit ?> <?php echo l('or more kills'); ?>)</b><?php echo $g_options["fontend_normal"];?>
 </td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
	if ($numitems > 0) {
    	$tblPlayerKillStats->draw($query, $numitems, 100);
  	}
  	else {
  		echo $g_options["font_normal"].l("Data out of selected range").$g_options["fontend_normal"];
  	}
  	?>
  	</div>
  	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="2">
	<form method="GET" action="">
	<?php echo $g_options["font_normal"]; ?>
	<?php echo l('Show people this person has killed'); ?>
	<SELECT name="killLimit" onchange='changeLimit(this.options[this.selectedIndex].value)'>
<?php
  for($j = 1; $j < 16; $j++) {
		echo "<option value=$j";
		if($killLimit == $j) { echo " selected"; }
		echo ">$j</option>";
	}
?>
	</select>
	<?php echo l('or more times in the last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?><?php echo $g_options["fontend_normal"];?>
	<script type="text/javascript" language="javascript">
	<!--
	function changeLimit(num) {
		location = "http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ?>?mode=playerinfo&player=<?php echo $player ?>&killLimit=" + num + "#playerkills";
	}
	-->
	</script>
	</form>
	</td>
</tr>
</table><br />

<?php
	if($g_options['showChart'] == "1") {
		// get the kills
		$query = mysql_query("SELECT `eventTime` FROM `".DB_PREFIX."_Events_Frags` WHERE `killerId` = '".mysql_escape_string($player)."'");
		$killsArr = array();
		while($result = mysql_fetch_assoc($query)) {
			$killsArr[] = $result;
		}
		if(!empty($killsArr)) {
			$dateArr = "";
			$eventsGruped = "";
			$timeLineData = "";
			// group by day
		    foreach ($killsArr as $entry) {
		    	$dateArr = explode(" ",$entry['eventTime']);
				$eventsGruped[$dateArr[0]][] = $entry;
		    }

		    // create the xml data for the flash
	        $timeLineData['xml'] = "<?xml version='1.0' encoding='UTF-8'?>";
	        $timeLineData['xml'] .= "<chart>";

	        // first we create the data for x
	        $timeLineData['xml'] .= "<series>";
	        foreach ($eventsGruped as $day=>$val) {
	        	$timeLineData['xml'] .= "<value xid='".$day."'>".$day."</value>";
	        }
	        $timeLineData['xml'] .= "</series>";

	        // now we create the graphs
	        $timeLineData['xml'] .= "<graphs>";

	        foreach ($eventsGruped as $day=>$events) {
				$value = count($events);
	        	$timeLineData['xml'] .= "<graph gid='".$day."' title='".$value."'>";
	        	$timeLineData['xml'] .= "<value xid='".$day."' description='Kills'>".$value."</value>";
	        	$timeLineData['xml'] .= "</graph>";
	        }

	        $timeLineData['xml'] .= "</graphs>";

	        $timeLineData['xml'] .= "</chart>";

	        $timeLineData['height'] = 250;
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td width="70%">
  	<a name="playerkillsperday"></a>
	<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Player Kill Statistics per Day'); ?></b> (<?php echo l('hover over the bars to get more information'); ?>)<?php echo $g_options["fontend_normal"];?>
 </td>
	<td width="30%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<div style="text-align: center; display: block;" id="flash_timeline_kills">
	        <div id="playerKills">
				<?php echo $g_options["font_normal"]; ?>
				<b><?php echo l('You need to upgrade your flash player'); ?></b><br />
				<a href="http://www.adobe.com/go/getflashplayer" target="_blank"><?php echo l('Get Flashplayer'); ?></a>
				<?php echo $g_options["fontend_normal"];?>
			</div>
			<script type="text/javascript">
				// <![CDATA]
				var so = new SWFObject("hlstatsinc/amcharts/column/amcolumn.swf?<?php echo time(); ?>", "playerKills", "600", "<?php echo $timeLineData["height"]; ?>", "8", "<?php echo $g_options['body_bgcolor']; ?>");
				so.addVariable("path", "hlstatsinc/amcharts/column/");
				so.addVariable("settings_file", escape("hlstatsinc/amcharts/column/settings_playertime.xml"));
				so.addVariable("chart_data", "<?php echo $timeLineData['xml']; ?>");
				so.addVariable("additional_chart_settings", "<settings><text_color><?php echo $g_options["body_text"]; ?></text_color></settings>");
				so.addVariable("preloader_color", "<?php echo $g_options["body_text"]; ?>");
				so.write("playerKills");
				// ]]
			</script>
		</div>
  	</div>
  	</td>
</tr>
</table>
<?php
		}
	}
?>
<p>&nbsp;</p>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td width="100%"><?php echo $g_options["font_normal"]; ?><b><?php echo l('Note'); ?></b> <?php echo l('Player event histories cover only the last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?>. <?php echo l('Items marked "Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days" or "*" above are generated from the player\'s Event History. Player kill, death and suicide totals and points ratings cover the entire recorded period'); ?>.<?php echo $g_options["fontend_normal"];?></td>
    </tr>
    <tr>
    	<td width="100%" align="right"><br><br>
    	<?php echo $g_options["font_small"]; ?><b><?php echo l('Admin Options'); ?>:</b> <a href="<?php echo "index.php?mode=admin&task=toolsEditdetailsPlayer&id=$player"; ?>"><?php echo l('Edit Player Details'); ?></a><?php echo $g_options["fontend_small"]; ?></td>
    </tr>
</table>

</div>
