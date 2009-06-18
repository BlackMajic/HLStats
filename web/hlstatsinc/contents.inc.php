<?php
/**
 * $Id: contents.inc.php 671 2009-03-03 07:36:22Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstatsinc/contents.inc.php $
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

	$resultGames = $db->query("
		SELECT
			code,name
		FROM
			".DB_PREFIX."_Games
		WHERE
			hidden='0'
		ORDER BY
			name ASC
	");

	$num_games = $db->num_rows($resultGames);

	if(!empty($_GET['game'])) {
		if(validateInput($_GET['game'],'nospace')) {
			$game = $_GET["game"];
			include(INCLUDE_PATH . "/game.inc.php");
		}
		else {
			die('Wrong input');
		}
	}
	elseif ($num_games == 1) {
		list($game) = $db->fetch_row($resultGames);
		include(INCLUDE_PATH . "/game.inc.php");
	}
	else {

		pageHeader(array("Contents"), array("Contents"=>""));
		// should we hide the news ?
		if(!$g_options['hideNews']) {
			$db->query("
				SELECT *
				FROM
					".DB_PREFIX."_News
				ORDER BY
					date DESC
			");

		if($db->num_rows() > 0) {


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

	<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif">
				<b>&nbsp;News</b><?php echo $g_options["fontend_normal"];?>
				<table width="75%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $g_options["table_border"]; ?>">
					<tr valign="top">
						<td>

	<?php
		$i = 0;
		while ($rowdata = $db->fetch_array()) {
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
										from <?php echo $rowdata['date']; ?>
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
										<b>Author</b>
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
										<b>Subject</b>
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
										<b>Message</b>
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
										<b>Posted at</b>
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
	<td><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Games</b><?php echo $g_options["fontend_normal"];?><p>

		<table width="80%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $g_options["table_border"]; ?>">

		<tr>
			<td><table width="100%" border="0" cellspacing="1" cellpadding="4">

				<tr valign="bottom" bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
					<td width="60%" align="left"><?php echo $g_options["font_small"]; ?><font color="<?php echo $g_options["table_head_text"]; ?>">&nbsp;Game</font><?php echo $g_options["fontend_small"]; ?></td>
					<td width="20%" align="center"><?php echo $g_options["font_small"]; ?><font color="<?php echo $g_options["table_head_text"]; ?>">&nbsp;Top Player</font><?php echo $g_options["fontend_small"]; ?></td>
					<td width="20%" align="center"><?php echo $g_options["font_small"]; ?><font color="<?php echo $g_options["table_head_text"]; ?>">&nbsp;Top Clan</font><?php echo $g_options["fontend_small"]; ?></td>
				</tr>

<?php
				while ($gamedata = $db->fetch_row($resultGames))
				{
					$result = $db->query("
						SELECT
							playerId,
							lastName
						FROM
							".DB_PREFIX."_Players
						WHERE
							game='$gamedata[0]'
							AND hideranking=0
						ORDER BY
							skill DESC
						LIMIT 1
					");

					if ($db->num_rows($result) == 1)
					{
						$topplayer = $db->fetch_row($result);
					}
					else
					{
						$topplayer = false;
					}

					$result = $db->query("
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
							".DB_PREFIX."_Clans.game='$gamedata[0]'
						GROUP BY
							".DB_PREFIX."_Clans.clanId
						HAVING
							skill IS NOT NULL
							AND numplayers > 1
						ORDER BY
							skill DESC
						LIMIT 1
					");

					if ($db->num_rows($result) == 1)
					{
						$topclan = $db->fetch_row($result);
					}
					else
					{
						$topclan = false;
					}
		?>

				<tr valign="middle">
					<td bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">

						<tr valign="middle">
							<td width="100%"><?php echo $g_options["font_normal"]; ?><b>&nbsp;<a href="<?php echo $g_options["scripturl"] . "?game=$gamedata[0]"; ?>"><img src="<?php
	$imgfile = $g_options["imgdir"] . "/game-$gamedata[0].gif";
	if (file_exists($imgfile))
	{
		echo $imgfile;
	}
	else
	{
		echo $g_options["imgdir"] . "/game.gif";
	}
?>" width="24" height="24" hspace="3" border="0" align="middle" alt="Game"><?php echo $gamedata[1]; ?></a></b><?php echo $g_options["fontend_normal"]; ?></td>
							<td><?php echo $g_options["font_small"]; ?>&nbsp;<a href="<?php echo $g_options["scripturl"] . "?mode=players&amp;game=$gamedata[0]"; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/player.gif" width=16 height=16 hspace=3 alt="Player Rankings" border="0" align="middle">Players</a>&nbsp;&nbsp;<?php echo $g_options["fontend_small"]; ?></td>
							<td><?php echo $g_options["font_small"]; ?>&nbsp;<a href="<?php echo $g_options["scripturl"] . "?mode=clans&amp;game=$gamedata[0]"; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/clan.gif" width=16 height=16 hspace=3 alt="Clan Rankings" border="0" align="middle">Clans</a>&nbsp;&nbsp;<?php echo $g_options["fontend_small"]; ?></td>
						</tr>

						</table></td>
					<td align="center" bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>"><?php
						echo $g_options["font_normal"];
						if ($topplayer)
						{
							echo '<a href="' . $g_options['scripturl'] . '?mode=playerinfo&amp;player='
								. $topplayer[0] . '">' . htmlentities($topplayer[1], ENT_COMPAT, "UTF-8") . '</a>';
						}
						else
						{
							echo '-';
						}
						echo $g_options["fontend_normal"];
					?></td>
					<td align="center" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php
						echo $g_options["font_normal"];
						if ($topclan)
						{
							echo '<a href="' . $g_options['scripturl'] . '?mode=claninfo&amp;clan='
								. $topclan[0] . '">' . htmlentities($topclan[1], ENT_COMPAT, "UTF-8") . '</a>';
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


		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;General Statistics</b><?php echo $g_options["fontend_normal"];?><p>

		<?php
			$result = $db->query("SELECT COUNT(*) FROM ".DB_PREFIX."_Players");
			list($num_players) = $db->fetch_row($result);
			$num_players = intval($num_players);

			$result = $db->query("SELECT COUNT(*) FROM ".DB_PREFIX."_Clans");
			list($num_clans) = $db->fetch_row($result);
			$num_clans = intval($num_clans);

			$result = $db->query("SELECT COUNT(*) FROM ".DB_PREFIX."_Servers");
			list($num_servers) = $db->fetch_row($result);
			$num_servers = intval($num_servers);

			$result = $db->query("
				SELECT
					DATE_FORMAT(MAX(eventTime), '%r, %a. %e %b.')
				FROM
					".DB_PREFIX."_Events_Frags
			");
			list($lastevent) = $db->fetch_row($result);
?>

		<table width="80%" align="center" border="0" cellspacing="0" cellpadding="3">
		<tr valign="top">
			<td width=10><?php echo $g_options["font_normal"]; ?><b>&#149;&nbsp;</b><?php echo $g_options["fontend_normal"]; ?></td>
			<td width="100%"><?php
				echo $g_options["font_normal"];

				echo "<b>$num_players</b> players and <b>$num_clans</b> clans "
					. "ranked in <b>$num_games</b> games on <b>$num_servers</b>"
					. " servers.";

				echo $g_options["fontend_normal"];
			?></td>
		</tr>

<?php
			if ($lastevent)
			{
?>
		<tr valign="top">
			<td width=10><?php echo $g_options["font_normal"]; ?><b>&#149;&nbsp;</b><?php echo $g_options["fontend_normal"]; ?></td>
			<td width="100%"><?php
				echo $g_options["font_normal"];

				echo "Last kill <b>$lastevent</b>";

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

				echo "All statistics are generated in real-time. Event history data expires after <b>" . DELETEDAYS . "</b> days.";

				echo $g_options["fontend_normal"];
			?></td>
		</tr>
		</table>
	</td>
</tr>
</table>

<?php
	}
?>
