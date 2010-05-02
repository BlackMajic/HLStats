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
$playerObj->setOption('killLimit',$killLimit);
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
				<a href="index.php?mode=playerhistory&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/history.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="history.gif"><?php echo l('Event History'); ?></a>
			</li>
			<li>
				<a href="index.php?mode=playerchathistory&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/history.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="history.gif"><?php echo l('Chat History'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h2><?php echo $pl_name; ?></h2>
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
							border="0" align="middle" alt="clan.gif" />
					<?php echo makeSavePlayerName($playerObj->getParam("clan_name")); ?>
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
			<td><?php echo number_format((int)$playerObj->getParam("kpd"),1); ?></t>
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
			<td><?php echo number_format($playerObj->getParam("accuracy"),1); ?>%</td>
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
			echo '<td>',number_format($entry['kpd'],1),'</td>';
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
			<th><?php echo l('Percentage of Times'); ?></th>
		</tr>
		<?php
		foreach ($teamSelection as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td>',$entry['name'],'</td>';
			echo '<td>',$entry['teamcount'],'</td>';
			echo '<td>';
			echo '<div class="percentBar"><div class="barContent" style="width:',number_format($entry['percent'],0),'px"></div></div>',"\n";
			echo '</td>';
			echo '</tr>';
		}
		?>
	</table>
<?php }

