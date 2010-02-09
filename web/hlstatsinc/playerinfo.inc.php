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
	<a name="aliases"></a>
	<h1>
		<?php echo l('Aliases'); ?>
		<a href="index.php?mode=playerhistory&amp;player=<?php echo $player; ?>#aliases"><img src="<?php echo $g_options["imgdir"]; ?>/link.gif" alt="<?php echo l('Direct Link'); ?>" /></a>
	</h1>
	<?php
	$aliases = $playerObj->getParam('aliases');
	if(!empty($aliases)) { ?>
	<table cellpadding="2" cellspacing="0" border="1" width="100%">
		<tr class="<?php echo toggleRowClass($rcol); ?>">
			<th><?php echo l('Name'); ?></th>
			<th><?php echo l('Used'); ?></th>
			<th><?php echo l('Kills'); ?></th>
			<th><?php echo l('Deaths'); ?></th>
			<th><?php echo l('Kills per Death'); ?></th>
			<th><?php echo l('Suicides'); ?></th>
		</tr>
		<?php
		foreach ($aliases as $entry) {
			echo '<tr class="',toggleRowClass($rcol),'">';
			echo '<td>',makeSavePlayerName($entry['name']),'</td>';
			echo '<td>',$entry['lastuse'],'</td>';
			echo '<td>',$entry['numuses'],'</td>';
			echo '<td>',$entry['kills'],'</td>';
			echo '<td>',$entry['deaths'],'</td>';
			echo '<td>',$entry['kpd'],'</td>';
			echo '<td>',$entry['suicides'],'</td>';
			echo '</tr>';
		}
		?>
	</table>

	<?php } ?>

