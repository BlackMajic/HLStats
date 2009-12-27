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

pageHeader(
	array($gamename, l("Action Statistics")),
	array($gamename=>"%s?game=$game", l("Action Statistics")=>"")
);

$tblPlayerActions = new Table(
	array(
		new TableColumn(
			"description",
			"Action",
			"width=45&link=" . urlencode("mode=actioninfo&amp;action=%k&amp;game=$game")
		),
		new TableColumn(
			"obj_count",
			"Achieved",
			"width=25&align=right&append=+times"
		),
		new TableColumn(
			"obj_bonus",
			"Skill Bonus",
			"width=25&align=right"
		)
	),
	"code",
	"obj_count",
	"description",
	true,
	9999,
	"obj_page",
	"obj_sort",
	"obj_sortorder"
);

$queryActionsCount = mysql_query("
	SELECT COUNT(*) ac
	FROM ".DB_PREFIX."_Actions, ".DB_PREFIX."_Events_PlayerActions
	WHERE ".DB_PREFIX."_Events_PlayerActions.actionId = ".DB_PREFIX."_Actions.id
		AND ".DB_PREFIX."_Actions.game='".mysql_escape_string($game)."'");
$result = mysql_fetch_assoc($queryActionsCount);
$totalactions = $result['ac'];
mysql_free_result($queryActionsCount);

$queryActions = mysql_query("SELECT
		".DB_PREFIX."_Actions.code,
		".DB_PREFIX."_Actions.description,
		COUNT(".DB_PREFIX."_Events_PlayerActions.id) AS obj_count,
		".DB_PREFIX."_Actions.reward_player AS obj_bonus
	FROM
		".DB_PREFIX."_Actions, ".DB_PREFIX."_Events_PlayerActions, ".DB_PREFIX."_Players
	WHERE
		".DB_PREFIX."_Events_PlayerActions.playerId = ".DB_PREFIX."_Players.playerId
		AND ".DB_PREFIX."_Players.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Events_PlayerActions.actionId = ".DB_PREFIX."_Actions.id
		AND ".DB_PREFIX."_Actions.game='".mysql_escape_string($game)."'
		AND ".DB_PREFIX."_Players.hideranking = 0
	GROUP BY
		".DB_PREFIX."_Actions.id
	ORDER BY
		".$tblPlayerActions->sort." ".$tblPlayerActions->sortorder.",
		".$tblPlayerActions->sort2." ".$tblPlayerActions->sortorder."");
?>
<p>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="50%"><?php echo $g_options["font_normal"]; ?><?php echo l('From a total of'); ?> <b><?php echo $totalactions; ?></b> <?php echo l('actions'); ?> (<?php echo l('Last'); ?> <?php echo DELETEDAYS; ?> <?php echo l('days'); ?>)<?php echo $g_options["fontend_normal"]; ?></td>
	<td width="50%" align="right"><?php echo $g_options["font_normal"]; ?><?php echo l('Back to'); ?> <a href="<?php echo "index.php?game=$game"; ?>"><?php echo $gamename; ?></a><?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table>
</p>
<?php
	$tblPlayerActions->draw($queryActions, mysql_num_rows($queryActions), 90);
?>