$roleSelection = $playerObj->getParam('roleSelection');
if(!empty($roleSelection)) { ?>
	exit("role selection todo");
	<a name="roles"></a>
	<h1>
		<?php echo l('Role Selection'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#role"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Role'); ?></th>
			<th><?php echo l('Joined'); ?></th>
			<th><?php echo l('Percentage of Times'); ?></th>
		</tr>
		<?php
		foreach ($teamSelection as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td>',$entry['name'],'</td>';
			echo '<td>',$entry['rolecount'],'</td>';
			echo '<td>';
			echo '<div class="percentBar"><div class="barContent" style="width:',number_format($entry['percent'],0),'px"></div></div>',"\n";
			echo '</td>';
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
			echo '<tr class="',toggleRowClass($rcol),'">',"\n";
			echo '<td align="center">',"\n";
			echo '<a href="index.php?mode=weaponinfo&amp;weapon='.$entry['weapon'].'&amp;game='.$game.'"><img src="',$g_options["imgdir"],'/weapons/',$game,'/',$entry['weapon'],'.png" alt="',$entry['weapon'],'" title="',$entry['weapon'],'" /></a>',"\n";
			echo '</td>',"\n";
			echo '<td>',$entry['modifier'],'</td>',"\n";
			echo '<td>',$entry['kills'],'</td>',"\n";
			echo '<td>',"\n";
			echo '<div class="percentBar"><div class="barContent" style="width:',number_format($entry['percent'],0),'px"></div></div>',"\n";
			echo '</td>',"\n";
			echo '</tr>',"\n";
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
			if($entry['smshots'] == "0" && $entry['smhits'] == "0" && $entry['smdamage'] == "0"
				&& $entry['smheadshots'] == "0" && $entry['smkills'] == "0" && $entry['smdeaths'] == "0"
			) {
				continue;
			}
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td align="center"><img src="',$g_options["imgdir"],'/weapons/',$game,'/',$entry['smweapon'],'.png" alt="',$entry['smweapon'],'" title="',$entry['smweapon'],'" /></td>';
			echo '<td>',$entry['smshots'],'</td>';
			echo '<td>',$entry['smhits'],'</td>';
			echo '<td>',$entry['smdamage'],'</td>';
			echo '<td>',$entry['smheadshots'],'</td>';
			echo '<td>',$entry['smkills'],'</td>';
			echo '<td>',$entry['smdeaths'],'</td>';
			echo '<td>',number_format($entry['smkdr'],1),'</td>';
			echo '<td>',number_format($entry['smaccuracy'],1),'%</td>';
			echo '<td>',number_format($entry['smdhr'],1),'</td>';
			echo '<td>',number_format($entry['smspk'],1),'</td>';
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
			if($entry['smhead'] == "0" && $entry['smchest'] == "0" && $entry['smstomach'] == "0"
				&& $entry['smleftarm'] == "0" && $entry['smrightarm'] == "0" && $entry['smleftleg'] == "0"
				&& $entry['smrightleg'] == "0"
			) {
				continue;
			}
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td align="center"><img src="',$g_options["imgdir"],'/weapons/',$game,'/',$entry['smweapon'],'.png" alt="',$entry['smweapon'],'" title="',$entry['smweapon'],'" /></td>';
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

$maps = $playerObj->getParam('maps');
if(!empty($maps)) { ?>
	<a name="maps"></a>
	<h1>
		<?php echo l('Map Performance'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#maps"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Map Name'); ?></th>
			<th><?php echo l('Kills'); ?></th>
			<th><?php echo l('Percentage of Kills'); ?></th>
			<th><?php echo l('Deaths'); ?></th>
			<th><?php echo l('Kills per Death'); ?></th>
		</tr>
		<?php
		foreach ($maps as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td><a href="index.php?mode=mapinfo&game=',$game,'&map=',$entry['map'],'">',$entry['map'],'</a></td>';
			echo '<td>',$entry['kills'],'</td>';
			echo '<td>';
			echo '<div class="percentBar"><div class="barContent" style="width:',number_format($entry['percentage'],0),'px"></div></div>',"\n";
			echo '</td>';
			echo '<td>',$entry['deaths'],'</td>';
			echo '<td>',number_format($entry['kpd'],1),'</td>';
			echo '</tr>';
		}
		?>
	</table>
<?php }

$playerKillStats = $playerObj->getParam('killstats');
if(!empty($playerKillStats)) { ?>
	<a name="killstats"></a>
	<h1>
		<?php echo l('Player Kill Statistics'); ?>
		<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#killstats"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
		<?php echo $killLimit ?> <?php echo l('or more kills'); ?>
		(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
	</h1>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Victim'); ?></th>
			<th><?php echo l('Times Killed'); ?></th>
			<th><?php echo l('Deaths by'); ?></th>
			<th><?php echo l('Kills per Death'); ?></th>
		</tr>
		<?php
		foreach ($playerKillStats as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td>';
				if($entry['active'] == "1") {
					echo '<img src="',$g_options["imgdir"],'/player.gif" width="16" height="16" alt="',l('Player'),'" alt="',l('Player'),'" />';
				}
				else {
					echo '<img src="',$g_options["imgdir"],'/player_inactive.gif" width="16" height="16" alt="',l('Player'),'" alt="',l('Player'),'" />';
				}
				echo '<a href="index.php?mode=playerinfo&player=',$entry['playerId'],'">',makeSavePlayerName($entry['name']),'</a>';
			echo '</td>';
			echo '<td>',$entry['kills'],'</td>';
			echo '<td>',$entry['deaths'],'</td>';
			echo '<td>',number_format($entry['kpd'],1),'</td>';
			echo '</tr>';
		}
		?>
	</table>
	<script type="text/javascript" language="javascript">
	<!--
	function changeLimit(num) {
		location = "index.php?mode=playerinfo&player=<?php echo $player ?>&killLimit=" + num + "#killstats";
	}
	-->
	</script>
	<?php echo l('Show people this person has killed'); ?>
		<select onchange='changeLimit(this.options[this.selectedIndex].value)'>
	<?php
	  for($j = 1; $j < 16; $j++) {
			echo "<option value=$j";
			if($killLimit == $j) { echo " selected"; }
			echo ">$j</option>";
		}
	?>
	</select>
	<?php echo l('or more times in the last'),' ',DELETEDAYS,' ',l('days'); ?>
<?php }


if($g_options['showChart'] == "1") {
	require('class/chart.class.php');
	$chartObj = new Chart($game);
	$playtimeChart = $chartObj->getChart('playTimePerDay',$player);
	if(!empty($chart)) {
?>
		<a name="playtime"></a>
		<h1>
			<?php echo l('Playtime per day'); ?>
			<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#playtime"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
			(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
		</h1>
		<img src="<?php echo $playtimeChart; ?>" />
<?php }

	$chartObj = new Chart($game);
	$killDayChart = $chartObj->getChart('killsPerDay',$player);
	if(!empty($killDayChart)) {
?>
		<a name="playerkillsperday"></a>
		<h1>
			<?php echo l('Player Kill Statistics per Day'); ?>
			<a href="index.php?mode=playerinfo&amp;player=<?php echo $player; ?>#playerkillsperday"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" title="<?php echo l('Direct Link'); ?>" /></a>
			(<?php echo l('Last'),' ',DELETEDAYS,' ',l('Days'); ?>)
		</h1>
		<img src="<?php echo $killDayChart; ?>" />
<?php }
}
?>
<p><b><?php echo l('Note'); ?>:</b><br />
<?php echo l('Player event histories cover only the last'); ?>&nbsp;
<?php echo DELETEDAYS; ?> <?php echo l('days'); ?>. <?php echo l('Items marked "Last'); ?>&nbsp;
<?php echo DELETEDAYS; ?> <?php echo l('Days" or "*" above are generated from the player\'s Event History. Player kill, death and suicide totals and points ratings cover the entire recorded period'); ?>.
</p>
<p style="text-align: right">
    <b><?php echo l('Admin Options'); ?>:</b> <a href="<?php echo "index.php?mode=admin&task=toolsEditdetailsPlayer&id=$player"; ?>"><?php echo l('Edit Player Details'); ?></a>
</p>
</div>
