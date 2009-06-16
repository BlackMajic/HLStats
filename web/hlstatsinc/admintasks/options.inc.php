<?php
/**
 * $Id: options.inc.php 591 2008-10-08 07:31:13Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/admintasks/options.inc.php $
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

	if ($auth->userdata["acclevel"] < 100) die ("Access denied!");

	function whichStyle() {
		global $db;

		$result = $db->query("SELECT value FROM ".DB_PREFIX."_Options WHERE keyname = 'style'");
		$data = $db->fetch_row($result);
		return $data['0'];
	}


	class OptionGroup
	{
		var $title = "";
		var $options = array();

		function OptionGroup ($title)
		{
			$this->title = $title;
		}

		function draw ()
		{
			global $g_options;
?>
<b><?php echo $this->title; ?></b><br>
<table width="75%" border="0" cellspacing="0" cellpadding="0">

<tr valign="top" bgcolor="<?php echo $g_options["table_border"]; ?>">
	<td><table width="100%" border="0" cellspacing="1" cellpadding="4">
<?php
			foreach ($this->options as $opt)
			{
				$opt->draw();
			}
?>
		</table></td>
</tr>

</table><p>
<?php
		}

		function update ()
		{
			global $db;

			foreach ($this->options as $opt)
			{
				$optval = $_POST[$opt->name];

				$result = $db->query("
					SELECT
						value
					FROM
						".DB_PREFIX."_Options
					WHERE
						keyname='$opt->name'
				");

				if ($db->num_rows($result) == 1)
				{
					$result = $db->query("
						UPDATE
							".DB_PREFIX."_Options
						SET
							value='$optval'
						WHERE
							keyname='$opt->name'
					");
				}
				else
				{
					$result = $db->query("
						INSERT INTO
							".DB_PREFIX."_Options
							(
								keyname,
								value
							)
						VALUES
						(
							'$opt->name',
							'$optval'
						)
					");
				}
			}
		}
		function changeStyle($style)
		{
			global $db;

			$result = $db->query("SELECT keyname, `$style` FROM ".DB_PREFIX."_Style");
			while($rowdata = $db->fetch_row($result))
			{
				$key = $rowdata[0];
				$data = $rowdata[1];
				$db->query("UPDATE ".DB_PREFIX."_Options SET value='$data' WHERE keyname='$key'");
			}
			$db->query("UPDATE ".DB_PREFIX."_Options SET value = '$style' WHERE keyname = 'style' ");
		}
	}

	class Option
	{
		var $name;
		var $title;
		var $type;

		function Option ($name, $title, $type)
		{
			$this->name = $name;
			$this->title = $title;
			$this->type = $type;
		}

		function draw ()
		{
			global $g_options, $optiondata;

			$styletype = whichStyle();

			if (!$g_options[$this->name]) {
				$n = ' selected="selected"';
			} else {
				$y = ' selected="selected"';
			}

			if($styletype == "grey")
			{ $gr = "selected"; }
			elseif($styletype == "black")
			{ $bl = "selected"; }
			elseif($styletype == "light_blue")
			{ $lb = "selected"; }
			elseif($styletype == "ua_style")
			{ $ua = "selected"; }
			elseif($styletype == "red")
			{ $red = "selected"; }
			elseif($styletype == "light_grey")
			{ $lg = "selected"; }
			elseif($styletype == "white")
			{ $wh = "selected"; }
			else
			{ $def = "selected"; }


?>
<tr valign="middle">
	<td width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php
	echo $g_options["font_normal"];
	echo $this->title . ":";
	echo $g_options["fontend_normal"];
?></td>
	<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php
			switch ($this->type)
			{
				case "textarea":
					echo "<textarea name=\"$this->name\" cols=35 rows=4 wrap=\"virtual\">";
					echo htmlspecialchars($optiondata[$this->name]);
					echo "</textarea>";
					break;

				case "style_select":
					echo "<select name=\"$this->name\">";
					echo "<option value=\"def\" ".$def.">Default</option>";
					echo "<option value=\"black\" ".$bl.">Black</option>";
					echo "<option value=\"grey\" ".$gr.">Grey</option>";
					echo "<option value=\"light_blue\" ".$lb.">Light Blue</option>";
					echo "<option value=\"light_grey\" ".$lg.">Light Grey</option>";
					echo "<option value=\"red\" ".$red.">Red</option>";
					echo "<option value=\"ua_style\" ".$ua.">UA Style</option>";
					echo "<option value=\"white\" ".$wh.">White</option>";
					echo "</select>";
					break;

				case "bool":
					echo '<select name="' . $this->name . '">';
					echo '<option value="0"' . $n . '>No</option>';
					echo '<option value="1"' . $y . '>Yes</option>';
					echo '</select>';
					break;

				default:
					echo "<input type=\"text\" name=\"$this->name\" size=35 value=\"";
					echo htmlspecialchars($optiondata[$this->name]);
					echo "\" class=\"textbox\" maxlength=255>";
			}
?></td>
</tr>
<?php
		}
	}

	$optiongroups = array();

	$optiongroups[1] = new OptionGroup("General");
	$optiongroups[1]->options[] = new Option("sitename", "Site Name", "text");
	$optiongroups[1]->options[] = new Option("siteurl", "Site URL", "text");
	$optiongroups[1]->options[] = new Option("contact", "Contact URL", "text");
	$optiongroups[1]->options[] = new Option("hideAwards", "Hide Daily Awards", "bool");
	$optiongroups[1]->options[] = new Option("hideNews", "Hide News", "bool");
	$optiongroups[1]->options[] = new Option("useFlash", "Use flash graphics", "bool");
	$optiongroups[1]->options[] = new Option("allowSig", "Allow the use of signatures", "bool");
	$optiongroups[1]->options[] = new Option("allowXML", "Allow XML interface", "bool");

	$optiongroups[2] = new OptionGroup("Paths");
	$optiongroups[2]->options[] = new Option("imgdir", "Image Directory URL", "text");
	$optiongroups[2]->options[] = new Option("imgpath", "Image Directory Filesystem Path", "text");
	$optiongroups[2]->options[] = new Option("map_dlurl", "Map Download URL<br><font size=1>(%MAP% = map, %GAME% = gamecode)</font>", "text");

	$optiongroups[3] = new OptionGroup("Body Style");
	$optiongroups[3]->options[] = new Option("body_background", "Background Image", "text");
	$optiongroups[3]->options[] = new Option("body_bgcolor", "Background Colour", "text");
	$optiongroups[3]->options[] = new Option("body_text", "Text Colour", "text");
	$optiongroups[3]->options[] = new Option("body_link", "Link Colour", "text");
	$optiongroups[3]->options[] = new Option("body_vlink", "Visited Link Colour", "text");
	$optiongroups[3]->options[] = new Option("body_alink", "Active Link Colour", "text");
	$optiongroups[3]->options[] = new Option("body_leftmargin", "Left/Right Margin", "text");
	$optiongroups[3]->options[] = new Option("body_topmargin", "Top/Bottom Margin", "text");

	$optiongroups[4] = new OptionGroup("Location Bar Style");
	$optiongroups[4]->options[] = new Option("location_bgcolor", "Background Colour", "text");
	$optiongroups[4]->options[] = new Option("location_text", "Text Colour", "text");
	$optiongroups[4]->options[] = new Option("location_link", "Link Colour", "text");

	$optiongroups[5] = new OptionGroup("Table Style");
	$optiongroups[5]->options[] = new Option("table_border", "Border Colour", "text");
	$optiongroups[5]->options[] = new Option("table_bgcolor1", "Cell Background Colour (1)", "text");
	$optiongroups[5]->options[] = new Option("table_bgcolor2", "Cell Background Colour (2)", "text");
	$optiongroups[5]->options[] = new Option("table_wpnbgcolor", "Weapon Background Colour", "text");
	$optiongroups[5]->options[] = new Option("table_head_bgcolor", "Head Background Colour", "text");
	$optiongroups[5]->options[] = new Option("table_head_text", "Head Text Colour", "text");

	$optiongroups[6] = new OptionGroup("Fonts");
	$optiongroups[6]->options[] = new Option("font_normal", "Normal Font Tag(s)", "textarea");
	$optiongroups[6]->options[] = new Option("fontend_normal", "Normal Font Closing Tag(s)", "textarea");
	$optiongroups[6]->options[] = new Option("font_small", "Small Font Tag(s)", "textarea");
	$optiongroups[6]->options[] = new Option("fontend_small", "Small Font Closing Tag(s)", "textarea");
	$optiongroups[6]->options[] = new Option("font_title", "Title Font Tag(s)", "textarea");
	$optiongroups[6]->options[] = new Option("fontend_title", "Title Font Closing Tag(s)", "textarea");

	$optiongroups[7] = new OptionGroup("Preset Styles");
	$optiongroups[7]->options[] = new Option("style", "Load Preset Style", "style_select");


	if ($_POST)
	{
		$styletype = whichStyle();
		$style = $_POST['style'];

		if($styletype != $style)
		{
			foreach ($optiongroups as $og)
			{
				$og->changeStyle($style);
			}
			message("success", "Options updated successfully.");
		}
		else
		{
			foreach ($optiongroups as $og)
			{
				$og->update();
			}
			message("success", "Options updated successfully.");
		}
	}


	$result = $db->query("SELECT keyname, value FROM ".DB_PREFIX."_Options");
	while ($rowdata = $db->fetch_row($result))
	{
		$optiondata[$rowdata[0]] = $rowdata[1];
	}

	foreach ($optiongroups as $og)
	{
		$og->draw();
	}
?>

<table width="75%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td align="center"><input type="submit" value="  Apply  " class="submit"></td>
</tr>
</table>