<?php
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
	$tblAliases = new Table(
		array(
			new TableColumn(
				"name",
				"Name",
				"width=25"
			),
			new TableColumn(
				"numuses",
				"Used",
				"width=10&align=right&append=+times"
			),
			new TableColumn(
				"lastuse",
				"Last Use",
				"width=20"
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
			),
			new TableColumn(
				"suicides",
				"Suicides",
				"width=10&align=right"
			)
		),
		"name",
		"lastuse",
		"name",
		true,
		20,
		"aliases_page",
		"aliases_sort",
		"aliases_sortorder",
		"aliases"
	);

	$queryAlias = mysql_query("
		SELECT
			name,
			lastuse,
			numuses,
			kills,
			deaths,
			IFNULL(
				kills / deaths,
				'-'
			) AS kpd,
			suicides
		FROM
			".DB_PREFIX."_PlayerNames
		WHERE
			playerId='".mysql_escape_string($player)."'
		ORDER BY
			".$tblAliases->sort." ".$tblAliases->sortorder."
		LIMIT ".$tblAliases->startitem.",".$tblAliases->numperpage."
	");

	$resultCount = mysql_query("SELECT COUNT(*) pl FROM ".DB_PREFIX."_PlayerNames WHERE playerId=".mysql_escape_string($player)."");
	$result = mysql_fetch_assoc($resultCount);
	$numitems = $result['pl'];

	if ($numitems > 1) {
?>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%"></a>
<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b><?php echo l('Aliases'); ?></b><?php echo $g_options["fontend_normal"];?></td>
</tr>
<tr>
	<td>
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblAliases->draw($queryAlias, $numitems, 100);
	?>
	</div>
	</td>
</tr>
</table>
<p>&nbsp;</p>
<?php
	}
	flush();
	$tblPlayerActions = new Table(
		array(
			new TableColumn(
				"description",
				"Action",
				"width=45"
			),
			new TableColumn(
				"obj_count",
				"Achieved",
				"width=25&align=right&append=+times"
			),
			new TableColumn(
				"obj_bonus",
				"Points Bonus",
				"width=25&align=right"
			)
		),
		"id",
		"obj_count",
		"description",
		true,
		9999,
		"obj_page",
		"obj_sort",
		"obj_sortorder",
		"playeractions"
	);

	$query = mysql_query("
		SELECT
			".DB_PREFIX."_Actions.description,
			COUNT(".DB_PREFIX."_Events_PlayerActions.id) AS obj_count,
			COUNT(".DB_PREFIX."_Events_PlayerActions.id) * ".DB_PREFIX."_Actions.reward_player AS obj_bonus
		FROM
			".DB_PREFIX."_Actions
		LEFT JOIN ".DB_PREFIX."_Events_PlayerActions ON
			".DB_PREFIX."_Events_PlayerActions.actionId = ".DB_PREFIX."_Actions.id
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_PlayerActions.serverId
		WHERE
			".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Events_PlayerActions.playerId=".mysql_escape_string($player)."
		GROUP BY
			".DB_PREFIX."_Actions.id
		ORDER BY
			".$tblPlayerActions->sort." ".$tblPlayerActions->sortorder.",
			".$tblPlayerActions->sort2." ".$tblPlayerActions->sortorder."
	");

	$numitems = mysql_num_fields($query);

	if ($numitems > 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="playeractions"></a>
	<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Player Actions'); ?></b><?php echo $g_options["fontend_normal"];?>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?></td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<?php
			$tblPlayerActions->draw($query, $numitems, 100);
		?>
	</div>
	</td>
</tr>
</table>
<p>
<?php
	}
	$tblPlayerPlayerActions = new Table(
		array(
			new TableColumn(
				"description",
				"Action",
				"width=45"
			),
			new TableColumn(
				"obj_count",
				"Achieved",
				"width=25&align=right&append=+times"
			),
			new TableColumn(
				"obj_bonus",
				"Points Bonus",
				"width=25&align=right"
			)
		),
		"id",
		"obj_count",
		"description",
		true,
		9999,
		"ppa_page",
		"ppa_sort",
		"ppa_sortorder",
		"playerplayeractions"
	);

	$query = mysql_query("
		SELECT
			".DB_PREFIX."_Actions.description,
			COUNT(".DB_PREFIX."_Events_PlayerPlayerActions.id) AS obj_count,
			COUNT(".DB_PREFIX."_Events_PlayerPlayerActions.id) * ".DB_PREFIX."_Actions.reward_player AS obj_bonus
		FROM
			".DB_PREFIX."_Actions
		LEFT JOIN ".DB_PREFIX."_Events_PlayerPlayerActions ON
			".DB_PREFIX."_Events_PlayerPlayerActions.actionId = ".DB_PREFIX."_Actions.id
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_PlayerPlayerActions.serverId
		WHERE
			".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Events_PlayerPlayerActions.playerId=".mysql_escape_string($player)."
		GROUP BY
			".DB_PREFIX."_Actions.id
		ORDER BY
			".$tblPlayerPlayerActions->sort." ".$tblPlayerPlayerActions->sortorder.",
			".$tblPlayerPlayerActions->sort2." ".$tblPlayerPlayerActions->sortorder."
	");

	$numitems = mysql_num_rows($query);

	if ($numitems > 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="playerplayeractions"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Player-Player Actions'); ?></b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<?php
			$tblPlayerPlayerActions->draw($query, $numitems, 100);
		?>
	</div>
	</td>
</tr>
</table>
<p>
<?php
	}
	flush();
	$tblTeams = new Table(
		array(
			new TableColumn(
				"name",
				"Team",
				"width=35"
			),
			new TableColumn(
				"teamcount",
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
		"teamcount",
		"name",
		true,
		9999,
		"teams_page",
		"teams_sort",
		"teams_sortorder",
		"teams"
	);

	$queryTjoins = mysql_query("SELECT COUNT(*) AS tj FROM ".DB_PREFIX."_Events_ChangeTeam WHERE playerId=".mysql_escape_string($player)."");
	$result = mysql_fetch_assoc($queryTjoins);
	$numteamjoins = $result['tj'];

	$query = mysql_query("
		SELECT
			IFNULL(".DB_PREFIX."_Teams.name, ".DB_PREFIX."_Events_ChangeTeam.team) AS name,
			COUNT(".DB_PREFIX."_Events_ChangeTeam.id) AS teamcount,
			COUNT(".DB_PREFIX."_Events_ChangeTeam.id) / $numteamjoins * 100 AS percent
		FROM
			".DB_PREFIX."_Events_ChangeTeam
		LEFT JOIN ".DB_PREFIX."_Teams ON
			".DB_PREFIX."_Events_ChangeTeam.team=".DB_PREFIX."_Teams.code
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_ChangeTeam.serverId
		WHERE
			".DB_PREFIX."_Teams.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Events_ChangeTeam.playerId=".mysql_escape_string($player)."
		AND (hidden <>'1' OR hidden IS NULL)
		GROUP BY
			".DB_PREFIX."_Events_ChangeTeam.team
		ORDER BY
			".$tblTeams->sort." ".$tblTeams->sortorder.",
			".$tblTeams->sort2." ".$tblTeams->sortorder."
	");

	$numitems = mysql_num_rows($query);

	if ($numitems > 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="teams"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Team Selection'); ?></b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblTeams->draw($query, $numitems, 100);
	?>
	</div>
	</td>
</tr>
</table><p>
<?php
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
	$tblWeapons = new Table(
		array(
			new TableColumn(
				"weapon",
				"Weapon",
				"width=21&type=weaponimg&align=center&link=" . urlencode("mode=weaponinfo&weapon=%k&game=$game")
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
		"weap_sortorder",
		"weapons"
	);

	$query = mysql_query("
		SELECT
			".DB_PREFIX."_Events_Frags.weapon,
			".DB_PREFIX."_Weapons.name,
			IFNULL(".DB_PREFIX."_Weapons.modifier, 1.00) AS modifier,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) AS kills,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) / ".mysql_escape_string($realkills)." * 100 AS percent
		FROM
			".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Weapons ON
			".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Frags.weapon
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
		WHERE
			".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."' AND ".DB_PREFIX."_Events_Frags.killerId=$player
			AND (".DB_PREFIX."_Weapons.game='".mysql_escape_string($game)."' OR ".DB_PREFIX."_Weapons.weaponId IS NULL)
		GROUP BY
			".DB_PREFIX."_Events_Frags.weapon
		ORDER BY
			".$tblWeapons->sort." ".$tblWeapons->sortorder.",
			".$tblWeapons->sort2." ".$tblWeapons->sortorder."
	");
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="weapons"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Weapon Usage'); ?></b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<?php
			$tblWeapons->draw($query, mysql_num_rows($query), 100);
		?>
	</div>
	</td>
</tr>
</table><p>
<?php
	flush();
	$tblWeaponstats = new Table(
		array(
			new TableColumn(
				"smweapon",
				"Weapon",
				"width=21&type=weaponimg&align=center&link=" . urlencode("mode=weaponinfo&weapon=%k&game=$game")
			),
			new TableColumn(
				"smshots",
				"Shots",
				"width=7&align=right"
			),
			new TableColumn(
				"smhits",
				"Hits",
				"width=7&align=right"
			),
			new TableColumn(
				"smdamage",
				"Damage",
				"width=8&align=right"
			),
			new TableColumn(
				"smheadshots",
				"Head Shots",
				"width=6&align=right"
			),
			new TableColumn(
				"smkills",
				"Kills",
				"width=7&align=right"
			),
			new TableColumn(
				"smdeaths",
				"Deaths",
				"width=7&align=right"
			),
			new TableColumn(
				"smkdr",
				"Kills per Death",
				"width=8&align=right"
			),
			new TableColumn(
				"smaccuracy",
				"Accuracy",
				"width=9&align=right&append=" . urlencode("%")
			),
			new TableColumn(
				"smdhr",
				"Damage per Hit",
				"width=7&align=right"
			),
			new TableColumn(
				"smspk",
				"Shots per Kill",
				"width=8&align=right"
			)
		),
		"smweapon",
		"smkdr",
		"smweapon",
		true,
		9999,
		"weap_page",
		"weap_sort",
		"weap_sortorder",
		"weaponstats"
	);

	$query = mysql_query("
		SELECT
			".DB_PREFIX."_Events_Statsme.weapon AS smweapon,
			".DB_PREFIX."_Weapons.name,
			SUM(".DB_PREFIX."_Events_Statsme.kills) AS smkills,
			SUM(".DB_PREFIX."_Events_Statsme.hits) AS smhits,
			SUM(".DB_PREFIX."_Events_Statsme.shots) AS smshots,
			SUM(".DB_PREFIX."_Events_Statsme.headshots) AS smheadshots,
			SUM(".DB_PREFIX."_Events_Statsme.deaths) AS smdeaths,
			SUM(".DB_PREFIX."_Events_Statsme.damage) AS smdamage,
			IFNULL((ROUND((SUM(".DB_PREFIX."_Events_Statsme.damage) / SUM(".DB_PREFIX."_Events_Statsme.hits)), 1)), '-') as smdhr,
			SUM(".DB_PREFIX."_Events_Statsme.kills) / IF((SUM(".DB_PREFIX."_Events_Statsme.deaths)=0), 1, (SUM(".DB_PREFIX."_Events_Statsme.deaths))) as smkdr,
			ROUND((SUM(".DB_PREFIX."_Events_Statsme.hits) / SUM(".DB_PREFIX."_Events_Statsme.shots) * 100), 1) as smaccuracy,
			IFNULL((ROUND((SUM(".DB_PREFIX."_Events_Statsme.shots) / SUM(".DB_PREFIX."_Events_Statsme.kills)), 1)), '-') as smspk
		FROM
			".DB_PREFIX."_Events_Statsme
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Statsme.serverId
		LEFT JOIN ".DB_PREFIX."_Weapons ON
			".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Statsme.weapon
		WHERE
			".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Events_Statsme.PlayerId=".mysql_escape_string($player)."
		GROUP BY
			".DB_PREFIX."_Events_Statsme.weapon
		ORDER BY
			".$tblWeaponstats->sort." ".$tblWeaponstats->sortorder.",
			".$tblWeaponstats->sort2." ".$tblWeaponstats->sortorder."
	");

if (mysql_num_rows($query) != 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="weaponstats"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Weapon Stats'); ?></b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblWeaponstats->draw($query, mysql_num_rows($query), 100);
	?>
	</div></td>
</tr>
</table><p>
<?php
}
	flush();
	$tblWeaponstats2 = new Table(
		array(
			new TableColumn(
				"smweapon",
				"Weapon",
				"width=21&type=weaponimg&align=center&link=" . urlencode("mode=weaponinfo&weapon=%k&game=$game")
			),
			new TableColumn(
				"smhead",
				"Head",
				"width=7&align=right"
			),
			new TableColumn(
				"smchest",
				"Chest",
				"width=7&align=right"
			),
			new TableColumn(
				"smstomach",
				"Stomach",
				"width=7&align=right"
			),
			new TableColumn(
				"smleftarm",
				"Left Arm",
				"width=7&align=right"
			),
			new TableColumn(
				"smrightarm",
				"Right Arm",
				"width=7&align=right"
			),
			new TableColumn(
				"smleftleg",
				"Left Leg",
				"width=7&align=right"
			),
			new TableColumn(
				"smrightleg",
				"Right Leg",
				"width=7&align=right"
			),
			new TableColumn(
				"smleft",
				"Left",
				"width=8&align=right&append=" . urlencode("%")
			),
			new TableColumn(
				"smmiddle",
				"Middle",
				"width=9&align=right&append=" . urlencode("%")
			),
			new TableColumn(
				"smright",
				"Right",
				"width=8&align=right&append=" . urlencode("%")
			)
		),
		"smweapon",
		"smhead",
		"smweapon",
		true,
		9999,
		"weap_page",
		"weap_sort",
		"weap_sortorder",
		"weaponstats2"
	);

	$query = mysql_query("
		SELECT
			".DB_PREFIX."_Events_Statsme2.weapon AS smweapon,
			".DB_PREFIX."_Weapons.name,
			SUM(".DB_PREFIX."_Events_Statsme2.head) AS smhead,
			SUM(".DB_PREFIX."_Events_Statsme2.chest) AS smchest,
			SUM(".DB_PREFIX."_Events_Statsme2.stomach) AS smstomach,
			SUM(".DB_PREFIX."_Events_Statsme2.leftarm) AS smleftarm,
			SUM(".DB_PREFIX."_Events_Statsme2.rightarm) AS smrightarm,
			SUM(".DB_PREFIX."_Events_Statsme2.leftleg) AS smleftleg,
			SUM(".DB_PREFIX."_Events_Statsme2.rightleg) AS smrightleg,
			IFNULL(ROUND((SUM(".DB_PREFIX."_Events_Statsme2.leftarm) + SUM(".DB_PREFIX."_Events_Statsme2.leftleg)) / (SUM(".DB_PREFIX."_Events_Statsme2.head) + SUM(".DB_PREFIX."_Events_Statsme2.chest) + SUM(".DB_PREFIX."_Events_Statsme2.stomach) + SUM(".DB_PREFIX."_Events_Statsme2.leftarm ) + SUM(".DB_PREFIX."_Events_Statsme2.rightarm) + SUM(".DB_PREFIX."_Events_Statsme2.leftleg) + SUM(".DB_PREFIX."_Events_Statsme2.rightleg)) * 100, 1), 0.0) AS smleft,
			IFNULL(ROUND((SUM(".DB_PREFIX."_Events_Statsme2.rightarm) + SUM(".DB_PREFIX."_Events_Statsme2.rightleg)) / (SUM(".DB_PREFIX."_Events_Statsme2.head) + SUM(".DB_PREFIX."_Events_Statsme2.chest) + SUM(".DB_PREFIX."_Events_Statsme2.stomach) + SUM(".DB_PREFIX."_Events_Statsme2.leftarm ) + SUM(".DB_PREFIX."_Events_Statsme2.rightarm) + SUM(".DB_PREFIX."_Events_Statsme2.leftleg) + SUM(".DB_PREFIX."_Events_Statsme2.rightleg)) * 100, 1), 0.0) AS smright,
			IFNULL(ROUND((SUM(".DB_PREFIX."_Events_Statsme2.head) + SUM(".DB_PREFIX."_Events_Statsme2.chest) + SUM(".DB_PREFIX."_Events_Statsme2.stomach)) / (SUM(".DB_PREFIX."_Events_Statsme2.head) + SUM(".DB_PREFIX."_Events_Statsme2.chest) + SUM(".DB_PREFIX."_Events_Statsme2.stomach) + SUM(".DB_PREFIX."_Events_Statsme2.leftarm ) + SUM(".DB_PREFIX."_Events_Statsme2.rightarm) + SUM(".DB_PREFIX."_Events_Statsme2.leftleg) + SUM(".DB_PREFIX."_Events_Statsme2.rightleg)) * 100, 1), 0.0) AS smmiddle
		FROM
			".DB_PREFIX."_Events_Statsme2
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Statsme2.serverId
		LEFT JOIN ".DB_PREFIX."_Weapons ON
			".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Statsme2.weapon
		WHERE
			".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'
			AND ".DB_PREFIX."_Events_Statsme2.PlayerId=".mysql_escape_string($player)."
		GROUP BY
			".DB_PREFIX."_Events_Statsme2.weapon
		ORDER BY
			".$tblWeaponstats2->sort." ".$tblWeaponstats2->sortorder.",
			".$tblWeaponstats2->sort2." ".$tblWeaponstats2->sortorder."
	");

if (mysql_num_rows($query) != 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="weaponstats2"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Weapon Target'); ?></b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('Days'); ?>)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblWeaponstats2->draw($query, mysql_num_rows($query), 100);
	?>
	</div>
	</td>
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
