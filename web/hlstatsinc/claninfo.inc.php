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

$clan = '';
if(!empty($_GET["clan"])) {
	if(validateInput($_GET["clan"],'digit') === true) {
		$clan = $_GET["clan"];
	}
	else {
		error("No clan ID specified.");
	}
}

$query = mysql_query("
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
		".DB_PREFIX."_Clans.clanId=".mysql_escape_string($clan)."
		AND ".DB_PREFIX."_Players.hideranking = 0
	GROUP BY
		".DB_PREFIX."_Clans.clanId
");
if (mysql_num_rows($query) != 1)
	error("No such clan '$clan'.");

$clandata = mysql_fetch_assoc($query);
mysql_free_result($query);


$cl_name = ereg_replace(" ", "&nbsp;", htmlspecialchars($clandata["name"]));
$cl_tag  = ereg_replace(" ", "&nbsp;", htmlspecialchars($clandata["tag"]));
$cl_full = $cl_tag . " " . $cl_name;

$game = $clandata["game"];
$query = mysql_query("SELECT name FROM ".DB_PREFIX."_Games WHERE code='".mysql_escape_string($game)."'");
if (mysql_num_rows($query) != 1) {
	$gamename = ucfirst($game);
}
else {
	$result = mysql_fetch_assoc($query);
	$gamename = $result['name'];
}


pageHeader(
	array($gamename, l("Clan Details"), $cl_full),
	array(
		$gamename=>$g_options["scripturl"] . "?game=$game",
		l("Clan Rankings")=>$g_options["scripturl"] . "?mode=clans&amp;game=$game",
		l("Clan Details")=>""
	),
	$clandata["name"]
);
?>



<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%" colspan=2><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b><?php echo l('Clan Profile and Statistics Summary'); ?></b><?php echo $g_options["fontend_normal"];?></td>
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
						echo l("Home Page:");
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
							echo '(',l("Not specified."),")";
						}
						echo $g_options["fontend_normal"];
					?></td>
				</tr>

				<tr bgcolor="<?php echo $g_options["table_bgcolor2"]; ?>">
					<td><?php
						echo $g_options["font_normal"];
						echo l("Number of Members:");
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
						echo l("Avg. Member Points:");
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
						echo l("Total Kills:");
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
						echo l("Total Deaths:");
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
						echo l("Kills per Death:");
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
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<p>
<?php
	if ($clandata['nummembers'] < 1) {
?>
		<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100%"><a name="members"></a>
				<?php echo $g_options["font_normal"]; ?>&nbsp;
				<img src="<?php echo $g_options["imgdir"]; ?>/rightarrow.gif" width="6" height="9" border="0" align="middle" alt="downarrow.gif">
				<b><?php echo l('Clan has no active members to display.'); ?></b>
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

	$query = mysql_query("
		SELECT
			playerId,
			lastName,
			skill,
			kills,
			deaths,
			IFNULL(kills/deaths, '-') AS kpd,
			(kills/" . mysql_escape_string($clandata["kills"]) . ") * 100 AS percent
		FROM
			".DB_PREFIX."_Players
		WHERE
			clan=".mysql_escape_string($clan)."
			AND hideranking = 0
		ORDER BY
			".$tblMembers->sort." ".$tblMembers->sortorder.",
			".$tblMembers->sort2." ".$tblMembers->sortorder.",
			lastName ASC
		LIMIT ".$tblMembers->startitem.",".$tblMembers->numperpage."
	");

	$queryCount = mysql_query("SELECT COUNT(*) AS pc FROM ".DB_PREFIX."_Players WHERE clan=".mysql_escape_string($clan)."");
	$result = mysql_fetch_assoc($queryCount);
	$numitems = $result['pc'];
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%" colspan="2"><a name="members"></a>
<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"> <b><?php echo l('Members'); ?></b><?php echo $g_options["fontend_normal"];?></td>
</tr>
<tr>
	<td width="5%">&nbsp;</td>
	<td width="95%">&nbsp;<br>
	<?php
		$tblMembers->draw($query, $numitems, 100);
	?></td>
</tr>
</table><p>
<br>
<br>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%" align="right"><br><br>
	<?php echo $g_options["font_small"]; ?><b><?php echo l('Admin Options:'); ?></b> <a href="<?php echo $g_options["scripturl"] . "?mode=admin&amp;task=toolsEditdetailsClan&amp;id=$clan"; ?>"><?php echo l('Edit Clan Details'); ?></a><?php echo $g_options["fontend_small"]; ?></td>
</tr>
</table><p>
<?php
	}
?>
