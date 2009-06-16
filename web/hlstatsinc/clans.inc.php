<?php
/**
 * $Id: clans.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/clans.inc.php $
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


	// Clan Rankings

	$game = sanitize($_GET['game']);

	$db->query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='$game'");
	if ($db->num_rows() < 1) error("No such game '$game'.");

	list($gamename) = $db->fetch_row();
	$db->free_result();

	if (isset($_GET["minmembers"]))
	{
		$minmembers = intval($_GET["minmembers"]);
	}
	else
	{
		$minmembers = 2;
	}

	pageHeader(
		array($gamename, "Clan Rankings"),
		array($gamename=>"%s?game=$game", "Clan Rankings"=>"")
	);
?>

<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="game" value="<?php echo $game; ?>">
<input type="hidden" name="st" value="clan">

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">

<tr valign="bottom">
	<td width="75%"><?php echo $g_options["font_normal"]; ?><b>&#149;</b> Find a clan: <input type="text" name="q" size=20 maxlength=64 class="textbox"> <input type="submit" value="Search" class="smallsubmit"><?php echo $g_options["fontend_normal"]; ?></td>
	<td width="25%" align="right" nowrap><?php echo $g_options["font_normal"]; ?>Go to <a href="<?php echo $g_options["scripturl"] . "?mode=players&amp;game=$game"; ?>"><img src="<?php echo $g_options["imgdir"]; ?>/player.gif" width="16" height="16" hspace="3" border="0" align="middle" alt="player.gif">Player Rankings</a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table><p>
</form>

<?php
	$table = new Table(
		array(
			new TableColumn(
				"name",
				"Name",
				"width=30&icon=clan&link=" . urlencode("mode=claninfo&amp;clan=%k")
			),
			new TableColumn(
				"tag",
				"Tag",
				"width=15"
			),
			new TableColumn(
				"skill",
				"Points",
				"width=10&align=right"
			),
			new TableColumn(
				"nummembers",
				"Members",
				"width=10&align=right"
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
			)
		),
		"clanId",
		"skill",
		"kpd",
		true
	);

	$result = $db->query("
		SELECT
			".DB_PREFIX."_Clans.clanId,
			".DB_PREFIX."_Clans.name,
			".DB_PREFIX."_Clans.tag,
			COUNT(".DB_PREFIX."_Players.playerId) AS nummembers,
			SUM(".DB_PREFIX."_Players.kills) AS kills,
			SUM(".DB_PREFIX."_Players.deaths) AS deaths,
			ROUND(AVG(".DB_PREFIX."_Players.skill)) AS skill,
			IFNULL(SUM(".DB_PREFIX."_Players.kills)/SUM(".DB_PREFIX."_Players.deaths), '-') AS kpd
		FROM
			".DB_PREFIX."_Clans
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.clan=".DB_PREFIX."_Clans.clanId
		WHERE
			".DB_PREFIX."_Clans.game='$game'
			AND ".DB_PREFIX."_Players.hideranking = 0
		GROUP BY
			".DB_PREFIX."_Clans.clanId
		HAVING
			nummembers >= $minmembers
		ORDER BY
			$table->sort $table->sortorder,
			$table->sort2 $table->sortorder,
			name ASC
		LIMIT $table->startitem,$table->numperpage
	");

	$resultCount = $db->query("
		SELECT
			".DB_PREFIX."_Clans.clanId
		FROM
			".DB_PREFIX."_Clans
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.clan=".DB_PREFIX."_Clans.clanId
		WHERE
			".DB_PREFIX."_Clans.game='$game'
			AND ".DB_PREFIX."_Players.hideranking = 0
		GROUP BY
			".DB_PREFIX."_Clans.clanId
		HAVING
			COUNT(".DB_PREFIX."_Players.playerId) >= $minmembers
	");

	$table->draw($result, $db->num_rows($resultCount), 90);
?><p>

<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="2">

<tr valign="bottom">
	<td width="75%"><?php echo $g_options["font_normal"]; ?>
<?php
	foreach ($_GET as $k=>$v)
	{
		if ($k != "minmembers")
		{
			echo "<input type=\"hidden\" name=\"$k\" value=\"" . htmlspecialchars($v) . "\">\n";
		}
	}
?>
		<b>&#149;</b> Only show clans with <input type="text" name="minmembers" size=4 maxlength=2 value="<?php echo $minmembers; ?>" class="textbox"> or more members. <input type="submit" value="Apply" class="smallsubmit"><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table>
</form>
