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
	$queryNews = mysql_query("SELECT id,`date`,`user`,`email`,`subject`,`message` FROM ".DB_PREFIX."_News ORDER BY `date` DESC");
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
							<table cellpadding="2" cellspacing="1" border="0" width="100%">
								<tr bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
									<td colspan="2">
										<?php echo $g_options["font_normal"]; ?>
										<a href="javascript:showNews('<?php echo $i; ?>');"><?php echo htmlentities($rowdata['subject'],ENT_QUOTES, "UTF-8"); ?></a>
										<?php echo l('from'); ?> <?php echo $rowdata['date']; ?>
									</td>
								</tr>
							</table>
							<?php echo $g_options["fontend_normal"];?>
							<div class="newsBox" id="newsBox_<?php echo $i; ?>" style="display: none;">
	<?php
			}
	?>
							<table cellpadding="2" cellspacing="1" border="0" width="100%">
								<tr bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
									<td width="100">
										<?php echo $g_options["font_normal"]; ?>
										<b><?php echo l('Author'); ?></b>
										<?php echo $g_options["fontend_normal"];?>
									</td>
									<td width="*">
										<?php echo $g_options["font_normal"]; ?>
										<a href="mailto:<?php echo $rowdata['email']; ?>"><?php echo $rowdata['user']; ?></a>
										<?php echo $g_options["fontend_normal"];?>
									</td>
								</tr>
								<tr bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
									<td width="100">
										<?php echo $g_options["font_normal"]; ?>
										<b><?php echo l('Subject'); ?></b>
										<?php echo $g_options["fontend_normal"];?>
									</td>
									<td width="*">
										<?php echo $g_options["font_normal"]; ?>
										<?php echo $rowdata['subject']; ?>
										<?php echo $g_options["fontend_normal"];?>
									</td>
								</tr>
								<tr bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
									<td width="100" valign="top">
										<?php echo $g_options["font_normal"]; ?>
										<b><?php echo l('Message'); ?></b>
										<?php echo $g_options["fontend_normal"];?>
									</td>
									<td width="*">
										<?php echo $g_options["font_normal"]; ?>
										<?php echo nl2br($rowdata['message']); ?>
										<?php echo $g_options["fontend_normal"];?>
									</td>
								</tr>
								<tr bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
									<td width="100">
										<?php echo $g_options["font_normal"]; ?>
										<b><?php echo l('Posted at'); ?></b>
										<?php echo $g_options["fontend_normal"];?>
									</td>
									<td width="*">
										<?php echo $g_options["font_normal"]; ?>
										<?php echo $rowdata['date']; ?>
										<?php echo $g_options["fontend_normal"];?>
									</td>
								</tr>
							</table>
							</div>
	<?php
			$i++;
		}
	?>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php
		}
	}
?>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Games'); ?></b><?php echo $g_options["fontend_normal"];?><p>

		<table width="80%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $g_options["table_border"]; ?>">

		<tr>
			<td><table width="100%" border="0" cellspacing="1" cellpadding="4">

				<tr valign="bottom" bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
					<td width="60%" align="left"><?php echo $g_options["font_small"]; ?><font color="<?php echo $g_options["table_head_text"]; ?>">&nbsp; <?php echo l('Game'); ?></font><?php echo $g_options["fontend_small"]; ?></td>
					<td width="20%" align="center"><?php echo $g_options["font_small"]; ?><font color="<?php echo $g_options["table_head_text"]; ?>">&nbsp; <?php echo l('Top Player'); ?></font><?php echo $g_options["fontend_small"]; ?></td>
					<td width="20%" align="center"><?php echo $g_options["font_small"]; ?><font color="<?php echo $g_options["table_head_text"]; ?>">&nbsp; <?php echo l('Top Clan'); ?></font><?php echo $g_options["fontend_small"]; ?></td>
				</tr>

