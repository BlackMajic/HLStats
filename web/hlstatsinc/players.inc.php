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
 * + 2007 - 2009
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

$minkills = 1;
if (isset($_GET["minkills"])) {
	$check = validateInput($_GET['minkills'],'digit');
	if($check === true) {
		$minkills = $_GET["minkills"];
	}
}

// the rating system
$rdlimit = 100;
if (isset($_GET["rdlimit"])) {
	$check = validateInput($_GET['rdlimit'],'digit');
	$rdlimit = $_GET["rdlimit"];
}
$rd2limit = $rdlimit * $rdlimit;

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
    // if so show a timeline of player count
    if($g_options['useFlash'] == "1") {

    	// we use flash
    	echo '<script type="text/javascript" src="'.INCLUDE_PATH.'/amcharts/swfobject.js"></script>';

        // get the connects
        $query = mysql_query("SELECT DATE_FORMAT(`".DB_PREFIX."_Events_Connects`.`eventTime`,'%Y-%m-%d') AS eventTime,
        				`".DB_PREFIX."_Events_Connects`.`playerId`
						FROM `".DB_PREFIX."_Events_Connects`
						LEFT JOIN `".DB_PREFIX."_Players`
							ON `".DB_PREFIX."_Events_Connects`.`playerId` = `".DB_PREFIX."_Players`.`playerId`
						WHERE `".DB_PREFIX."_Players`.`game` = '".mysql_escape_string($game)."'");

        while ($result = mysql_fetch_assoc($query)) {
            // we group by day
            //$dataArr = explode(" ",$result['eventTime']);
        	$playersArr[$result['eventTime']][] = $result['playerId'];
        }
        mysql_free_result($query);

		//unset($dataArr);
        // get the disconnects
		$query = mysql_query("SELECT DATE_FORMAT(`".DB_PREFIX."_Events_Disconnects`.`eventTime`,'%Y-%m-%d') AS eventTime,
						`".DB_PREFIX."_Events_Disconnects`.`playerId`
		                FROM `".DB_PREFIX."_Events_Disconnects`
		                LEFT JOIN `".DB_PREFIX."_Players`
		                	ON `".DB_PREFIX."_Events_Disconnects`.`playerId` = `".DB_PREFIX."_Players`.`playerId`
		                WHERE `".DB_PREFIX."_Players`.`game` = '".mysql_escape_string($game)."'");
        while ($result = mysql_fetch_assoc($query)) {
            // we group by day
            //$dataArr = explode(" ",$result['eventTime']);
        	$playersDisconnectArr[$result['eventTime']][] = $result['playerId'];
        }
        mysql_free_result($query);
        //unset($dataArr);

		if(!empty($playersArr) && !empty($playersDisconnectArr)) {

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
    	$query = mysql_query("SELECT ".DB_PREFIX."_Events_StatsmeTime.*,
					TIME_TO_SEC(".DB_PREFIX."_Events_StatsmeTime.time) as tTime
					FROM ".DB_PREFIX."_Events_StatsmeTime
					LEFT JOIN ".DB_PREFIX."_Servers
						ON ".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_StatsmeTime.serverId
					WHERE ".DB_PREFIX."_Servers.game='".mysql_escape_string($game)."'");

		while($result = mysql_fetch_assoc($query)) {
			$onlineArr[$result['playerId']][] = $result;
		}
		mysql_free_result($query);

		if(!empty($onlineArr)) {
			// summ the time up for each player
			$eventsGruped = array();
			foreach ($onlineArr as $pId=>$pOnline) {
				foreach ($pOnline as $eventData) {
					if(empty($eventsGruped[$pId])) $eventsGruped[$pId] = '';
					$eventsGruped[$pId] += $eventData['tTime'];
				}
			}
			arsort($eventsGruped);
			// now use only the top 5
			$topPlayers = array_slice($eventsGruped,0,5,true);
			// now get the player names
			foreach ($topPlayers as $pId=>$tP) {
				$query = mysql_query("SELECT `lastName` FROM `".DB_PREFIX."_Players` WHERE `playerId` = '".$pId."'");
				$result = mysql_fetch_assoc($query);
				$topPlayersArr[$pId]['time'] = $tP;
				$topPlayersArr[$pId]['name'] = $result['lastName'];
				mysql_free_result($query);
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

	// we want the elo rating system
	if(defined('ELORATING') && (ELORATING === "1")) {
		// the players table
		$table = new Table(
			array(
				new TableColumn(
					"lastName",
					"Name",
					"width=31&icon=player&link=" . urlencode("mode=playerinfo&amp;player=%k")
				),
				new TableColumn(
					"skill",
					"Points",
					"width=11&align=right"
				),
				new TableColumn(
					"rating",
					"Rating",
					"width=10&align=right"
				),
				new TableColumn(
					"rd",
					"RD",
					"width=8&align=right"
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
	    			ROUND(rating) as rating,
	    			ROUND(SQRT(rd2)) as rd,
	    			kills,
	    			deaths,
	    			IFNULL(kills/deaths, '-') AS kpd
	    		FROM
	    			".DB_PREFIX."_Players as t1
	    			INNER JOIN ".DB_PREFIX."_PlayerUniqueIds as t2 ON t1.playerId = t2.playerId
	    		WHERE
	    			t1.game='$game'
	    			AND t1.hideranking=0
	    			AND t1.rd2 <= $rd2limit
	    			AND t2.uniqueID not like 'BOT:%'
	    		ORDER BY
	    			$table->sort $table->sortorder,
	    			$table->sort2 $table->sortorder,
	    			lastName ASC
	    		LIMIT $table->startitem,$table->numperpage
	    	";

		}
		else {
			$query = "SELECT
					t1.playerId,
					t1.lastName,
					t1.skill,
					t1.oldSkill,
					ROUND(rating) as rating,
					ROUND(SQRT(rd2)) as rd,
					t1.kills,
					t1.deaths,
					IFNULL(t1.kills/t1.deaths, '-') AS kpd
				FROM
					".DB_PREFIX."_Players as t1
				WHERE
					t1.game='$game'
					AND t1.hideranking=0
					AND rd2 <= $rd2limit
				GROUP BY t1.playerId
				ORDER BY
					$table->sort $table->sortorder,
					$table->sort2 $table->sortorder,
					lastName ASC
				LIMIT $table->startitem,$table->numperpage
			";

		}
		$result = mysql_query($query);

		$query = mysql_query("SELECT COUNT(*) AS pc
					FROM `".DB_PREFIX."_Players`
					WHERE game='".$game."'
						AND hideranking=0
						AND rd2 <= $rd2limit");
		$resultCount = mysql_fetch_assoc($query);

		$numitems = $resultCount['pc'];
	}
	elseif(defined('ELORATING') && (ELORATING === "2")) {
		// we want only the rating system
	}
	else {
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
	    	$query = mysql_query("SELECT
	    			t1.playerId,
	    			lastName,
	    			oldSkill,
	    			skill,
	    			kills,
	    			deaths,
	    			IFNULL(kills/deaths, '-') AS kpd
	    		FROM
	    			".DB_PREFIX."_Players as t1
	    			INNER JOIN ".DB_PREFIX."_PlayerUniqueIds as t2 ON t1.playerId = t2.playerId
	    		WHERE
	    			t1.game='$game'
	    			AND t1.hideranking=0
	    			AND t1.kills >= $minkills
	    			AND t2.uniqueID not like 'BOT:%'
	    		ORDER BY
	    			$table->sort $table->sortorder,
	    			$table->sort2 $table->sortorder,
	    			lastName ASC
	    		LIMIT $table->startitem,$table->numperpage");

		}
		else {
			$queryPlayers = mysql_query("SELECT
					t1.playerId,
					t1.lastName,
					t1.skill,
					t1.oldSkill,
					t1.kills,
					t1.deaths,
					IFNULL(t1.kills/t1.deaths, '-') AS kpd
				FROM
					".DB_PREFIX."_Players as t1
				WHERE
					t1.game='".mysql_escape_string($game)."'
					AND t1.hideranking=0
					AND t1.kills >= '".mysql_escape_string($minkills)."'
				GROUP BY t1.playerId
				ORDER BY
					".$table->sort." ".$table->sortorder.",
					".$table->sort2." ".$table->sortorder.",
					lastName ASC
				LIMIT ".$table->startitem.",".$table->numperpage."");

		}

		$query = mysql_query("SELECT COUNT(*) as pc
					FROM `".DB_PREFIX."_Players`
					WHERE game='".mysql_escape_string($game)."'
						AND hideranking=0
						AND kills >= ".mysql_escape_string($minkills)."");
		$resultCount = mysql_fetch_assoc($query);
		$numitems = $resultCount['pc'];
	}
	// output
	$table->draw($queryPlayers, $numitems, 90);
?>
<p>
<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
<input type="hidden" name="game" value="<?php echo $game; ?>" />
<input type="hidden" name="mode" value="players" />
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">
	<tr valign="bottom">
		<td width="75%"><?php echo $g_options["font_normal"]; ?>
			<b>&#149;</b>
			<?php if (defined('ELORATING') && (ELORATING === "1" || ELORATING === "2")) { ?>
				Don't show players with an RD higher than <input type="text" name="rdlimit" size=4 maxlength=3 value="<?php echo $rdlimit; ?>" class="textbox"> of 350. <input type="submit" value="Apply" class="smallsubmit"> (lower RD = more accurate rating)
			<?php } else { ?>
				Only show players with <input type="text" name="minkills" size=4 maxlength=2 value="<?php echo $minkills; ?>" class="textbox"> or more kills. <input type="submit" value="Apply" class="smallsubmit">
			<?php }
				echo $g_options["fontend_normal"];
			?>
		</td>
	</tr>
</table>
</form>
</p>
