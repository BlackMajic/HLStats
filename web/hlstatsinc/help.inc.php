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
pageHeader(array("Help"), array("Help"=>""));
?>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="100%" colspan=2><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Questions</b><br>
		&nbsp;<?php echo $g_options["fontend_normal"];?></td>
</tr>
<tr>
	<td width="5%">&nbsp;</td>
	<td width="95%"><?php echo $g_options["font_normal"]; ?>
		<ol>
			<li>
				<a href="#help">How and where can I get help if I need it ?</a>
			</li>
			<li>
				<a href="#players">How are players tracked? Or, why is my name listed more than once?</a>
			</li>
			<li>
				<a href="#points">How is the "points" rating calculated?</a>
			</li>
			<li>
				<a href="#weaponmods">What are all the weapon points modifiers?</a>
			</li>
			<li>
				<a href="#set">How can I set my real name, e-mail address, homepage and ICQ number?</a>
			</li>
			<li>
				<a href="#hideranking">My rank is embarrassing. How can I opt out?</a>
			</li>
		</ol>
		<?php echo $g_options["fontend_normal"]; ?></td>
</tr>

<tr>
	<td width="100%" colspan=2><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Answers</b><br>
		&nbsp;<?php echo $g_options["fontend_normal"];?>
</td>
</tr>
<tr>
	<td width="5%">&nbsp;</td>
	<td width="95%"><?php echo $g_options["font_normal"]; ?>

	<a name="help"><?php echo $g_options["font_title"]; ?><b>1. How and where can I get help if I need it ?</b></a><?php echo $g_options["fontend_title"]; ?>
	<p>
		First make sure you have <a hreaf="http://www.hlstats-community.org/index.php?p=doc" target="_blank">read the documentation</a>.<br />
		In most cases it is only a config error.<br />
		<br />
		Then you can request help in the <a href="http://forum.hlstats-community.org/" target="_blank">hlstats-community.org forum</a>. But please use the
		search function first befor you start a new topic.<br />
		Also explain as much as possible otherwise no one can help.<br />
		<br />
		There is also a <a href="https://lists.sourceforge.net/lists/listinfo/hlstats-discuss" target="_blank">mailing list</a> which will inform you
		about the latest development status and even some help.<br />
	</p>
	<a name="players"><?php echo $g_options["font_title"]; ?><b>2. How are players tracked? Or, why is my name listed more than once?</b></a><?php echo $g_options["fontend_title"]; ?><p>

<?php
	if (MODE == "NameTrack")
	{
?>
	Players are tracked by nickname. All statistics for any player using a particular name will be grouped under that name. It is not possible for a name to be listed more than once for each game.<p>
<?php
	}
	else
	{
		if (MODE == "LAN")
		{
			$uniqueid = "IP Address";
			$uniqueid_plural = "IP Addresses";
?>
	Players are tracked by IP Address. IP addresses are specific to a computer on a network.<p>
<?php
		}
		else
		{
			$uniqueid = "Unique ID";
			$uniqueid_plural = "Unique IDs";
?>
	Players are tracked by Unique ID. Unique IDs are calculated from your Half-Life CD Key. You should <b>never disclose your CD Key</b> to another player. The CD Key-to-Unique ID calculation algorithm is one-way, however, so it is quite safe to disclose your Unique ID.<p>
<?php
		}
?>

	A player may have more than one name. On the Player Rankings pages, players are shown with the most recent name they used in the game. If you click on a player's name, the Player Details page will show you a list of all other names that this player uses, if any, under the Aliases section (if the player has not used any other names, the Aliases section will not be displayed).<p>

	Your name may be listed more than once if somebody else (with a different <?php echo $uniqueid; ?>) uses the same name.<p>

	You can use the <a href="<?php echo $g_options["scripturl"]; ?>?mode=search">Search</a> function to find a player by name or <?php echo $uniqueid; ?>.<p>
<?php
	}
?>