<?php
				while ($gamedata = mysql_fetch_assoc($queryAllGames)) {
					$queryTopPlayer = mysql_query("
						SELECT
							playerId,
							lastName
						FROM
							".DB_PREFIX."_Players
						WHERE
							game='".$gamedata['code']."'
							AND hideranking=0
						ORDER BY
							skill DESC
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

				<tr valign="middle">
					<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr valign="middle">
							<td width="100%"><?php echo $g_options["font_normal"]; ?><b>&nbsp;<a href="<?php echo $g_options["scripturl"] . "?game=".$gamedata['code']; ?>"><img src="<?php	echo $g_options["imgdir"] . "/game-".$gamedata['code'].".gif"; ?>" width="24" height="24" hspace="3" border="0" align="middle" alt="Game"><?php echo $gamedata['name']; ?></a></b><?php echo $g_options["fontend_normal"]; ?></td>
							<td><?php echo $g_options["font_small"]; ?>&nbsp;<a href="<?php echo $g_options["scripturl"] . "?mode=players&amp;game=".$gamedata['code']; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/player.gif" width=16 height=16 hspace=3 alt="Player Rankings" border="0" align="middle"><?php echo l('Players'); ?></a>&nbsp;&nbsp;<?php echo $g_options["fontend_small"]; ?></td>
							<td><?php echo $g_options["font_small"]; ?>&nbsp;<a href="<?php echo $g_options["scripturl"] . "?mode=clans&amp;game=".$gamedata['code']; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/clan.gif" width=16 height=16 hspace=3 alt="Clan Rankings" border="0" align="middle"><?php echo l('Clans'); ?></a>&nbsp;&nbsp;<?php echo $g_options["fontend_small"]; ?></td>
						</tr>
						</table>
					</td>
					<td align="center" bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>"><?php
						echo $g_options["font_normal"];
						if ($topplayer !== false) {
							echo '<a href="' . $g_options['scripturl'] . '?mode=playerinfo&amp;player='
								. $topplayer['playerId'] . '">' . htmlentities($topplayer['lastName'], ENT_COMPAT, "UTF-8") . '</a>';
						}
						else {
							echo '-';
						}
						echo $g_options["fontend_normal"];
					?></td>
					<td align="center" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php
						echo $g_options["font_normal"];
						if ($topclan !== false) {
							echo '<a href="' . $g_options['scripturl'] . '?mode=claninfo&amp;clan='
								. $topclan['clanId'] . '">' . htmlentities($topclan['name'], ENT_COMPAT, "UTF-8") . '</a>';
						}
						else
						{
							echo '-';
						}
						echo $g_options["fontend_normal"];
					?></td>
				</tr>
<?php
				}
?>
				</table></td>
		</tr>

		</table><p>
		<br>


		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('General Statistics'); ?></b><?php echo $g_options["fontend_normal"];?><p>

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

		<table width="80%" align="center" border="0" cellspacing="0" cellpadding="3">
		<tr valign="top">
			<td width=10><?php echo $g_options["font_normal"]; ?><b>&#149;&nbsp;</b><?php echo $g_options["fontend_normal"]; ?></td>
			<td width="100%"><?php
				echo $g_options["font_normal"];

				echo "<b>$num_players</b> ",l('players and'),' <b>',$num_clans,'</b> ',l('Clans'),
					' ',l("ranked in"),' <b>',$num_games,'</b> ',l('games on'),' <b>',$num_servers,"</b> ",
					 l("Servers");

				echo $g_options["fontend_normal"];
			?></td>
		</tr>

<?php
			if ($lastevent) {
?>
		<tr valign="top">
			<td width=10><?php echo $g_options["font_normal"]; ?><b>&#149;&nbsp;</b><?php echo $g_options["fontend_normal"]; ?></td>
			<td width="100%"><?php
				echo $g_options["font_normal"];

				echo l("Last kill")," <b>$lastevent</b> ",l('ago');

				echo $g_options["fontend_normal"];
			?></td>
		</tr>
<?php
			}
?>

		<tr valign="top">
			<td width=10><?php echo $g_options["font_normal"]; ?><b>&#149;&nbsp;</b><?php echo $g_options["fontend_normal"]; ?></td>
			<td width="100%"><?php
				echo $g_options["font_normal"];

				echo l("All statistics are generated in real-time. Event history data expires after"),"<b> " . DELETEDAYS . "</b> ",l("days"),'.';

				echo $g_options["fontend_normal"];
			?></td>
		</tr>
		</table>
	</td>
</tr>
</table>
