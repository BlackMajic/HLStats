<?php
/**
 * admin options file. manage the general options
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


function whichStyle() {
	$query = mysql_query("SELECT value FROM ".DB_PREFIX."_Options WHERE keyname = 'style'");
	$data = mysql_fetch_assoc($query);
	return $data['value'];
}

pageHeader(array(l("Admin"),l('Options')), array(l("Admin")=>"index.php?mode=admin",l('Options')=>''));
?>

<div id="sidebar">
	<h1><?php echo l('Options'); ?></h1>
	<div class="left-box">
		<ul class="sidemenu">
			<li>
				<a style="color:#CC0001;" href="<?php echo "index.php?mode=admin&task=options"; ?>"><?php echo l('HLStats Options'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php"; ?>"><?php echo l('Admin Users'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php"; ?>"><?php echo l('Games'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php"; ?>"><?php echo l('Clan Tag Patterns'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php"; ?>"><?php echo l('Server Plugins'); ?></a>
			</li>
			<li>
				<a href="<?php echo "index.php"; ?>"><?php echo l('Back to game overview'); ?></a>
			</li>
		</ul>
	</div>
</div>
<div id="main">
	<h1><?php echo l('HLStats Options'); ?></h1>
	<form method="post" action="">
		<h2><?php echo l('General'); ?></h2>
		<table cellpadding="2" cellspacing="0" border="0">
			<tr>
				<th><?php echo l("Site Name"); ?></th>
				<td>
					<input type="text" name="option[sitename]" size="40"
						value="<?php echo $g_options['sitename']; ?>" />
				</td>
			</tr>
			<tr>
				<th><?php echo l("Site URL"); ?></th>
				<td>
					<input type="text" name="option[siteurl]" size="40"
						value="<?php echo $g_options['siteurl']; ?>" />
				</td>
			</tr>
			<tr>
				<th><?php echo l("Contact URL"); ?></th>
				<td>
					<input type="text" name="option[contact]" size="40"
						value="<?php echo $g_options['contact']; ?>" /><br />
					<span class="small"><?php echo l('Can be an URL or even mailto:address'); ?></span>
				</td>
			</tr>
			<tr>
				<th><?php echo l("Hide Daily Awards"); ?></th>
				<td>
					<select name="option[hideAwards]">
						<option value="0" <?php if($g_options['hideAwards'] === "0") echo 'selected="1"'; ?>><?php echo l('No'); ?></option>
						<option value="1" <?php if($g_options['hideAwards'] === "1") echo 'selected="1"'; ?>><?php echo l('Yes'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo l("Hide News"); ?></th>
				<td>
					<select name="option[hideNews]">
						<option value="0" <?php if($g_options['hideNews'] === "0") echo 'selected="1"'; ?>><?php echo l('No'); ?></option>
						<option value="1" <?php if($g_options['hideNews'] === "0") echo 'selected="1"'; ?>><?php echo l('Yes'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo l("Show chart graphics"); ?></th>
				<td>
					<select name="option[showChart]">
						<option value="0" <?php if($g_options['showChart'] === "0") echo 'selected="1"'; ?>><?php echo l('No'); ?></option>
						<option value="1" <?php if($g_options['showChart'] === "0") echo 'selected="1"'; ?>><?php echo l('Yes'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo l("Allow the use of signatures"); ?></th>
				<td>
					<select name="option[allowSig]">
						<option value="0" <?php if($g_options['allowSig'] === "0") echo 'selected="1"'; ?>><?php echo l('No'); ?></option>
						<option value="1" <?php if($g_options['allowSig'] === "0") echo 'selected="1"'; ?>><?php echo l('Yes'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo l("Allow XML interface"); ?></th>
				<td>
					<select name="option[allowXML]">
						<option value="0" <?php if($g_options['allowXML'] === "0") echo 'selected="1"'; ?>><?php echo l('No'); ?></option>
						<option value="1" <?php if($g_options['allowXML'] === "0") echo 'selected="1"'; ?>><?php echo l('Yes'); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<h2><?php echo l('Paths'); ?></h2>
		<table cellpadding="2" cellspacing="0" border="0">
			<tr>
				<th><?php echo l("Map Download URL"); ?></th>
				<td>
					<input type="text" name="option[sitename]" size="40"
						value="<?php echo $g_options['map_dlurl']; ?>" /><br />
					<span class="small">eg. http://domain.tld/maps/%GAME%/%MAP%.zip</span><br />
					<span class="small">=&gt; http://domain.tld/maps/cstrike/nuke.zip</span><br />
				</td>
			</tr>
		</table>
		<h2><?php echo l('Preset Styles'); ?></h2>
		<table cellpadding="2" cellspacing="0" border="0">
			<tr>
				<th><?php echo l("Load Preset Style"); ?></th>
				<td>
					<select name="option[style]">
						<option value="0" <?php if($g_options['allowXML'] === "0") echo 'selected="1"'; ?>><?php echo l('No'); ?></option>
						<option value="1" <?php if($g_options['allowXML'] === "0") echo 'selected="1"'; ?>><?php echo l('Yes'); ?></option>
					</select>
				</td>
			</tr>
		</table>
	</form>
</div>


<?php







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
			foreach ($this->options as $opt)
			{
				$optval = $_POST[$opt->name];

				$query = mysql_query("
					SELECT
						value
					FROM
						".DB_PREFIX."_Options
					WHERE
						keyname='$opt->name'
				");

				if (mysql_num_rows($query) == 1)
				{
					$query = mysql_query("
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
					$query = mysql_query("
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

		function changeStyle($style) {
			$query = mysql_query("SELECT keyname, `$style` FROM ".DB_PREFIX."_Style");
			while($rowdata = mysql_fetch_array($query)) {
				$key = $rowdata[0];
				$data = $rowdata[1];
				mysql_query("UPDATE ".DB_PREFIX."_Options SET value='$data' WHERE keyname='$key'");
			}
			mysql_query("UPDATE ".DB_PREFIX."_Options SET value = '$style' WHERE keyname = 'style' ");
		}
	}

	class Option {
		var $name;
		var $title;
		var $type;

		function Option ($name, $title, $type) {
			$this->name = $name;
			$this->title = $title;
			$this->type = $type;
		}

		function draw () {
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
			switch ($this->type) {
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

	$optiongroups[1] = new OptionGroup(l("General"));
	$optiongroups[1]->options[] = new Option("sitename", l("Site Name"), "text");
	$optiongroups[1]->options[] = new Option("siteurl", l("Site URL"), "text");
	$optiongroups[1]->options[] = new Option("contact", l("Contact URL"), "text");
	$optiongroups[1]->options[] = new Option("hideAwards", l("Hide Daily Awards"), "bool");
	$optiongroups[1]->options[] = new Option("hideNews", l("Hide News"), "bool");
	$optiongroups[1]->options[] = new Option("showChart", l("Show chart graphics"), "bool");
	$optiongroups[1]->options[] = new Option("allowSig", l("Allow the use of signatures"), "bool");
	$optiongroups[1]->options[] = new Option("allowXML", l("Allow XML interface"), "bool");

	$optiongroups[2] = new OptionGroup(l("Paths"));
	$optiongroups[2]->options[] = new Option("imgdir", l("Image Directory URL"), "text");
	$optiongroups[2]->options[] = new Option("imgpath", l("Image Directory Filesystem Path"), "text");
	$optiongroups[2]->options[] = new Option("map_dlurl", l("Map Download URL")."<br><font size=1>(%MAP% = map, %GAME% = gamecode)</font>", "text");

	$optiongroups[3] = new OptionGroup(l("Body Style"));
	$optiongroups[3]->options[] = new Option("body_background", l("Background Image"), "text");
	$optiongroups[3]->options[] = new Option("body_bgcolor", l("Background Colour"), "text");
	$optiongroups[3]->options[] = new Option("body_text", l("Text Colour"), "text");
	$optiongroups[3]->options[] = new Option("body_link", l("Link Colour"), "text");
	$optiongroups[3]->options[] = new Option("body_vlink", l("Visited Link Colour"), "text");
	$optiongroups[3]->options[] = new Option("body_alink", l("Active Link Colour"), "text");
	$optiongroups[3]->options[] = new Option("body_leftmargin", l("Left/Right Margin"), "text");
	$optiongroups[3]->options[] = new Option("body_topmargin", l("Top/Bottom Margin"), "text");

	$optiongroups[4] = new OptionGroup(l("Location Bar Style"));
	$optiongroups[4]->options[] = new Option("location_bgcolor", l("Background Colour"), "text");
	$optiongroups[4]->options[] = new Option("location_text", l("Text Colour"), "text");
	$optiongroups[4]->options[] = new Option("location_link", l("Link Colour"), "text");

	$optiongroups[5] = new OptionGroup(l("Table Style"));
	$optiongroups[5]->options[] = new Option("table_border", l("Border Colour"), "text");
	$optiongroups[5]->options[] = new Option("table_bgcolor1", l("Cell Background Colour 1"), "text");
	$optiongroups[5]->options[] = new Option("table_bgcolor2", l("Cell Background Colour 2"), "text");
	$optiongroups[5]->options[] = new Option("table_wpnbgcolor", l("Weapon Background Colour"), "text");
	$optiongroups[5]->options[] = new Option("table_head_bgcolor", l("Head Background Colour"), "text");
	$optiongroups[5]->options[] = new Option("table_head_text", l("Head Text Colour"), "text");

	$optiongroups[6] = new OptionGroup(l("Fonts"));
	$optiongroups[6]->options[] = new Option("font_normal", l("Normal Font Tag"), "textarea");
	$optiongroups[6]->options[] = new Option("fontend_normal", l("Normal Font Closing Tag"), "textarea");
	$optiongroups[6]->options[] = new Option("font_small", l("Small Font Tag"), "textarea");
	$optiongroups[6]->options[] = new Option("fontend_small", l("Small Font Closing Tag"), "textarea");
	$optiongroups[6]->options[] = new Option("font_title", l("Title Font Tag"), "textarea");
	$optiongroups[6]->options[] = new Option("fontend_title", l("Title Font Closing Tag"), "textarea");

	$optiongroups[7] = new OptionGroup(l("Preset Styles"));
	$optiongroups[7]->options[] = new Option("style", l("Load Preset Style"), "style_select");


	if (isset($_POST['saveOptions'])) {
		$styletype = whichStyle();
		$style = $_POST['style'];

		if($styletype != $style) {
			foreach ($optiongroups as $og) {
				$og->changeStyle($style);
			}
			message("success", l("Options updated successfully"));
		}
		else {
			foreach ($optiongroups as $og) {
				$og->update();
			}
			message("success", l("Options updated successfully"));
		}
	}


	$query = mysql_query("SELECT keyname, value FROM ".DB_PREFIX."_Options");
	while ($rowdata = mysql_fetch_assoc($query)) {
		$optiondata[$rowdata['keyname']] = $rowdata['value'];
	}

	foreach ($optiongroups as $og) {
		$og->draw();
	}
?>
<table width="75%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center"><input type="submit" name="saveOptions" value="  <?php echo l('Apply'); ?>  " class="submit"></td>
	</tr>
</table>
