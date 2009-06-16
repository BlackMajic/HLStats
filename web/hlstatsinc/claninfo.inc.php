<?php
/**
 * $Id: claninfo.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/claninfo.inc.php $
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


	// Clan Details

	$clan = sanitize($_GET["clan"])
		or error("No clan ID specified.");
	$game = sanitize($_GET['game']);

	$db->query("
		SELECT
			".DB_PREFIX."_Clans.tag,
			".DB_PREFIX."_Clans.name,
			".DB_PREFIX."_Clans.homepage,
			".DB_PREFIX."_Clans.game,
			SUM(".DB_PREFIX."_Players.kills) AS kills,
			SUM(".DB_PREFIX."_Players.deaths) AS deaths,
			COUNT(".DB_PREFIX."_Players.playerId) AS nummembers,
			ROUND(AVG(".DB_PREFIX."_Players.skill)) AS avgskill
		FROM
			".DB_PREFIX."_Clans
		LEFT JOIN ".DB_PREFIX."_Players ON
			".DB_PREFIX."_Players.clan = ".DB_PREFIX."_Clans.clanId
		WHERE
			".DB_PREFIX."_Clans.clanId=$clan
			AND ".DB_PREFIX."_Players.hideranking = 0
		GROUP BY
			".DB_PREFIX."_Clans.clanId
	");
	if ($db->num_rows() != 1)
		error("No such clan '$clan'.");

	$clandata = $db->fetch_array();
	$db->free_result();


	$cl_name = ereg_replace(" ", "&nbsp;", htmlspecialchars($clandata["name"]));
	$cl_tag  = ereg_replace(" ", "&nbsp;", htmlspecialchars($clandata["tag"]));
	$cl_full = $cl_tag . " " . $cl_name;

	$game = $clandata["game"];
	$db->query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='$game'");
	if ($db->num_rows() != 1)
		$gamename = ucfirst($game);
	else
		list($gamename) = $db->fetch_row();


	pageHeader(
		array($gamename, "Clan Details", $cl_full),
		array(
			$gamename=>$g_options["scripturl"] . "?game=$game",
			"Clan Rankings"=>$g_options["scripturl"] . "?mode=clans&amp;game=$game",
			"Clan Details"=>""
		),
		$clandata["name"]
	);
?>



<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="100%" colspan=2><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Clan Profile and Statistics Summary</b><?php echo $g_options["fontend_normal"];?></td>
</tr>

<tr valign="top">
	<td width="5%">&nbsp;</td>
	<td width="95%">&nbsp;<br>
		<table width="60%" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $g_options["table_border"]; ?>">
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="1" cellpadding="4">

				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
					<td><?php
						echo $g_options["font_normal"];
						echo "Home Page:";
						echo $g_options["fontend_normal"];
					?></td>
					<td><?php
						echo $g_options["font_normal"];
						if ($url = getLink($clandata["homepage"]))
						{
							echo $url;
						}
						else
						{
							echo "(Not specified.)";
						}
						echo $g_options["fontend_normal"];
					?></td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
					<td><?php
						echo $g_options["font_normal"];
						echo "Number of Members:";
						echo $g_options["fontend_normal"];
					?></td>
					<td><?php
						echo $g_options["font_normal"];
						echo $clandata["nummembers"];
						echo $g_options["fontend_normal"];
					?></td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
					<td><?php
						echo $g_options["font_normal"];
						echo "Avg. Member Points:";
						echo $g_options["fontend_normal"];
					?></td>
					<td><?php
						echo $g_options["font_normal"];
						echo $clandata["avgskill"];
						echo $g_options["fontend_normal"];
					?></td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
					<td><?php
						echo $g_options["font_normal"];
						echo "Total Kills:";
						echo $g_options["fontend_normal"];
					?></td>
					<td><?php
						echo $g_options["font_normal"];
						echo $clandata["kills"];
						echo $g_options["fontend_normal"];
					?></td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
					<td><?php
						echo $g_options["font_normal"];
						echo "Total Deaths:";
						echo $g_options["fontend_normal"];
					?></td>
					<td><?php
						echo $g_options["font_normal"];
						echo $clandata["deaths"];
						echo $g_options["fontend_normal"];
					?></td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
					<td><?php
						echo $g_options["font_normal"];
						echo "Kills per Death:";
						echo $g_options["fontend_normal"];
					?></td>
					<td><?php
						echo $g_options["font_normal"];
						if ($clandata["deaths"] != 0)
						{
							printf("%0.2f", $clandata["kills"] / $clandata["deaths"]);
						}
						else
						{
							echo "-";
						}
						echo $g_options["fontend_normal"];
					?></td>
				</tr>


				</table></td>
		</tr>

		</table></td>
</tr>

</table><p>

<?php
	if ($clandata['nummembers'] < 1) {
?>
		<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%"><a name="members"></a>
				<?php echo $g_options["font_normal"]; ?>&nbsp;
				<img src="<?php echo $g_options["imgdir"]; ?>/rightarrow.gif" width="6" height="9" border="0" align="middle" alt="downarrow.gif">
				<b>&nbsp;Clan has no active members to display.</b>
				<?php echo $g_options["fontend_normal"];?></td>
			</tr>
		</table><p>
<?php
	} else {
	flush();

	$tblMembers = new Table(
		array(
			new TableColumn(
				"lastName",
				"Name",
				"width=30&icon=player&link=" . urlencode("mode=playerinfo&amp;player=%k")
			),
			new TableColumn(
				"skill",
				"Points",
				"width=8&align=right"
			),
			new TableColumn(
				"kills",
				"Kills",
				"width=8&align=right"
			),
			new TableColumn(
				"percent",
				"Contribution&nbsp;to Clan&nbsp;Kills",
				"width=20&sort=no&type=bargraph"
			),
			new TableColumn(
				"percent",
				"%",
				"width=8&sort=no&align=right&append=" . urlencode("%")
			),
			new TableColumn(
				"deaths",
				"Deaths",
				"width=8&align=right"
			),
			new TableColumn(
				"kpd",
				"Kills per Death",
				"width=8&align=right"
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
		true,
		20,
		"members_page",
		"members_sort",
		"members_sortorder",
		"members"
	);

	$result = $db->query("
		SELECT
			playerId,
			lastName,
			skill,
			kills,
			deaths,
			IFNULL(kills/deaths, '-') AS kpd,
			(kills/" . $clandata["kills"] . ") * 100 AS percent
		FROM
			".DB_PREFIX."_Players
		WHERE
			clan=$clan
			AND hideranking = 0
		ORDER BY
			$tblMembers->sort $tblMembers->sortorder,
			$tblMembers->sort2 $tblMembers->sortorder,
			lastName ASC
		LIMIT $tblMembers->startitem,$tblMembers->numperpage
	");

	$resultCount = $db->query("
		SELECT
			COUNT(*)
		FROM
			".DB_PREFIX."_Players
		WHERE
			clan=$clan
	");

	list($numitems) = $db->fetch_row($resultCount);
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="100%" colspan="2"><a name="members"></a>
<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Members</b><?php echo $g_options["fontend_normal"];?></td>
</tr>

<tr>
	<td width="5%">&nbsp;</td>
	<td width="95%">&nbsp;<br>
	<?php
		$tblMembers->draw($result, $numitems, 100);
	?></td>
</tr>

</table><p>

<br>
<br>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr>
	<td width="100%" align="right"><br><br>
	<?php echo $g_options["font_small"]; ?><b>Admin Options:</b> <a href="<?php echo $g_options["scripturl"] . "?mode=admin&amp;task=tools_editdetails_clan&amp;id=$clan"; ?>">Edit Clan Details</a><?php echo $g_options["fontend_small"]; ?></td>
</tr>

</table><p>
<?php
	}
?>
