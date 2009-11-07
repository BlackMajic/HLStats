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

	$edlist = new EditList("id", DB_PREFIX."_ClanTags", "clan", true);
	$edlist->columns[] = new EditListColumn("pattern", "Pattern", 40, true, "text", "", 64);
	$edlist->columns[] = new EditListColumn("position", "Match Position", 0, true, "select", "EITHER/EITHER;START/START only;END/END only");


	if ($_POST)
	{
		if ($edlist->update())
			message("success", l("Operation successful"));
		else
			message("warning", $edlist->error());
	}

?>

<p>
<?php echo l("Here you can define the patterns used to determine what clan a player is in. These patterns are applied to players' names when they connect or change name"); ?>.
</p>

<?php echo l("Special characters in the pattern"); ?>:<p>

<table border="0" cellspacing="0" cellpadding="4">

<tr bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
	<td><?php echo $g_options["font_small"],l('Character'),$g_options["fontend_small"]; ?></td>
	<td><?php echo $g_options["font_small"],l('Description'),$g_options["fontend_small"]; ?></td>
</tr>

<tr>
	<td><?php echo $g_options["font_normal"],'<tt>A</tt>', $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"],l('Matches one character. Character is required'), $g_options["fontend_normal"]; ?></td>
</tr>

<tr>
	<td><?php echo $g_options["font_normal"]; ?><tt>X</tt><?php echo $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"], l('Matches zero or one characters. Character is optional'), $g_options["fontend_normal"]; ?></td>
</tr>

<tr>
	<td><?php echo $g_options["font_normal"]; ?><tt>a</tt><?php echo $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"], l('Matches literal A or a'), $g_options["fontend_normal"]; ?></td>
</tr>

<tr>
	<td><?php echo $g_options["font_normal"]; ?><tt>x</tt><?php echo $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"], l('Matches literal X or x'), $g_options["fontend_normal"]; ?></td>
</tr>

</table><p>

<?php echo l('Example patterns'); ?>:<p>

<table border="0" cellspacing="0" cellpadding="4">

<tr bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
	<td><?php echo $g_options["font_small"],l('Pattern'), $g_options["fontend_small"]; ?></td>
	<td><?php echo $g_options["font_small"],l('Description'), $g_options["fontend_small"]; ?></td>
	<td><?php echo $g_options["font_small"],l('Example'), $g_options["fontend_small"]; ?></td>
</tr>

<tr>
	<td><?php echo $g_options["font_normal"]; ?><tt>[AXXXXX]</tt><?php echo $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"],l('Matches 1 to 6 characters inside square braces'), $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"]; ?><tt>[ZOOM]Player</tt><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

<tr>
	<td><?php echo $g_options["font_normal"]; ?><tt>{AAXX}</tt><?php echo $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"], l('Matches 2 to 4 characters inside curly braces'), $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"]; ?><tt>{S3G}Player</tt><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

<tr>
	<td><?php echo $g_options["font_normal"]; ?><tt>rex>></tt><?php echo $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"],l('Matches the string rex>>, REX>>, etc.'), $g_options["fontend_normal"]; ?></td>
	<td><?php echo $g_options["font_normal"]; ?><tt>REX>>Tyranno</tt><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table><p>

<?php echo l('Avoid adding patterns to the database that are too generic. Always ensure you have at least one literal (non-special) character in the pattern -- for example if you were to add the pattern "AXXA", it would match any player with 2 or more letters in their name'); ?>!
<p>

<?php echo l("The Match Position field sets which end of the player's name the clan tag is allowed to appear"); ?>.<p>

<?php

	$query = mysql_query("
		SELECT
			id,
			pattern,
			position
		FROM
			".DB_PREFIX."_ClanTags
		ORDER BY
			position,
			pattern,
			id
	");

	$edlist->draw($query);
?>

<table width="75%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" value="  <?php echo l('Apply'); ?>  " class="submit"></td>
</tr>
</table>
