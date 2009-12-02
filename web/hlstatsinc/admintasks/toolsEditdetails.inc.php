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
?>

&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo $task->title; ?></b><p>

<?php echo l('You can enter a player or clan ID number directly, or you can search for a player or clan'); ?>.<p>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr valign="top">
	<td width="100%"><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;<?php echo l('Jump Direct'); ?></b><?php echo $g_options["fontend_normal"]; ?><p>

		<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
		<input type="hidden" name="mode" value="admin">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="90%">
				<table width="40%" border="0" cellspacing="0" cellpadding="0">

				<tr valign="top" bgcolor="<?php echo $g_options["table_border"]; ?>">
					<td>
						<table width="100%" border="0" cellspacing="1" cellpadding="4">

						<tr valign="middle">
							<td nowrap width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?><?php echo l('Type'); ?>:<?php echo $g_options["fontend_normal"]; ?></td>
							<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
								<?php
									echo getSelect("task",
										array(
											"toolsEditdetailsPlayer"=>"Player",
											"toolsEditdetailsClan"=>"Clan"
										)
									);
								?>
							<td rowspan="2"  bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><input type="submit" value=" <?php echo l('Edit'); ?> &gt;&gt; " class="submit"></td>
						</tr>

						<tr valign="middle">
							<td nowrap width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?><?php echo l('ID Number'); ?>:<?php echo $g_options["fontend_normal"]; ?></td>
							<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><input type="text" name="id" size=15 maxlength=12 class="textbox"></td>
						</tr>

						</table>
					</td>
				</tr>

				</table></td>
		</tr>

		</table>

		</form><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table><p>

<?php
	require("hlstatsinc/search-class.inc.php");

	$sr_query = '';
	$sr_type  = 'player';
	$sr_game  = '';

	if(!empty($_GET['q'])) {
		$sr_query = sanitize($_GET["q"]);
	}

	if(!empty($_GET['st'])) {
		$sr_type  = sanitize($_GET["st"]);
	}

	if(!empty($_GET['game'])) {
		$sr_game  = sanitize($_GET["game"]);
	}


	$search = new Search($sr_query, $sr_type, $sr_game);

	$search->drawForm(array(
		"mode"=>"admin",
		"task"=>$selTask
	));

	if ($sr_query)
	{
		$search->drawResults(
			"mode=admin&task=toolsEditdetailsPlayer&id=%k",
			"mode=admin&task=toolsEditdetailsClan&id=%k"
		);
	}
?>
