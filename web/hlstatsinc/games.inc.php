<?php
/**
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

pageHeader(array(l("Contents")), array(l("Contents")=>""));

// should we hide the news ?
if(!$g_options['hideNews']) {
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
?>

<h1><?php echo l('Games'); ?></h1>
<p>
	<table align="center" border="0" cellspacing="0" cellpadding="2" width="100%">
		<tr>
			<th colspan="3"><?php echo l('Game'); ?></th>
			<th align="center"><?php echo l('Top Player'); ?></th>
			<th align="center"><?php echo l('Top Clan'); ?></th>
		</tr>

	<?php
		while ($gamedata = mysql_fetch_assoc($queryAllGames)) {
			$queryTopPlayer = mysql_query("
				SELECT `playerId`,`lastName`
				FROM `".DB_PREFIX."_Players`
				WHERE `game`= '".mysql_escape_string($gamedata['code'])."'
					AND `hideranking` = 0
				ORDER BY `skill` DESC
				LIMIT 1
			");

			$topplayer = false;
			if (mysql_num_rows($queryTopPlayer) === 1) {
				$topplayer = mysql_fetch_assoc($queryTopPlayer);
			}

			$queryTopClan = mysql_query("
				SELECT
					".DB_PREFIX."_Clans.clanId,
					".DB_PREFIX."_Clans.name,
					AVG(".DB_PREFIX."_Players.skill) AS skill,
					COUNT(".DB_PREFIX."_Players.playerId) AS numplayers
				FROM
					".DB_PREFIX."_Clans
				LEFT JOIN ".DB_PREFIX."_Players ON
					".DB_PREFIX."_Players.clan = ".DB_PREFIX."_Clans.clanId
				WHERE
					".DB_PREFIX."_Clans.game='".$gamedata['code']."'
				GROUP BY
					".DB_PREFIX."_Clans.clanId
				HAVING
					skill IS NOT NULL
					AND numplayers > 1
				ORDER BY
					skill DESC
				LIMIT 1
			");

			$topclan = false;
			if (mysql_num_rows($queryTopClan) === 1) {
				$topclan = mysql_fetch_assoc($queryTopClan);
			}
	?>
		<tr>
			<td>
				<a href="<?php echo $g_options["scripturl"] . "?game=".$gamedata['code']; ?>"><img src="<?php	echo $g_options["imgdir"] . "/game-".$gamedata['code'].".gif"; ?>" width="24" height="24" hspace="3" border="0" align="middle" alt="<?php echo $gamedata['code']; ?>"><?php echo $gamedata['name']; ?></a>
			</td>
			<td>
				<a href="<?php echo $g_options["scripturl"] . "?mode=players&amp;game=".$gamedata['code']; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/player.gif" width="16" height="16" hspace="3" alt="<?php echo l('Player Rankings'); ?>" border="0" align="middle"> <?php echo l('Players'); ?></a>
			</td>
			<td>
				<a href="<?php echo $g_options["scripturl"] . "?mode=clans&amp;game=".$gamedata['code']; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/clan.gif" width="16" height="16" hspace="3" alt="<?php echo l('Clan Rankings'); ?>" border="0" align="middle"> <?php echo l('Clans'); ?></a>
			</td>
			<td>
	<?php
		if ($topplayer !== false) {
			echo '<a href="' . $g_options['scripturl'] . '?mode=playerinfo&amp;player='
				. $topplayer['playerId'] . '">' . htmlentities($topplayer['lastName'], ENT_COMPAT, "UTF-8") . '</a>';
		}
		else {
			echo '-';
		}
	?>
			</td>
			<td>
	<?php
		if ($topclan !== false) {
			echo '<a href="' . $g_options['scripturl'] . '?mode=claninfo&amp;clan='
				. $topclan['clanId'] . '">' . htmlentities($topclan['name'], ENT_COMPAT, "UTF-8") . '</a>';
		}
		else {
			echo '-';
		}
			?></td>
		</tr>
	<?php
		}
	?>
	</table>
</p>
<h1><?php echo l('General Statistics'); ?></h1>
<p>
<?php
	$query = mysql_query("SELECT COUNT(*) AS pc FROM ".DB_PREFIX."_Players");
	$result = mysql_fetch_assoc($query);
	$num_players = $result['pc'];

	$query = mysql_query("SELECT COUNT(*) AS cc FROM ".DB_PREFIX."_Clans");
	$result = mysql_fetch_assoc($query);
	$num_clans = $result['cc'];

	$query = mysql_query("SELECT COUNT(*) AS sc FROM ".DB_PREFIX."_Servers");
	$result = mysql_fetch_assoc($query);
	$num_servers = $result['sc'];

	$query = mysql_query("SELECT MAX(eventTime) AS lastEvent FROM ".DB_PREFIX."_Events_Frags");
	$result = mysql_fetch_assoc($query);
	$timstamp = strtotime($result['lastEvent']);
	$lastevent = getInterval($timstamp);
?>
	<ul>
		<li>
			<b><?php echo $num_players; ?></b>
			<?php echo l('players and'); ?><b> <?php echo $num_clans; ?></b> <?php echo l('Clans'),' ',l("ranked in"); ?>
			<b><?php echo $num_games; ?></b> <?php echo l('games on'); ?> <b><?php echo $num_servers; ?></b>
			<?php echo l("Servers"); ?>
		</li>

<?php
	if ($lastevent) {
?>
		<li>
			<?php echo l("Last kill"); ?> <b><?php echo $lastevent; ?></b> <?php echo l('ago'); ?>
		</li>
<?php
	}
?>
		<li>
			<?php echo l("All statistics are generated in real-time. Event history data expires after"),"<b> " . DELETEDAYS . "</b> ",l("days"),'.';?>
		</li>
	</ul>
</p>