<br>

	<a name="points"><?php echo $g_options["font_title"]; ?><b>2. How is the "points" rating calculated?</b></a><?php echo $g_options["fontend_title"]; ?><p>

	A new player has 1000 points. Every time you make a kill, you gain a certain amount of points depending on a) the victim's points rating, and b) the weapon you used. If you kill someone with a higher points rating than you, then you gain more points than if you kill someone with a lower points rating than you. Therefore, killing newbies will not get you as far as killing the #1 player. And if you kill someone with your knife, you gain more points than if you kill them with a rifle, for example.<p>

	When you are killed, you lose a certain amount of points, which again depends on the points rating of your killer and the weapon they used (you don't lose as many points for being killed by the #1 player with a rifle than you do for being killed by a low ranked player with a knife). This makes moving up the rankings easier, but makes staying in the top spots harder.<p>

	Specifically, the equations are:<p>

<pre>  Killer Points = Killer Points + (Victim Points / Killer Points)
                 &times; Weapon Modifier &times; 5

  Victim Points = Victim Points - (Victim Points / Killer Points)
                 &times; Weapon Modifier &times; 5</pre><p>

	Plus, the following point bonuses are available for completing objectives in some games:<p>

	<a name="actions"></a>
<?php
	$tblActions = new Table(
		array(
			new TableColumn(
				"gamename",
				"Game",
				"width=20&sort=no"
			),
			new TableColumn(
				"for_PlayerActions",
				"Player Action",
				"width=4&sort=no&align=center"
			),
			new TableColumn(
				"for_PlayerPlayerActions",
				"PlyrPlyr Action",
				"width=4&sort=no&align=center"
			),
			new TableColumn(
				"for_TeamActions",
				"Team Action",
				"width=4&sort=no&align=center"
			),
			new TableColumn(
				"for_WorldActions",
				"World Action",
				"width=4&sort=no&align=center"
			),
			new TableColumn(
				"description",
				"Action",
				"width=34"
			),
			new TableColumn(
				"s_reward_player",
				"Player Reward",
				"width=8&align=right"
			),
			new TableColumn(
				"s_reward_team",
				"Team Reward",
				"width=22&align=right"
			)
		),
		"id",
		"description",
		"s_reward_player",
		false,
		9999,
		"act_page",
		"act_sort",
		"act_sortorder",
		"actions",
		"asc"
	);

	$query = mysql_query("
		SELECT
			".DB_PREFIX."_Games.name AS gamename,
			".DB_PREFIX."_Actions.description,
			IF(SIGN(".DB_PREFIX."_Actions.reward_player) > 0,
				CONCAT('+', ".DB_PREFIX."_Actions.reward_player),
				".DB_PREFIX."_Actions.reward_player
			) AS s_reward_player,
			IF(".DB_PREFIX."_Actions.team != '' AND ".DB_PREFIX."_Actions.reward_team != 0,
				IF(SIGN(".DB_PREFIX."_Actions.reward_team) >= 0,
					CONCAT(".DB_PREFIX."_Teams.name, ' +', ".DB_PREFIX."_Actions.reward_team),
					CONCAT(".DB_PREFIX."_Teams.name,  ' ', ".DB_PREFIX."_Actions.reward_team)
				),
				''
			) AS s_reward_team,
			IF(for_PlayerActions='1', 'Yes', 'No') AS for_PlayerActions,
			IF(for_PlayerPlayerActions='1', 'Yes', 'No') AS for_PlayerPlayerActions,
			IF(for_TeamActions='1', 'Yes', 'No') AS for_TeamActions,
			IF(for_WorldActions='1', 'Yes', 'No') AS for_WorldActions
		FROM
			".DB_PREFIX."_Actions
		LEFT JOIN ".DB_PREFIX."_Games ON
			".DB_PREFIX."_Games.code = ".DB_PREFIX."_Actions.game
		LEFT JOIN ".DB_PREFIX."_Teams ON
			".DB_PREFIX."_Teams.code = ".DB_PREFIX."_Actions.team
			AND ".DB_PREFIX."_Teams.game = ".DB_PREFIX."_Actions.game
		ORDER BY
			".DB_PREFIX."_Actions.game ASC,
			".$tblActions->sort." ".$tblActions->sortorder.",
			".$tblActions->sort2." ".$tblActions->sortorder."
	");
	$numitems = mysql_num_rows($query);

	$tblActions->draw($query, $numitems, 100, "center");
?><p>

	<b>Note</b> The player who triggers an action may receive both the player reward and the team reward.<p>

<br>

	<a name="weaponmods"><?php echo $g_options["font_title"]; ?><b>4. What are all the weapon points modifiers?</b></a><?php echo $g_options["fontend_title"]; ?><p>

	Weapon points modifiers are used to determine how many points you should gain or lose when you make a kill or are killed by another player. Higher modifiers indicate that more points will be gained when killing with that weapon (and similarly, more points will be lost when being killed <i>by</i> that weapon). Modifiers generally range from 0.00 to 2.00.<p>

<a name="weapons"></a>
<?php
	$tblWeapons = new Table(
		array(
			new TableColumn(
				"gamename",
				"Game",
				"width=25&sort=no"
			),
			new TableColumn(
				"code",
				"Weapon",
				"width=20"
			),
			new TableColumn(
				"name",
				"Name",
				"width=40"
			),
			new TableColumn(
				"modifier",
				"Points Modifier",
				"width=15&align=right"
			)
		),
		"weaponId",
		"modifier",
		"code",
		false,
		9999,
		"weap_page",
		"weap_sort",
		"weap_sortorder",
		"weapons",
		"desc"
	);

	$query = mysql_query("
		SELECT
			".DB_PREFIX."_Games.name AS gamename,
			".DB_PREFIX."_Weapons.code,
			".DB_PREFIX."_Weapons.name,
			".DB_PREFIX."_Weapons.modifier
		FROM
			".DB_PREFIX."_Weapons
		LEFT JOIN ".DB_PREFIX."_Games ON
			".DB_PREFIX."_Games.code = ".DB_PREFIX."_Weapons.game
		ORDER BY
			game ASC,
			".$tblWeapons->sort." ".$tblWeapons->sortorder.",
			".$tblWeapons->sort2." ".$tblWeapons->sortorder."
	");

	$numitems = mysql_num_rows($query);

	$tblWeapons->draw($query, $numitems, 100, "center");
?><p>

<br>



<a name="set"><?php echo $g_options["font_title"]; ?><b>5. How can I set my real name, e-mail address, homepage and ICQ number?</b></a><?php echo $g_options["fontend_title"]; ?><p>

	Player profile options can be configured by saying the appropriate <b>SET</b> command while you are playing on a participating game server. To say commands, push your chat key and type the command text.<p>

	Syntax: say <b>/set option value</b>.<p>

	Acceptable "options" are:

	<ul>
		<li><b>realname</b><br>
		Sets your Real Name as shown in your profile.<br>
		Example: &nbsp;&nbsp; <b>/hls_set realname Joe Bloggs</b><p>

		<li><b>email</b><br>
		Sets your E-mail Address as shown in your profile.<br>
		Example: &nbsp;&nbsp; <b>/hls_set email joe@hotmail.com</b><p>

		<li><b>homepage</b><br>
		Sets your Home Page as shown in your profile.<br>
		Example: &nbsp;&nbsp; <b>/hls_set homepage http://www.geocities.com/joe/</b><p>

		<li><b>icq</b><br>
		Sets your ICQ Number as shown in your profile.<br>
		Example: &nbsp;&nbsp; <b>/hls_set icq 123456789</b><p>
	</ul>

	The server will respond with "SET command successful." If you get no response, it probably means you typed the command incorrectly.<p>

	<b>Note</b> These are not standard Half-Life console commands. If you type them in the console, Half-Life will give you an error.<p>

<br>

<a name="hideranking"><?php echo $g_options["font_title"]; ?><b>6. My rank is embarrassing. How can I opt out?</b></a><?php echo $g_options["fontend_title"]; ?><p>

	Say <b>/hls_hideranking</b> while playing on a participating game server. This will toggle you between being visible on the Player Rankings and being invisible.<p>

	<b>Note</b> You will still be tracked and you can still view your Player Details page. Use the <a href="<?php echo $g_options["scripturl"]; ?>?mode=search">Search</a> page to find yourself.<p>
	<?php echo $g_options["fontend_normal"]; ?></td>
</tr>
</table>
