<?php
/**
 * game overview file
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

$gameData = false;
$gc = false;
// load the game
if(isset($_GET['code'])) {
	$gc = trim($_GET['code']);
	if(!empty($gc)) {
		$query = mysql_query("SELECT code,name,hidden FROM `".DB_PREFIX."_Games`
								WHERE `code` = '".mysql_escape_string($gc)."'");
		if(mysql_num_rows($query) > 0) {
			$gameData = mysql_fetch_assoc($query);
		}
	}
}

pageHeader(array(l("Admin"),l('Game Overview')), array(l("Admin")=>"index.php?mode=admin",l('Game Overview')=>''));
?>
<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a href="<?php echo "index.php?mode=admin"; ?>"><?php echo l('Back to admin overview'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l('Game Overview'); ?>: <?php echo $gameData['name']; ?></h1>
	<ul>
		<li>
			<a href="index.php?mode=admin&task=servers&gc=<?php echo $gameData['code']; ?>">
				<?php echo l('Servers'); ?>
			</a><br />
			<span class="small"><?php echo l("Add a server to accept data from."); ?></span>
		</li>
		<li>
			<a href="index.php?mode=admin&task=resetgame&gc=<?php echo $gameData['code']; ?>">
				<?php echo l('Reset'); ?>
			</a><br />
			<span class="small"><?php echo l("Reset the statistics for this game."); ?></span>
		</li>
		<li>
			<a href="index.php?mode=admin&task=actions&gc=<?php echo $gameData['code']; ?>">
				<?php echo l('Actions'); ?>
			</a><br />
			<span class="small"><?php echo l("Manage the actions."); ?></span>
		</li>
		<li>
			<a href="index.php?mode=admin&task=teams&gc=<?php echo $gameData['code']; ?>">
				<?php echo l('Teams'); ?>
			</a><br />
			<span class="small"><?php echo l("Manage the teams."); ?></span>
		</li>
		<li>
			<a href="index.php?mode=admin&task=roles&gc=<?php echo $gameData['code']; ?>">
				<?php echo l('Roles'); ?>
			</a><br />
			<span class="small"><?php echo l("Manage the roles."); ?></span>
		</li>
		<li>
			<a href="index.php?mode=admin&task=weapons&gc=<?php echo $gameData['code']; ?>">
				<?php echo l('Weapons'); ?>
			</a><br />
			<span class="small"><?php echo l("Manage the weapons."); ?></span>
		</li>
		<li>
			<a href="index.php?mode=admin&task=awardsWeapons&gc=<?php echo $gameData['code']; ?>">
				<?php echo l('Weapon Awards'); ?>
			</a><br />
			<span class="small"><?php echo l("Manage the award for each weapon."); ?></span>
		</li>
		<li>
			<a href="index.php?mode=admin&task=awardsActions&gc=<?php echo $gameData['code']; ?>">
				<?php echo l('Action Awards'); ?>
			</a><br />
			<span class="small"><?php echo l("Manage the awards for each action."); ?></span>
		</li>
	</ul>
</div>
