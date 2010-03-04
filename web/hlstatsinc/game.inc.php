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

pageHeader(array($gamename), array($gamename=>""));
?>
<div id="sidebar" >
	<h1><?php echo l('Sections'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?mode=players&amp;game=$game"; ?>"><?php echo l('Player Rankings'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php?mode=clans&amp;game=$game"; ?>"><?php echo l('Clan Rankings'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php?mode=weapons&amp;game=$game"; ?>"><?php echo l('Weapon Statistics'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php?mode=actions&amp;game=$game"; ?>"><?php echo l('Action Statistics'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php?mode=maps&amp;game=$game"; ?>"><?php echo l('Map Statistics'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
<?php
// should we hide the news ?
if(!$g_options['hideNews'] && $num_games === 1) {
	$queryNews = mysql_query("SELECT id,`date`,`user`,`email`,`subject`,`message`
							 FROM ".DB_PREFIX."_News
							 ORDER BY `date` DESC");
	if(mysql_num_rows($queryNews) > 0) {
?>
<script type="text/javascript" language="javascript">
	<!--
	function showNews(id) {
		if(document.getElementById("newsBox_" + id).style.display == "none") {
			document.getElementById("newsBox_" + id).style.display = "block";
		}
		else {
			document.getElementById("newsBox_" + id).style.display = "none";
		}

	}
	//-->
	</script>
	<h1><?php echo l('News'); ?></h1>
	<?php
		$i = 0;
		while ($rowdata = mysql_fetch_assoc($queryNews)) {
			if($i == 0) {
	?>
	<div class="newsBox" id="newsBox_<?php echo $i; ?>">
	<?php
			}
			else {
	?>
	<a href="javascript:showNews('<?php echo $i; ?>');"><?php echo htmlentities($rowdata['subject'],ENT_QUOTES, "UTF-8"); ?></a>
	<?php echo l('from'); ?> <?php echo $rowdata['date']; ?>
	<div class="newsBox" id="newsBox_<?php echo $i; ?>" style="display: none;">
	<?php
			}
	?>
		<p>
			<i><?php echo $rowdata['subject']; ?></i><br />
			<br />
			<?php echo nl2br($rowdata['message']); ?>
		</p>
		<p class="comments align-right clear"><?php echo l('written by'),' ',$rowdata['user'],' (',$rowdata['date'],')'; ?></p>
	</div>
	<?php
			$i++;
		}
	?>
	<?php
	}
}
	if (!$g_options['hideAwards']) {
		$queryAwards = mysql_query("SELECT
									".DB_PREFIX."_Awards.awardType,
									".DB_PREFIX."_Awards.code,
									".DB_PREFIX."_Awards.name,
									".DB_PREFIX."_Awards.verb,
									".DB_PREFIX."_Awards.d_winner_id,
									".DB_PREFIX."_Awards.d_winner_count,
									".DB_PREFIX."_Players.lastName AS d_winner_name
								FROM
									".DB_PREFIX."_Awards
								LEFT JOIN ".DB_PREFIX."_Players ON
									".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_Awards.d_winner_id
								WHERE
									".DB_PREFIX."_Awards.game='$game'
								ORDER BY
									".DB_PREFIX."_Awards.awardType DESC,
									".DB_PREFIX."_Awards.name ASC");

		// this value comes from awards.pl
		$awards_numdays = 1;
		if(!empty($g_options['awards_numdays'])) {
			$awards_numdays = (int)$g_options['awards_numdays'];
		}

		$awards_d_date = false;
		if(!empty($g_options['awards_d_date'])) {
			$tmptime = strtotime($g_options['awards_d_date']);
			if($tmptime !== false) {
				// eliminates false dates from db
				$awards_d_date = date('l d.m.',$tmptime);

				// awards_d_date - the days configured in $awards_numdays
				$tmptime -= $awards_numdays*86400;
				$awards_s_date = $awards_d_date = date('l d.m.',$tmptime);
			}
		}

		if (mysql_num_rows($queryAwards) > 0 && $awards_d_date) {
?>
<h1><?php
			if ($awards_numdays == 1) {
				echo l("Daily Awards")," ",l("for")," ",$awards_s_date;
			}
			else {
				echo $awards_numdays," ",l('Day Awards'),": ",$awards_s_date," ",l('to')," ",$awards_d_date;
			}
?></h1>
	<div class="content">
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
<?php
			$c = 0;
			while ($awarddata = mysql_fetch_assoc($queryAwards)) {
				$colour = ($c % 2) + 1;
				$c++;
?>
		<tr>
			<th width="30%"><?php echo htmlspecialchars($awarddata["name"]);?></th>
			<td width="70%">
			<?php
				if ($awarddata["d_winner_id"]) {
					echo "<a href=\"index.php?mode=playerinfo&amp;player="
						. $awarddata["d_winner_id"] . "\"><img src=\""
						. $g_options["imgdir"] . "/player.gif\" width=16 height=16 "
						. "hspace='4' border='0' align=\"middle\" alt=\"player.gif\">&nbsp;<b>"
						. htmlspecialchars($awarddata["d_winner_name"]) . "</b></a> ("
						. $awarddata["d_winner_count"] . " " . htmlspecialchars($awarddata["verb"]) . ")";
				}
				else {
					echo "(",l('Nobody').')';
				}
				?>
			</td>
		</tr>
<?php
			}
?>
	</table>
	</div>
<?php
		}
		else {
			// awards not created yet
		}
	}
?>
<h1><?php echo l('Participating Servers'); ?></h1>
<div class="content">
	<table width="100%" border="0" cellspacing="1" cellpadding="4">
		<tr>
			<th>&nbsp;<?php echo l('Name'); ?></th>
			<th>&nbsp;<?php echo l('Address'); ?></th>
			<th>&nbsp;<?php echo l('Statistics'); ?></th>
		</tr>
<?php
	$query = mysql_query("SELECT
							serverId, name,
							IF(publicaddress != '',
								publicaddress,
								concat(address, ':', port)
							) AS addr,
							statusurl
						FROM
							".DB_PREFIX."_Servers
						WHERE
							game = '".mysql_escape_string($game)."'
						ORDER BY
							name ASC,
							addr ASC");
	$i=0;
	while ($rowdata = mysql_fetch_array($query)) {
		$c = ($i % 2) + 1;

		if ($rowdata["statusurl"]) {
			$addr = "<a href=\"" . $rowdata["statusurl"] . "\">"
				. $rowdata["addr"] . "</a>";
		}
		else {
			$addr = $rowdata["addr"];
		}
?>
		<tr>
			<td align="left">
				<img src="<?php echo $g_options["imgdir"]; ?>/server.gif" width="16" height="16" hspace="3" border="0" align="middle" alt="server.gif">
				<?php echo $rowdata["name"]; ?>
			</td>
			<td align="left"><?php echo $addr; ?></td>
			<td align="center"><?php echo "<a href=\"index.php?mode=livestats&amp;server=$rowdata[serverId]\">",l('View'),"</a>"; ?></td>
		</tr>
<?php
		$i++;
	}
?>
	</table>
</div>
<h1><?php echo $gamename; ?> <?php echo l('Statistics'); ?></h1>
<?php
	$query = mysql_query("SELECT COUNT(*) AS plc FROM ".DB_PREFIX."_Players WHERE game='".mysql_escape_string($game)."'");
	$result = mysql_fetch_assoc($query);
	$num_players = $result['plc'];

	$query = mysql_query("SELECT COUNT(*) AS cc FROM ".DB_PREFIX."_Clans WHERE game='".mysql_escape_string($game)."'");
	$result = mysql_fetch_assoc($query);
	$num_clans = $result['cc'];

	$query = mysql_query("SELECT COUNT(*) AS sc FROM ".DB_PREFIX."_Servers WHERE game='".mysql_escape_string($game)."'");
	$result = mysql_fetch_assoc($query);
	$num_servers = $result['sc'];

	$query = mysql_query("SELECT MAX(eventTime) as lastEvent
		FROM ".DB_PREFIX."_Events_Frags
		LEFT JOIN ".DB_PREFIX."_Servers ON
			".DB_PREFIX."_Servers.serverId = ".DB_PREFIX."_Events_Frags.serverId
		WHERE ".DB_PREFIX."_Servers.game='$game'");
	$result = mysql_fetch_assoc($query);
	$lastevent = $result['lastEvent'];
	$lastevent = date("D d. M. Y H:i:s",strtotime($lastevent));
	mysql_free_result($query);
?>
<p>
	<ul>
		<li>
			<?php echo "<b>$num_players</b> ",l('players'),"  <b>$num_clans</b> ",l('clans ranked on')," <b>$num_servers</b> ",l('servers'),"."; ?>
		</li>
<?php
	if ($lastevent) {
?>
		<li>
			<?php echo l("Last kill")," <b>$lastevent</b>"; ?>
		</li>
<?php
	}
?>
		<li>
			<?php echo l("All statistics are generated in real-time. Event history data expires after"), " <b>" . DELETEDAYS . "</b> ",l("days"),"."; ?>
		</li>
	</ul>
</p>
