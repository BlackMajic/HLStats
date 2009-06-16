<?php
/**
 * $Id: playerinfo.inc.php 625 2008-11-11 10:01:09Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/playerinfo.inc.php $
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

	// Player Details

	$player = sanitize($_GET["player"]);
	$uniqueid  = sanitize($_GET["uniqueid"]);
	$game = sanitize($_GET["game"]);
	$killLimit = sanitize($_GET['killLimit']);

	// set the default kill limit
	if($killLimit == "") {
		$killLimit = 5;
	}

	if (!$player && $uniqueid) {
		if (!$game) {
			header("Location: " . $g_options["scripturl"] . "?mode=search&st=uniqueid&q=$uniqueid");
			exit;
		}

		$db->query("
			SELECT
				playerId
			FROM
				".DB_PREFIX."_PlayerUniqueIds
			WHERE
				uniqueId='$uniqueid'
				AND game='$game'
		");

		if ($db->num_rows() > 1) {
			header("Location: " . $g_options["scripturl"] . "?mode=search&st=uniqueid&q=$uniqueid&game=$game");
			exit;
		}
		elseif ($db->num_rows() < 1) {
			error("No players found matching uniqueId '$uniqueid'");
		}
		else {
			list($player) = $db->fetch_row();
			$player = intval($player);
		}
	}
	elseif (!$player && !$uniqueid) {
		error("No player ID specified.");
	}

	$db->query("
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
			playerId='$player'
	");
	if ($db->num_rows() != 1)
		error("No such player '$player'.");

	$playerdata = $db->fetch_array();
	$db->free_result();

	$pl_name = $playerdata["lastName"];
	if (strlen($pl_name) > 10) {
		$pl_shortname = substr($pl_name, 0, 8) . "...";
	}
	else {
		$pl_shortname = $pl_name;
	}
	$pl_name = ereg_replace(" ", "&nbsp;", htmlspecialchars($pl_name));
	$pl_shortname = ereg_replace(" ", "&nbsp;", htmlspecialchars($pl_shortname));
	$pl_urlname = urlencode($playerdata["lastName"]);


	$game = $playerdata["game"];
	$db->query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='$game'");
	if ($db->num_rows() != 1) {
		$gamename = ucfirst($game);
	}
	else {
		list($gamename) = $db->fetch_row();
	}

    // show header
	pageHeader(
		array($gamename, "Player Details", $pl_name),
		array(
			$gamename=>$g_options["scripturl"] . "?game=$game",
			"Player Rankings"=>$g_options["scripturl"] . "?mode=players&game=$game",
			"Player Details"=>""
		),
		$pl_name
	);

	if($g_options['useFlash'] == "1") {
	    // we want use the flash graphics
?>
    <script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/amcharts/swfobject.js"></script>
<?php
	}
?>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="60%" colspan="2"><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Player Profile</b><?php echo $g_options["fontend_normal"];?></td>
	<td width="40%" colspan="2"><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Statistics Summary</b><?php echo $g_options["fontend_normal"];?></td>
</tr>
<tr valign="top">
	<td width="5%">&nbsp;</td>
	<td width="50%">&nbsp;<br>
		<table width="95%" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $g_options["table_border"]; ?>">
    		<tr>
    			<td>
    				<table width="100%" border="0" cellspacing="1" cellpadding="4">
        				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
        					<td>
        					   <?php
                                echo $g_options["font_normal"];
                                echo "Member of Clan:";
                                echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						if ($playerdata["clan"]) {
        							echo "&nbsp;<a href=\"" . $g_options["scripturl"]
        								. "?mode=claninfo&clan=" . $playerdata["clan"]
        								. "\"><img src=\"" . $g_options["imgdir"]
        								. "/clan.gif\" width='16' height='16' hspace='4' "
        								. "border='0' align=\"middle\" alt=\"clan.gif\">"
        								. htmlspecialchars($playerdata["clan_name"]) . "</a>";
        						}
        						else {
        							echo "(None.)";
        						}
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        				</tr>
        				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo "Real Name:";
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						if ($playerdata["fullName"]) {
        							echo "<b>" . htmlspecialchars($playerdata["fullName"]) . "</b>";
        						}
        						else {
        							echo "(Unknown.)";
        						}
        						echo $g_options["fontend_normal"];
        					   ?>
                            </td>
        				</tr>
        				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo "E-mail Address:";
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						if ($email = getEmailLink($playerdata["email"])) {
        							echo $email;
        						}
        						else {
        							echo "(Unknown.)";
        						}
        						echo $g_options["fontend_normal"];
        					   ?>
                            </td>
        				</tr>
        				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo "Home Page:";
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						if ($url = getLink($playerdata["homepage"])) {
        							echo $url;
        						}
        						else {
        							echo "(Not specified.)";
        						}
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        				</tr>
        				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo "ICQ Number:";
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						if ($playerdata["icq"]) {
        							echo "<a href=\"http://www.icq.com/"
        								. urlencode($playerdata["icq"]) . "\" target=\"_blank\">"
        								. htmlspecialchars($playerdata["icq"]) . "</a>";
        						}
        						else {
        							echo "(Not specified.)";
        						}
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        				</tr>
        				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo "Player ID:";
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo $player;
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        				</tr>
        				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						if (MODE == "LAN") {
        							echo "IP Addresses:";
        						}
        						else {
        							echo "Unique IDs:";
        						}
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						if (MODE == "NameTrack") {
        						    echo "(Unknown.)";
        						}
        						else {
        							$db->query("
        								SELECT
        									uniqueId
        								FROM
        									".DB_PREFIX."_PlayerUniqueIds
        								WHERE
        									playerId='$player'
        							");

        							$i=0;
        							while (list($uqid) = $db->fetch_row()) {
        								if ($i > 0) echo ", ";
        								echo $uqid;
        								$i++;
        							}
        						}
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        				</tr>

        				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo "Last Connect:*";
        						echo $g_options["fontend_normal"];
        					   ?>
        					   </td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						$db->query("
        							SELECT
        								DATE_FORMAT(MAX(eventTime), '%r, %a. %D %b.')
        							FROM
        								".DB_PREFIX."_Events_Connects
        							LEFT JOIN ".DB_PREFIX."_Servers ON
        								".DB_PREFIX."_Servers.serverId = ".DB_PREFIX."_Events_Connects.serverId
        							WHERE
        								".DB_PREFIX."_Servers.game='$game' AND playerId='$player'
        						");
        						list($lastevent) = $db->fetch_row();

        						if ($lastevent) {
        							echo $lastevent;
        						}
        						else {
        							echo "(No info)";
        						}
        				        echo $g_options["fontend_normal"];
        				     ?>
        				   </td>
        				</tr>
        				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo "Total Connection Time:*";
        						echo $g_options["fontend_normal"];
        					   ?>
        					   </td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						$db->query("
        							SELECT
        								SEC_TO_TIME(SUM(TIME_TO_SEC(time))) AS tTime
        							FROM
        								".DB_PREFIX."_Events_StatsmeTime
        							LEFT JOIN ".DB_PREFIX."_Servers ON
        								".DB_PREFIX."_Servers.serverId = ".DB_PREFIX."_Events_StatsmeTime.serverId
        							WHERE
        								".DB_PREFIX."_Servers.game='$game' AND playerId='$player'
        						");
        						list($tTime) = $db->fetch_row();

        						if ($tTime) {
        							echo $tTime;
        						}
        						else {
        							echo "(No info)";
        						}
        				        echo $g_options["fontend_normal"];
        				        ?>
        				    </td>
        				</tr>

        				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						echo "Average Ping:*";
        						echo $g_options["fontend_normal"];
        					   ?>
        					</td>
        					<td>
        					   <?php
        						echo $g_options["font_normal"];
        						$db->query("
        							SELECT
        								ROUND(SUM(ping) /
        									COUNT(ping), 1) AS av_ping
        							FROM
        								".DB_PREFIX."_Events_StatsmeLatency
        							LEFT JOIN ".DB_PREFIX."_Servers ON
        								".DB_PREFIX."_Servers.serverId = ".DB_PREFIX."_Events_StatsmeLatency.serverId
        							WHERE
        								".DB_PREFIX."_Servers.game='$game' AND playerId='$player'
        						");
        						list($av_ping) = $db->fetch_row();

        						if ($av_ping) {
        							echo $av_ping;
        						}
        						else {
        							echo "(No info)";
        						}
        				        echo $g_options["fontend_normal"]; ?>
        				   </td>
        				</tr>
    				</table>
    			</td>
    		</tr>
		</table>
	</td>
	<td width="5%">&nbsp;</td>
	<td width="40%">&nbsp;<br>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $g_options["table_border"]; ?>">
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="1" cellpadding="4">

				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
					<td width="45%">
					   <?php
						echo $g_options["font_normal"];
						echo "Points:";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
					<td width="55%">
					   <?php
						echo $g_options["font_normal"];
						echo "<b>" . $playerdata["skill"] . "</b>";

						// check if we have a top or flop
						if($playerdata['skill'] > $playerdata['oldSkill']) {
							echo "<img src=\"" . $g_options["imgdir"]
							. "/skill_up.gif\" width='16' height='16' hspace='4' "
							. "border='0' align=\"middle\" alt=\"skill_up.gif\">";
						}
						elseif ($playerdata['skill'] < $playerdata['oldSkill']) {
							echo "<img src=\"" . $g_options["imgdir"]
							. "/skill_down.gif\" width='16' height='16' hspace='4' "
							. "border='0' align=\"middle\" alt=\"skill_down\">";
						}
						else {
							echo "<img src=\"" . $g_options["imgdir"]
							. "/skill_stay.gif\" width='16' height='16' hspace='4' "
							. "border='0' align=\"middle\" alt=\"skill_stay.gif\">";
						}

						echo $g_options["fontend_normal"];
					   ?>
					</td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
					<td width="45%">
					   <?php
						echo $g_options["font_normal"];
						echo "Rank:";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
					<td width="55%">
					   <?php
						echo $g_options["font_normal"];

						$db->query("
							SELECT
								skill,playerId
							FROM
								".DB_PREFIX."_Players
							WHERE
								game='$game'
							ORDER BY skill DESC
						");
						$ranKnum = 1;
						while ($row = $db->fetch_row()) {
							$statsArr[$row[1]] = $ranKnum;
							$ranKnum++;
						}
						echo "<b>" . $statsArr[$player] . "</b> (ordered by Skill)";

						echo $g_options["fontend_normal"];
					   ?>
					</td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
					<td width="45%">
					   <?php
						echo $g_options["font_normal"];
						echo "Kills:";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
					<td width="55%">
					   <?php
						echo $g_options["font_normal"];
						echo $playerdata["kills"];
						$db->query("
							SELECT
								COUNT(*)
							FROM
								".DB_PREFIX."_Events_Frags
							LEFT JOIN ".DB_PREFIX."_Servers ON
								".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
							WHERE
								".DB_PREFIX."_Servers.game='$game' AND killerId='$player'
						");
						list($realkills) = $db->fetch_row();
						echo " ($realkills)";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
					<td width="45%">
					   <?php
						echo $g_options["font_normal"];
						echo "Deaths:";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
					<td width="55%">
					   <?php
						echo $g_options["font_normal"];
						echo $playerdata["deaths"];
						echo $g_options["fontend_normal"];
					   ?>
					</td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
					<td width="45%">
					   <?php
						echo $g_options["font_normal"];
						echo "Suicides:";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
					<td width="55%">
					   <?php
						echo $g_options["font_normal"];
						echo $playerdata["suicides"];
						echo $g_options["fontend_normal"];
					   ?>
					</td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
					<td width="45%">
					   <?php
						echo $g_options["font_normal"];
						echo "Kills per Death:";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
					<td width="55%">
					   <?php
						echo $g_options["font_normal"];
						echo $playerdata["kpd"];
						echo $g_options["fontend_normal"];
					   ?>
					</td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
					<td width="45%">
					   <?php
						echo $g_options["font_normal"];
						echo "Teammate Kills:*";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
					<td width="55%">
					   <?php
						echo $g_options["font_normal"];

						$db->query("
							SELECT
								COUNT(*)
							FROM
								".DB_PREFIX."_Events_Teamkills
							LEFT JOIN ".DB_PREFIX."_Servers ON
								".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Teamkills.serverId
							WHERE
								".DB_PREFIX."_Servers.game='$game' AND killerId='$player'
						");
						list($playerdata["teamkills"]) = $db->fetch_row();

						echo $playerdata["teamkills"];
						echo $g_options["fontend_normal"];
					   ?>
					</td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
					<td width="45%">
					   <?php
						echo $g_options["font_normal"];
						echo "Weapon Accuracy:";
						echo $g_options["fontend_normal"];
					   ?>
					</td>
					<td width="55%">
					   <?php
						echo $g_options["font_normal"];

						$db->query("
							SELECT
								IFNULL(ROUND((SUM(".DB_PREFIX."_Events_Statsme.hits)
									/ SUM(".DB_PREFIX."_Events_Statsme.shots) * 100), 1), 0.0) AS accuracy
							FROM
								".DB_PREFIX."_Events_Statsme
							LEFT JOIN ".DB_PREFIX."_Servers ON
								".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Statsme.serverId
							WHERE
								".DB_PREFIX."_Servers.game='$game' AND playerId='$player'
						");
						list($playerdata["accuracy"]) = $db->fetch_row();

						if ($playerdata["accuracy"] == 0){
							echo "(Unknown.)";
						}
						else {
							echo $playerdata["accuracy"] . "%";
						}
						echo $g_options["fontend_normal"];
					   ?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table><br>
		<?php echo  $g_options["font_normal"]; ?>
		&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=playerhistory&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/history.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="history.gif"><?php echo htmlspecialchars($playerdata["lastName"]); ?>'s Event&nbsp;History</a><br />
		&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=playerchathistory&amp;player=<?php echo $player; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/history.gif" width='16' height='16' border='0' hspace="3" align="middle" alt="history.gif"><?php echo htmlspecialchars($playerdata["lastName"]); ?>'s Chat&nbsp;History</a><br />
		&nbsp;<a href="<?php echo $g_options["scripturl"]; ?>?mode=search&st=player&q=<?php echo $pl_urlname; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/search.gif" width="16" height="16" hspace="3" border="0" align="middle" alt="search.gif">Find other players with the same name</a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table>
<p>&nbsp;</p>
<?php
	if($g_options['useFlash'] == "1") {
		$query = "SELECT
				".DB_PREFIX."_Events_StatsmeTime.*,
				TIME_TO_SEC(".DB_PREFIX."_Events_StatsmeTime.time) as tTime
			FROM
				".DB_PREFIX."_Events_StatsmeTime
			LEFT JOIN ".DB_PREFIX."_Servers ON
				".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_StatsmeTime.serverId
			WHERE
				".DB_PREFIX."_Servers.game='$game' AND playerId='$player'";
		$query = $db->query($query);
		while($result = $db->fetch_array($query)) {
			$eventsArr[] = $result;
		}
		if(count($eventsArr) > 0) {
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
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Playtime per day</b> (hover over the bars to get more information)<?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<div style="text-align: center; display: block;" id="flash_timeline">
	        <div id="playerTimeline">
				<?php echo $g_options["font_normal"]; ?>
				<b>You need to upgrade your flash player</b><br />
				<a href="http://www.adobe.com/go/getflashplayer" target="_blank">Get Flashplayer</a>
				<?php echo $g_options["fontend_normal"];?>
			</div>
			<script type="text/javascript">
				// <![CDATA]
				var so = new SWFObject("<?php echo INCLUDE_PATH; ?>/amcharts/column/amcolumn.swf?<?php echo time(); ?>", "playerTimeline", "600", "<?php echo $timeLineData["height"]; ?>", "8", "<?php echo $g_options['body_bgcolor']; ?>");
				so.addVariable("path", "<?php echo INCLUDE_PATH; ?>/amcharts/column/");
				so.addVariable("settings_file", escape("<?php echo INCLUDE_PATH; ?>/amcharts/column/settings_playertime.xml"));
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
<p>&nbsp;</p>
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

	$result = $db->query("
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
			playerId=$player
		ORDER BY
			$tblAliases->sort $tblAliases->sortorder
		LIMIT $tblAliases->startitem,$tblAliases->numperpage
	");

	$resultCount = $db->query("
		SELECT
			COUNT(*)
		FROM
			".DB_PREFIX."_PlayerNames
		WHERE
			playerId=$player
	");

	list($numitems) = $db->fetch_row($resultCount);

	if ($numitems > 1) {
?>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%"><a name="aliases"></a>
<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Aliases</b><?php echo $g_options["fontend_normal"];?></td>
</tr>
<tr>
	<td>
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblAliases->draw($result, $numitems, 100);
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

	$result = $db->query("
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
			".DB_PREFIX."_Servers.game='$game' AND ".DB_PREFIX."_Events_PlayerActions.playerId=$player
		GROUP BY
			".DB_PREFIX."_Actions.id
		ORDER BY
			$tblPlayerActions->sort $tblPlayerActions->sortorder,
			$tblPlayerActions->sort2 $tblPlayerActions->sortorder
	");

	$numitems = $db->num_rows($result);

	if ($numitems > 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="playeractions"></a>
	<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Player Actions</b><?php echo $g_options["fontend_normal"];?>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?></td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<?php
			$tblPlayerActions->draw($result, $numitems, 100);
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

	$result = $db->query("
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
			".DB_PREFIX."_Servers.game='$game' AND ".DB_PREFIX."_Events_PlayerPlayerActions.playerId=$player
		GROUP BY
			".DB_PREFIX."_Actions.id
		ORDER BY
			$tblPlayerPlayerActions->sort $tblPlayerPlayerActions->sortorder,
			$tblPlayerPlayerActions->sort2 $tblPlayerPlayerActions->sortorder
	");

	$numitems = $db->num_rows($result);

	if ($numitems > 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="playerplayeractions"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Player-Player Actions</b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<?php
			$tblPlayerPlayerActions->draw($result, $numitems, 100);
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

	$db->query("
		SELECT
			COUNT(*)
		FROM
			".DB_PREFIX."_Events_ChangeTeam
		WHERE
			playerId=$player
	");
	list($numteamjoins) = $db->fetch_row();

	$result = $db->query("
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
			".DB_PREFIX."_Teams.game='$game'
		AND ".DB_PREFIX."_Servers.game='$game'
		AND ".DB_PREFIX."_Events_ChangeTeam.playerId=$player
		AND (hidden <>'1' OR hidden IS NULL)
		GROUP BY
			".DB_PREFIX."_Events_ChangeTeam.team
		ORDER BY
			$tblTeams->sort $tblTeams->sortorder,
			$tblTeams->sort2 $tblTeams->sortorder
	");

	$numitems = $db->num_rows($result);

	if ($numitems > 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="teams"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Team Selection</b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblTeams->draw($result, $numitems, 100);
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
				"width=35"
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

	$db->query("
		SELECT
			COUNT(*)
		FROM
			".DB_PREFIX."_Events_ChangeRole
		WHERE
			playerId=$player
	");
	list($numrolejoins) = $db->fetch_row();

	$result = $db->query("
		SELECT
			IFNULL(".DB_PREFIX."_Roles.name, ".DB_PREFIX."_Events_ChangeRole.role) AS name,
			COUNT(".DB_PREFIX."_Events_ChangeRole.id) AS rolecount,
			COUNT(".DB_PREFIX."_Events_ChangeRole.id) / $numrolejoins * 100 AS percent
		FROM
			".DB_PREFIX."_Events_ChangeRole
		LEFT JOIN ".DB_PREFIX."_Roles ON
			".DB_PREFIX."_Events_ChangeRole.role=".DB_PREFIX."_Roles.code
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_ChangeRole.serverId
		WHERE
			".DB_PREFIX."_Servers.game='$game' AND ".DB_PREFIX."_Events_ChangeRole.playerId=$player AND (hidden <>'1' OR hidden IS NULL)
		GROUP BY
			".DB_PREFIX."_Events_ChangeRole.role
		ORDER BY
			$tblRoles->sort $tblRoles->sortorder,
			$tblRoles->sort2 $tblRoles->sortorder
	");

	$numitems = $db->num_rows($result);

	if ($numitems > 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="roles"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Role Selection</b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblRoles->draw($result, $numitems, 100);
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

	$result = $db->query("
		SELECT

			".DB_PREFIX."_Events_Frags.weapon,
			IFNULL(".DB_PREFIX."_Weapons.modifier, 1.00) AS modifier,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) AS kills,
			COUNT(".DB_PREFIX."_Events_Frags.weapon) / $realkills * 100 AS percent
		FROM
			".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Weapons ON
			".DB_PREFIX."_Weapons.code = ".DB_PREFIX."_Events_Frags.weapon
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
		WHERE
			".DB_PREFIX."_Servers.game='$game' AND ".DB_PREFIX."_Events_Frags.killerId=$player
			AND (".DB_PREFIX."_Weapons.game='$game' OR ".DB_PREFIX."_Weapons.weaponId IS NULL)
		GROUP BY
			".DB_PREFIX."_Events_Frags.weapon
		ORDER BY
			$tblWeapons->sort $tblWeapons->sortorder,
			$tblWeapons->sort2 $tblWeapons->sortorder
	");
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="weapons"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Weapon Usage</b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<?php
			$tblWeapons->draw($result, $db->num_rows($result), 100);
		?>
	</div>
	</td>
</tr>
</table><p>
<!-- Begin StatsMe Addon 1.0 by JustinHoMi@aol.com -->
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

	$result = $db->query("
		SELECT
			".DB_PREFIX."_Events_Statsme.weapon AS smweapon,
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
		WHERE
			".DB_PREFIX."_Servers.game='$game' AND ".DB_PREFIX."_Events_Statsme.PlayerId=$player
		GROUP BY
			".DB_PREFIX."_Events_Statsme.weapon
		ORDER BY
			$tblWeaponstats->sort $tblWeaponstats->sortorder,
			$tblWeaponstats->sort2 $tblWeaponstats->sortorder
	");

if ($db->num_rows($result) != 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="weaponstats"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Weapon Stats</b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblWeaponstats->draw($result, $db->num_rows($result), 100);
	?>
	</div></td>
</tr>
</table><p>
<!-- End StatsMe Addon 1.0 by JustinHoMi@aol.com -->
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

	$result = $db->query("
		SELECT
			".DB_PREFIX."_Events_Statsme2.weapon AS smweapon,
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
		WHERE
			".DB_PREFIX."_Servers.game='$game' AND ".DB_PREFIX."_Events_Statsme2.PlayerId=$player
		GROUP BY
			".DB_PREFIX."_Events_Statsme2.weapon
		ORDER BY
			$tblWeaponstats2->sort $tblWeaponstats2->sortorder,
			$tblWeaponstats2->sort2 $tblWeaponstats2->sortorder
	");

if ($db->num_rows($result) != 0) {
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="weaponstats2"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Weapon Target</b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblWeaponstats2->draw($result, $db->num_rows($result), 100);
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

	$result = $db->query("
		SELECT
			IF(map='', '(Unaccounted)', map) AS map,
			SUM(killerId=$player) AS kills,
			SUM(victimId=$player) AS deaths,
			IFNULL(SUM(killerId=$player) / SUM(victimId=$player), '-') AS kpd,
			ROUND(CONCAT(SUM(killerId=$player)) / $realkills * 100, 2) AS percentage # workaround weird mysql bug
		FROM
			".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId=".DB_PREFIX."_Events_Frags.serverId
		WHERE
			".DB_PREFIX."_Servers.game='$game' AND killerId='$player'
			OR victimId='$player'
		GROUP BY
			map
		ORDER BY
			$tblMaps->sort $tblMaps->sortorder,
			$tblMaps->sort2 $tblMaps->sortorder
	");
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%">
		<a name="maps"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Map Performance</b><?php echo $g_options["fontend_normal"];?>
	</td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
		$tblMaps->draw($result, $db->num_rows($result), 100);
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
	 $db->query("DROP TABLE IF EXISTS ".DB_PREFIX."_Frags_Kills");
	 $db->query("
		CREATE TEMPORARY TABLE ".DB_PREFIX."_Frags_Kills
		(
			playerId INT(10),
			kills INT(10),
			deaths INT(10)
		)
	");
	 $db->query("
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
						".DB_PREFIX."_Servers.game='$game' AND killerId = $player
	");

	 $db->query("
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
						".DB_PREFIX."_Servers.game='$game' AND victimId = $player
		");

		$result = $db->query("
				SELECT
					".DB_PREFIX."_Players.lastName AS name,
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
					Count(".DB_PREFIX."_Frags_Kills.kills) >= $killLimit
				ORDER BY
	            $tblPlayerKillStats->sort $tblPlayerKillStats->sortorder,
	            $tblPlayerKillStats->sort2 $tblPlayerKillStats->sortorder
		");

	$numitems = $db->num_rows($result);


?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td width="50%">
  	<a name="playerkills"></a>
	<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Player Kill Statistics (<?php echo $killLimit ?> or more kills)</b><?php echo $g_options["fontend_normal"];?>
 </td>
	<td width="50%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
	<?php
	if ($numitems > 0) {
    	$tblPlayerKillStats->draw($result, $numitems, 100);
  	}
  	else {
  		echo $g_options["font_normal"]."Data out of selected range".$g_options["fontend_normal"];
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
	Show people this person has killed
	<SELECT name="killLimit" onchange='changeLimit(this.options[this.selectedIndex].value)'>
<?php
  for($j = 0; $j < 16; $j++) {
		echo "<option value=$j";
		if($killLimit == $j) { echo " selected"; }
		echo ">$j</option>";
	}
?>
	</select>
	or more times in the last <?php echo DELETEDAYS; ?> days<?php echo $g_options["fontend_normal"];?>
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
	if($g_options['useFlash'] == "1") {
		// get the kills
		$query = "SELECT `eventTime` FROM `".DB_PREFIX."_Events_Frags` WHERE `killerId` = '".$player."'";
		$query = $db->query($query);
		while($result = $db->fetch_array($query)) {
			$killsArr[] = $result;
		}
		if(count($killsArr) > 0) {
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
	<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Player Kill Statistics per Day</b> (hover over the bars to get more information)<?php echo $g_options["fontend_normal"];?>
 </td>
	<td width="30%" align="right">
		<?php echo $g_options["font_normal"]; ?>(Last <?php echo DELETEDAYS; ?> Days)<?php echo $g_options["fontend_normal"];?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div style="margin-top: 10px; margin-left: 40px;">
		<div style="text-align: center; display: block;" id="flash_timeline_kills">
	        <div id="playerKills">
				<?php echo $g_options["font_normal"]; ?>
				<b>You need to upgrade your flash player</b><br />
				<a href="http://www.adobe.com/go/getflashplayer" target="_blank">Get Flashplayer</a>
				<?php echo $g_options["fontend_normal"];?>
			</div>
			<script type="text/javascript">
				// <![CDATA]
				var so = new SWFObject("<?php echo INCLUDE_PATH; ?>/amcharts/column/amcolumn.swf?<?php echo time(); ?>", "playerKills", "600", "<?php echo $timeLineData["height"]; ?>", "8", "<?php echo $g_options['body_bgcolor']; ?>");
				so.addVariable("path", "<?php echo INCLUDE_PATH; ?>/amcharts/column/");
				so.addVariable("settings_file", escape("<?php echo INCLUDE_PATH; ?>/amcharts/column/settings_playertime.xml"));
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
    	<td width="100%"><?php echo $g_options["font_normal"]; ?><b>Note</b> Player event histories cover only the last <?php echo DELETEDAYS; ?> days. Items marked "Last <?php echo DELETEDAYS; ?> Days" or "*" above are generated from the player's Event History. Player kill, death and suicide totals and points ratings cover the entire recorded period.<?php echo $g_options["fontend_normal"];?></td>
    </tr>
    <tr>
    	<td width="100%" align="right"><br><br>
    	<?php echo $g_options["font_small"]; ?><b>Admin Options:</b> <a href="<?php echo $g_options["scripturl"] . "?mode=admin&task=tools_editdetails_player&id=$player"; ?>">Edit Player Details</a><?php echo $g_options["fontend_small"]; ?></td>
    </tr>
</table>
