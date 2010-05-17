<?php
/**
 * help overview page
 * @package HLStats
 * @author Johannes 'Banana' Keßler
 * @copyright Johannes 'Banana' Keßler
 */


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

$query = mysql_query("SELECT ".DB_PREFIX."_Games.name AS gamename,
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
	FROM ".DB_PREFIX."_Actions
	LEFT JOIN ".DB_PREFIX."_Games ON ".DB_PREFIX."_Games.code = ".DB_PREFIX."_Actions.game
	LEFT JOIN ".DB_PREFIX."_Teams ON ".DB_PREFIX."_Teams.code = ".DB_PREFIX."_Actions.team
		AND ".DB_PREFIX."_Teams.game = ".DB_PREFIX."_Actions.game
	ORDER BY ".DB_PREFIX."_Actions.game ASC,".DB_PREFIX."_Actions.description ASC");
$gameActions = array();
if(mysql_num_rows($query) > 0) {
	while($result = mysql_fetch_assoc($query)) {
		$gameActions[] = $result;
	}
	mysql_free_result($query);
}

$query = mysql_query("
		SELECT ".DB_PREFIX."_Games.name AS gamename,
			".DB_PREFIX."_Weapons.code,
			".DB_PREFIX."_Weapons.name,
			".DB_PREFIX."_Weapons.modifier
		FROM ".DB_PREFIX."_Weapons
		LEFT JOIN ".DB_PREFIX."_Games ON ".DB_PREFIX."_Games.code = ".DB_PREFIX."_Weapons.game
		ORDER BY game ASC, modifier DESC");
$weaponModifiers = array();
if(mysql_num_rows($query) > 0) {
	while($result = mysql_fetch_assoc($query)) {
		$weaponModifiers[] = $result;
	}
	mysql_free_result($query);
}

pageHeader(array(l("Help")), array(l("Help")=>""));
?>

<div id="sidebar" >
	<h1><?php echo l('Questions'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
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
				<a href="#set">How can I set my profile data ? eg. hompage or Facebook profile</a>
			</li>
			<li>
				<a href="#hideranking">My rank is embarrassing. How can I opt out?</a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1>
		<a name="help"></a>How and where can I get help if I need it ?
	</h1>
	<p>
		First make sure you have <a href="http://hlstats-community.org/Documentation.html" target="_blank">read the documentation</a>.<br />
		In most cases it is only a config error.<br />
		<br />
		Then you can request help in the <a href="http://forum.hlstats-community.org/" target="_blank">hlstats-community.org forum</a>.
		But please use the search function first befor you start a new topic. Also explain as much as possible otherwise no one can help.<br />
		<br />
		For more and quick information we a <a href="http://blog.bananas-playground.net/categories/12-HLstats" target="_blank">blog</a>
		and <a href="http://twitter.com/HLStats" target="_blank">twitter</a>.
	</p>
	<h1>
		<a name="players"></a>How are players tracked? Or, why is my name listed more than once?
	</h1>
	<p>
	<?php if (MODE == "NameTrack") { ?>
		Players are tracked by nickname. All statistics for any player using a particular name will
		be grouped under that name. It is not possible for a name to be listed more than once for each game.<br />
		<br />
	<?php } else {
			if (MODE == "LAN") {
				$uniqueid = "IP Address";
				$uniqueid_plural = "IP Addresses";
	?>
		Players are tracked by IP Address. IP addresses are specific to a computer on a network.<br />
		<br />
	<?php
			} else {
				$uniqueid = "Unique ID";
				$uniqueid_plural = "Unique IDs";
	?>
		Players are tracked by Unique ID.<br />
		<br />
	<?php } ?>
		A player may have more than one name. On the Player Rankings pages, players are shown with the most
		recent name they used in the game. If you click on a player's name, the Player Details page will
		show you a list of all other names that this player uses, if any, under the Aliases section
		(if the player has not used any other names, the Aliases section will not be displayed).<br />
		<br />
		Your name may be listed more than once if somebody else (with a different <?php echo $uniqueid; ?>)
		uses the same name.<br />
		<br />
		You can use the <a href="index.php?mode=search">Search</a> function to find a player by name or
		<?php echo $uniqueid; ?>.
	<?php } ?>
	</p>
	<h1>
		<a name="points"></a>How is the "points" rating calculated?
	</h1>
	<p>
		A new player has 1000 points. Every time you make a kill, you gain a certain amount of
		points depending on a) the victim's points rating, and b) the weapon you used. If you kill
		someone with a higher points rating than you, then you gain more points than if you kill
		someone with a lower points rating than you. Therefore, killing newbies will not get you as
		far as killing the #1 player. And if you kill someone with your knife, you gain more points
		than if you kill them with a rifle, for example.<br />
		<br />
		When you are killed, you lose a certain amount of points, which again depends on the points
		rating of your killer and the weapon they used (you don't lose as many points for being killed
		by the #1 player with a rifle than you do for being killed by a low ranked player with a knife).
		This makes moving up the rankings easier, but makes staying in the top spots harder.<br />
		<br />
		Specifically, the equations are:<br />
		<br />
<pre>Killer Points = Killer Points + (Victim Points / Killer Points)
                 &times; Weapon Modifier &times; 5
Victim Points = Victim Points - (Victim Points / Killer Points)
                 &times; Weapon Modifier &times; 5</pre>
        <br />
		Plus, the following point bonuses are available for completing objectives in some games:<br />
		<?php if(!empty($gameActions)) { ?>
		<table cellpadding="2" cellspacing="0" border="1" width="100%">
			<tr>
				<th><?php echo l('Game'); ?></th>
				<th><?php echo l('Player Action'); ?></th>
				<th><?php echo l('PlyrPlyr Action'); ?></th>
				<th><?php echo l('Team Action'); ?></th>
				<th><?php echo l('World Action'); ?></th>
				<th><?php echo l('Action'); ?></th>
				<th><?php echo l('Player Reward'); ?></th>
				<th><?php echo l('Team Reward'); ?></th>
			</tr>
			<?php
				foreach($gameActions as $a) {
					echo '<tr>';

					echo '<td>',$a['gamename'],'</td>';
					echo '<td>',$a['for_PlayerActions'],'</td>';
					echo '<td>',$a['for_PlayerPlayerActions'],'</td>';
					echo '<td>',$a['for_TeamActions'],'</td>';
					echo '<td>',$a['for_WorldActions'],'</td>';
					echo '<td>',$a['description'],'</td>';
					echo '<td>',$a['s_reward_player'],'</td>';
					echo '<td>',$a['s_reward_team'],'</td>';

					echo '<tr>';
				}
			?>
		</table>
		<?php }	?>
		<b>Note</b> The player who triggers an action may receive both the player reward and the team reward.
	</p>
	<a name="weaponmods"></a>
	<h1>What are all the weapon points modifiers?</h1>
	<p>
		Weapon points modifiers are used to determine how many points you should gain or lose
		when you make a kill or are killed by another player. Higher modifiers indicate that more
		points will be gained when killing with that weapon (and similarly, more points will be lost
		when being killed <i>by</i> that weapon). Modifiers generally range from 0.00 to 2.00.<br />
		<br />
		<?php if(!empty($weaponModifiers)) { ?>
		<table cellpadding="2" cellspacing="0" border="1" width="100%">
			<tr>
				<th><?php echo l('Game'); ?></th>
				<th><?php echo l('Weapon'); ?></th>
				<th><?php echo l('Name'); ?></th>
				<th><?php echo l('Points Modifier'); ?></th>
			</tr>
			<?php
				foreach($weaponModifiers as $w) {
					echo '<tr>';

					echo '<td>',$w['gamename'],'</td>';
					echo '<td>',$w['code'],'</td>';
					echo '<td>',$w['name'],'</td>';
					echo '<td>',$w['modifier'],'</td>';

					echo '<tr>';
				}
				?>
		</table>
		<?php }	?>
	</p>
	<a name="set"></a>
	<h1>How can I set my profile data ?</h1>
	<p>
		Player profile options can be configured by saying the appropriate <b>SET</b> command
		while you are playing on a participating game server. To say commands, push your
		chat key and type the command text.<br />
		<br />
		Syntax: say <b>/set option value</b>.<br />
		<br />
		Acceptable "options" are:
		<ul>
			<li><b>realname</b><br>
				Sets your Real Name as shown in your profile.<br>
				Example: &nbsp;&nbsp; <b>/hls_set realname Joe Bloggs</b>
			</li>
			<li><b>email</b><br>
				Sets your E-mail Address as shown in your profile.<br>
				Example: &nbsp;&nbsp; <b>/hls_set email joe@hotmail.com</b>
			</li>
			<li><b>homepage</b><br>
				Sets your Home Page as shown in your profile.<br>
				Example: &nbsp;&nbsp; <b>/hls_set homepage http://www.geocities.com/joe/</b>
			</li>
			<li><b>icq</b><br>
				Sets your ICQ Number as shown in your profile.<br>
				Example: &nbsp;&nbsp; <b>/hls_set icq 123456789</b>
			</li>
			<li><b>myspace</b><br>
				Sets your myspace page as shown in your profile.<br>
				Example: &nbsp;&nbsp; <b>/hls_set myspace http://myspace.com/name</b>
			</li>
			<li><b>facebook</b><br>
				Sets your facebook page as shown in your profile.<br>
				Example: &nbsp;&nbsp; <b>/hls_set facebook http://facebook/name</b>
			</li>
			<li><b>jabber</b><br>
				Sets your jabber ID as shown in your profile.<br>
				Example: &nbsp;&nbsp; <b>/hls_set jabber ID</b>
			</li>
			<li><b>steamprofile</b><br>
				Sets your steamprofile URL as shown in your profile.<br>
				Example: &nbsp;&nbsp; <b>/hls_set steamprofile URL</b>
			</li>
		</ul>
		The server will respond with "SET command successful." If you get no response,
		it probably means you typed the command incorrectly.<br />
		<br />
		<b>Note</b> These are not standard Half-Life console commands. If you type them in the console,
		Half-Life will give you an error.
	</p>
	<a name="hideranking"></a><h1>My rank is embarrassing. How can I opt out?</h1>
	<p>
		Say <b>/hls_hideranking</b> while playing on a participating game server.
		This will toggle you between being visible on the Player Rankings and being invisible.<br />
		<br />
		<b>Note</b> You will still be tracked and you can still view your Player Details page.
		Use the <a href="index.php?mode=search">Search</a> page to find yourself.
	</p>
</div>