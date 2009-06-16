<?php
/**
 * $Id: players.inc.php 611 2008-10-29 12:49:03Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/players.inc.php $
 *
 * Original development:
 * +
 * + HLstats - Real-time player and clan rankings and statistics for Half-Life
 * + http://sourceforge.net/projects/hlstats/
 * +
 * + Copyright (C) 2001  Simon Garner
 * +
 *
 * Additional development:
 * +
 * + UA HLstats Team
 * + http://www.unitedadmins.com
 * + 2004 - 2007
 * +
 *
 *
 * Current development:
 * +
 * + Johannes 'Banana' Keßler
 * + http://hlstats.sourceforge.net
 * + 2007 - 2008
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



	// Player Rankings
	$game = sanitize($_GET['game']);

	$db->query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='$game'");
	if ($db->num_rows() < 1) error("No such game '$game'.");

	list($gamename) = $db->fetch_row();
	$db->free_result();

	if (isset($_GET["minkills"])) {
		$minkills = sanitize($_GET["minkills"]);
	}
	else {
		$minkills = 1;
	}

	pageHeader(
		array($gamename, "Player Rankings"),
		array($gamename=>"%s?game=$game", "Player Rankings"=>"")
	);
?>

<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="game" value="<?php echo $game; ?>">
<input type="hidden" name="st" value="player">
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">
<tr valign="bottom">
	<td width="75%"><?php echo $g_options["font_normal"]; ?><b>&#149;</b> Find a player: <input type="text" name="q" size=20 maxlength=64 class="textbox"> <input type="submit" value="Search" class="smallsubmit"><?php echo $g_options["fontend_normal"]; ?></td>
	<td width="25%" align="right" nowrap><?php echo $g_options["font_normal"]; ?>Go to <a href="<?php echo $g_options["scripturl"] . "?mode=clans&amp;game=$game"; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/clan.gif" width="16" height="16" hspace="3" border="0" align="middle" alt="clan.gif">Clan Rankings</a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table>
</form>
<?php
	// @todo: select game server ...
	$query = "SELECT COUNT(*) as serverCount FROM `".DB_PREFIX."_Servers`
				WHERE `game` = '".$game."'";
	$query = $db->query($query);
	$res = $db->fetch_array($query);
	if($res['serverCount'] > 1) {
		// ok get all the server data
		$query = "SELECT * FROM `".DB_PREFIX."_Servers`
				WHERE `game` = '".$game."'";
		$query = $db->query($query);
		while ($result = $db->fetch_array($query)) {
			$serverArr[] = $result;
		}
?>
<form method="post" action="">
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">
	<tr valign="bottom">
		<td width="100%">
			<?php echo $g_options["font_normal"]; ?>
				<b>&#149;</b> Select a server:
				<select name="chooseServer">
					<option value="all">All</option>
					<?php
					foreach($serverArr as $serverEntry) {
						if($_POST['chooseServer'] == $serverEntry['serverId']) $sel = 'selected="selected"';
						echo '<option value="'.$serverEntry['serverId'].'" '.$sel.'>'.$serverEntry['name'].' ('.$serverEntry['address'].' '.$serverEntry['port'].')</option>';
						$sel = "";
					}
					?>
				</select>
				<input type="submit" value="Show" name="selectServer" class="smallsubmit">
			<?php echo $g_options["fontend_normal"]; ?>
		</td>
	</tr>
</table>
</form>
<?php
	}
?>
<br /><br />
<?php
	// we have selected a server
	$serverPlayersStr = false;
	if(isset($_POST['selectServer'])) {
		if($_POST['chooseServer'] != "all") {
			// we can get the players from the events_connects
			$query = "SELECT `playerId` FROM `".DB_PREFIX."_Events_Connects` WHERE `serverId` = '".$_POST['chooseServer']."'";
			$query = $db->query($query);
			while($result = $db->fetch_array($query)) {
				$tmp[] = $result['playerId'];
			}
			if(count($tmp)) {
				$serverPlayersStr = implode(",",$tmp);
			}
		}
	}

    // if so show a timeline of player count
    if($g_options['useFlash'] == "1") {

    	// we use flash
    	echo '<script type="text/javascript" src="'.INCLUDE_PATH.'/amcharts/swfobject.js"></script>';

        // get the player history
        // // we need first all players for this game
        $query = "SELECT `playerId` FROM `".DB_PREFIX."_Players` WHERE `game` = '".$game."'";

        // we have a limit to the players
		if($serverPlayersStr !== false) {
			$query .= " AND `playerId` IN (".$serverPlayersStr.")";
		}

        $query = $db->query($query);
        while($result = $db->fetch_array($query)) {
            $players[] = $result['playerId'];
        }
        if(count($players) > 0) {
        	$playersStr = implode(",",$players);

	        // get the connects
	        $query = $db->query("SELECT `eventTime`,`playerId`
	                            FROM `".DB_PREFIX."_Events_Connects`
	                            WHERE `playerId` IN (".$playersStr.")");
	        while ($result = $db->fetch_array($query)) {
	            // we group by day
	            $dataArr = explode(" ",$result['eventTime']);
	        	$playersArr[$dataArr[0]][] = $result['playerId'];
	        }

	        // get the disconnects
	        $query = $db->query("SELECT `eventTime`,`playerId`
	                            FROM `".DB_PREFIX."_Events_Disconnects`
	                            WHERE `playerId` IN (".$playersStr.")");
	        while ($result = $db->fetch_array($query)) {
	            // we group by day
	            $dataArr = explode(" ",$result['eventTime']);
	        	$playersDisconnectArr[$dataArr[0]][] = $result['playerId'];
	        }

	        // create the xml data for the flash
	        $timeLineData['xml'] = "<?xml version='1.0' encoding='UTF-8'?>";
	        $timeLineData['xml'] .= "<chart>";

	        // first we create the data for x
	        $timeLineData['xml'] .= "<series>";
	        foreach ($playersArr as $day=>$val) {
	        	$timeLineData['xml'] .= "<value xid='".$day."'>".$day."</value>";
	        }
	        $timeLineData['xml'] .= "</series>";

	        // now we create the graphs
	        // first the connects
	        $timeLineData['xml'] .= "<graphs>";
	        $timeLineData['xml'] .= "<graph gid='0' title='Connects' color='#990000' line_width='2'>";
	        foreach ($playersArr as $day=>$val) {
	            $cCount = count($val);
	        	$timeLineData['xml'] .= "<value xid='".$day."'>".$cCount."</value>";
	        }
	        $timeLineData['xml'] .= "</graph>";

	        // the disconnects
	        $timeLineData['xml'] .= "<graph gid='1' title='Disconnects' fill_alpha='10' line_alpha='30' color='#FF9900'>";
	        foreach ($playersDisconnectArr as $day=>$val) {
	            $dCount = count($val);
	        	$timeLineData['xml'] .= "<value xid='".$day."'>".$dCount."</value>";
	        }
	        $timeLineData['xml'] .= "</graph>";
	        $timeLineData['xml'] .= "</graphs>";

	        $timeLineData['xml'] .= "</chart>";

	        $timeLineData['height'] = 250;

	        ?>
	        <table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">
	            <tr>
	                <td bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
	                   <?php echo $g_options["font_normal"]; ?><b>Player Activity</b><?php echo $g_options["fontend_normal"];?>
	                </td>
	            </tr>
	            <tr valign="bottom">
	            	<td align="left">
	            		<div style="text-align: center; display: block;" id="flash_timeline">
		                    <div id="playerTimeline">
		        				<?php echo $g_options["font_normal"]; ?>
		        				<b>You need to upgrade your flash player</b><br />
		        				<a href="http://www.adobe.com/go/getflashplayer" target="_blank">Get Flashplayer</a>
		        				<?php echo $g_options["fontend_normal"];?>
		        			</div>
		        			<script type="text/javascript">
		        				// <![CDATA]
		        				var so = new SWFObject("<?php echo INCLUDE_PATH; ?>/amcharts/line/amline.swf?<?php echo time(); ?>", "playerTimeline", "600", "<?php echo $timeLineData["height"]; ?>", "8", "<?php echo $g_options['body_bgcolor']; ?>");
		        				so.addVariable("path", "<?php echo INCLUDE_PATH; ?>/amcharts/line/");
		        				so.addVariable("settings_file", escape("<?php echo INCLUDE_PATH; ?>/amcharts/line/settings.xml"));
		        				so.addVariable("chart_data", "<?php echo $timeLineData['xml']; ?>");
		        				so.addVariable("additional_chart_settings", "<settings><text_color><?php echo $g_options["body_text"]; ?></text_color></settings>");
		        				so.addVariable("preloader_color", "<?php echo $g_options["body_text"]; ?>");
		        				so.write("playerTimeline");
		        				// ]]
		        			</script>
		        		</div>
	        		</td>
	        	</tr>
	    	</table>
	    	<p>&nbsp;</p>
	        <?php
    	}

    	// most time online
    	$query = "SELECT
				".DB_PREFIX."_Events_StatsmeTime.*,
				TIME_TO_SEC(".DB_PREFIX."_Events_StatsmeTime.time) as tTime
			FROM
				".DB_PREFIX."_Events_StatsmeTime
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_StatsmeTime.serverId
			WHERE
				".DB_PREFIX."_Servers.game='$game'";

    	// we have a limit to the players
		if($serverPlayersStr !== false) {
			$query .= " AND ".DB_PREFIX."_Servers.serverId = '".$_POST['chooseServer']."'";
		}

		$query = $db->query($query);
		while($result = $db->fetch_array($query)) {
			$onlineArr[$result['playerId']][] = $result;
		}

		if(count($onlineArr) > 1) {
			// summ the time up for each player
			foreach ($onlineArr as $pId=>$pOnline) {
				foreach ($pOnline as $eventData) {
					$eventsGruped[$pId] += $eventData['tTime'];
				}
			}
			arsort($eventsGruped);
			// now use only the top 5
			$topPlayers = array_slice($eventsGruped,0,5,true);
			// now get the player names
			foreach ($topPlayers as $pId=>$tP) {
				$query = "SELECT `lastName` FROM `".DB_PREFIX."_Players` WHERE `playerId` = '".$pId."'";
				$query = $db->query($query);
				$result = $db->fetch_array($query);
				$topPlayersArr[$pId]['time'] = $tP;
				$topPlayersArr[$pId]['name'] = $result['lastName'];
			}

			// build the graph data
			$timeLineData['xml'] = "<?xml version='1.0' encoding='UTF-8'?>";
	        $timeLineData['xml'] .= "<chart>";

	        // first we create the data for x
	        $timeLineData['xml'] .= "<series>";
	        foreach ($topPlayersArr as $pId=>$val) {
	        	$timeLineData['xml'] .= "<value xid='".makeXMLSave($val['name'])."'>".makeXMLSave($val['name'])."</value>";
	        }
	        $timeLineData['xml'] .= "</series>";

	        // now we create the graphs
	        $timeLineData['xml'] .= "<graphs>";

	        foreach ($topPlayersArr as $pId=>$val) {
	        	$timeLineData['xml'] .= "<graph gid='".makeXMLSave($val['name'])."' title='".makeXMLSave($val['name'])."'>";

	        	$totalTime = $val['time'];
	        	if($totalTime >= 86400) { // days
	        		$days = intval($totalTime / 86400);

	        		$secondsLeft = $totalTime - ($days * 86400);
	        		$hours = intval($totalTime / 3600);

	            	$secondsLeft = $totalTime - ($hours * 3600);
	            	$minutes = intval($secondsLeft / 60);

	            	$seconds = $secondsLeft - ($minutes * 60);

	            	$value = $days."d ".$hours."h ".$minutes."m ".$seconds."s";
	        	}
	        	elseif($totalTime >= 3600) { // hours
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

	        	$timeLineData['xml'] .= "<value xid='".makeXMLSave($val['name'])."' description='".$value."' url='".urlencode('hlstats.php?mode=playerinfo&player='.$pId)."'>".$totalTime."</value>";
	        	$timeLineData['xml'] .= "</graph>";
	        }

	        $timeLineData['xml'] .= "</graphs>";

	        $timeLineData['xml'] .= "</chart>";

	        $timeLineData['height'] = 250;

	        ?>
	        <table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">
	            <tr>
	                <td bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
	                   <?php echo $g_options["font_normal"]; ?><b>Most time online</b> (hover over the bars to get more information and click to jump to the player details)<?php echo $g_options["fontend_normal"];?>
	                </td>
	            </tr>
	            <tr valign="bottom">
	            	<td align="left">
	            		<div style="text-align: center; display: block;" id="flash_timeline">
		                    <div id="playerOnlineTime">
		        				<?php echo $g_options["font_normal"]; ?>
		        				<b>You need to upgrade your flash player</b><br />
		        				<a href="http://www.adobe.com/go/getflashplayer" target="_blank">Get Flashplayer</a>
		        				<?php echo $g_options["fontend_normal"];?>
		        			</div>
		        			<script type="text/javascript">
		        				// <![CDATA]
		        				var so = new SWFObject("<?php echo INCLUDE_PATH; ?>/amcharts/column/amcolumn.swf?<?php echo time(); ?>", "playerOnlineTime", "600", "<?php echo $timeLineData["height"]; ?>", "8", "<?php echo $g_options['body_bgcolor']; ?>");
		        				so.addVariable("path", "<?php echo INCLUDE_PATH; ?>/amcharts/line/");
		        				so.addVariable("settings_file", escape("<?php echo INCLUDE_PATH; ?>/amcharts/column/settings_mostTimeOnline.xml"));
		        				so.addVariable("chart_data", "<?php echo $timeLineData['xml']; ?>");
		        				so.addVariable("additional_chart_settings", "<settings><text_color><?php echo $g_options["body_text"]; ?></text_color></settings>");
		        				so.addVariable("preloader_color", "<?php echo $g_options["body_text"]; ?>");
		        				so.write("playerOnlineTime");
		        				// ]]
		        			</script>
		        		</div>
	        		</td>
	        	</tr>
	    	</table>
	    	<p>&nbsp;</p>
	        <?php
		}
    }

    // the players table
	$table = new Table(
		array(
			new TableColumn(
				"lastName",
				"Name",
				"width=46&icon=player&link=" . urlencode("mode=playerinfo&amp;player=%k")
			),
			new TableColumn(
				"skill",
				"Points",
				"width=11&align=right"
			),
			new TableColumn(
				"kills",
				"Kills",
				"width=11&align=right"
			),
			new TableColumn(
				"deaths",
				"Deaths",
				"width=11&align=right"
			),
			new TableColumn(
				"kpd",
				"Kills per Death",
				"width=11&align=right"
			),
			new TableColumn(
				"playerId",
				"ID",
				"width=5&align=right&sort=no"
			)
		),
		"playerId",
		"skill",
		"kpd",
		true
	);


	if(defined('HIDE_BOTS') && HIDE_BOTS == "1") {
    	$query = "SELECT
    			t1.playerId,
    			lastName,
    			oldSkill,
    			skill,
    			kills,
    			deaths,
    			IFNULL(kills/deaths, '-') AS kpd
    		FROM
    			".DB_PREFIX."_Players as t1 INNER JOIN ".DB_PREFIX."_PlayerUniqueIds as t2
    			ON t1.playerID = t2.playerID
    		WHERE
    			t1.game='$game'
    			AND t1.hideranking=0
    			AND t1.kills >= $minkills
    			AND t2.uniqueID not like 'BOT:%'";

    	if($serverPlayersStr !== false) {
    		// we have selected a server and have players for this server
    		$query .= " AND t1.playerId IN (".$serverPlayersStr.")";
    	}

    	$query .= " ORDER BY
    			$table->sort $table->sortorder,
    			$table->sort2 $table->sortorder,
    			lastName ASC
    		LIMIT $table->startitem,$table->numperpage
    	";

	}
	else
	{
		$query = "SELECT
				playerId,
				lastName,
				skill,
				oldSkill,
				kills,
				deaths,
				IFNULL(kills/deaths, '-') AS kpd
			FROM
				".DB_PREFIX."_Players
			WHERE
				game='$game'
				AND hideranking=0
				AND kills >= $minkills ";

		if($serverPlayersStr !== false) {
    		// we have selected a server and have players for this server
    		$query .= " AND playerId IN (".$serverPlayersStr.")";
    	}

		$query .="
			ORDER BY
				$table->sort $table->sortorder,
				$table->sort2 $table->sortorder,
				lastName ASC
			LIMIT $table->startitem,$table->numperpage
		";
	}
	$result = $db->query($query);

	$query = "SELECT COUNT(*) FROM `".DB_PREFIX."_Players`
				WHERE game='".$game."'
					AND hideranking=0
					AND kills >= $minkills";
	if($serverPlayersStr !== false) {
		$query .= " AND `playerId` IN (".$serverPlayersStr.")";
	}
	$resultCount = $db->query($query);

	list($numitems) = $db->fetch_row($resultCount);

	$table->draw($result, $numitems, 90);
?><p>

<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">

<tr valign="bottom">
	<td width="75%"><?php echo $g_options["font_normal"]; ?>
<?php
	foreach ($_GET as $k=>$v)
	{
		if ($k != "minkills") {
			echo "		<input type=\"hidden\" name=\"$k\" value=\"" . htmlspecialchars($v) . "\">\n";
		}
	}
?>
		<b>&#149;</b> Only show players with <input type="text" name="minkills" size=4 maxlength=2 value="<?php echo $minkills; ?>" class="textbox"> or more kills. <input type="submit" value="Apply" class="smallsubmit"><?php echo $g_options["fontend_normal"]; ?> </td>
</tr>

</table>
</form>
