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

	if ($auth->userdata["acclevel"] < 80) die ("Access denied!");

	$edlist = new EditList("id", DB_PREFIX."_Actions", "game");
	$edlist->columns[] = new EditListColumn("game", "Game", 0, true, "hidden", $gamecode);
	$edlist->columns[] = new EditListColumn("code", "Action Code", 15, true, "text", "", 64);
	$edlist->columns[] = new EditListColumn("for_PlayerActions", "Player Action", 0, false, "checkbox");
	$edlist->columns[] = new EditListColumn("for_PlayerPlayerActions", "PlyrPlyr Action", 0, false, "checkbox");
	$edlist->columns[] = new EditListColumn("for_TeamActions", "Team Action", 0, false, "checkbox");
	$edlist->columns[] = new EditListColumn("for_WorldActions", "World Action", 0, false, "checkbox");
	$edlist->columns[] = new EditListColumn("reward_player", "Player Points Reward", 4, false, "text", "0");
	$edlist->columns[] = new EditListColumn("reward_team", "Team Points Reward", 4, false, "text", "0");
	$edlist->columns[] = new EditListColumn("team", "Team", 0, false, "select", DB_PREFIX."_Teams.name/code/game='$gamecode'",0,false);
	$edlist->columns[] = new EditListColumn("description", "Action Description", 23, true, "text", "", 128);


	if ($_POST)
	{
		if ($edlist->update())
			message("success", "Operation successful.");
		else
			message("warning", $edlist->error());
	}

?>

<?php echo l('You can make an action map-specific by prepending the map name and an underscore to the Action Code'); ?>.
<?php echo l('For example, if the map'), " <b>rock2</b> ", l('has an action'), " <b>goalitem</b> ", l('then you can either make the action code just'); ?>
<?php echo " <b>goalitem</b> ", l('(in which case it will match all maps) or you can make it'); ?>
<?php echo " <b>rock2_goalitem</b> ", l('to match only on the "rock2" map'); ?>,'.<p>';

<?php

	$result = mysql_query("
		SELECT
			id,
			code,
			reward_player,
			reward_team,
			team,
			description,
			for_PlayerActions,
			for_PlayerPlayerActions,
			for_TeamActions,
			for_WorldActions
		FROM
			".DB_PREFIX."_Actions
		WHERE
			game='$gamecode'
		ORDER BY
			code ASC
	");

	$edlist->draw($result);
?>

<table width="75%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" value=" <?php echo l('Apply'); ?> " class="submit"></td>
</tr>
</table>
