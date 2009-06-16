<?php
/**
 * $Id: classes.inc.php 438 2008-04-09 12:26:43Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/classes.inc.php $
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

//
// Table
//
// Generates an HTML table from a DB result.
//

class Table
{
	var $columns;
	var $keycol;
	var $sort;
	var $sortorder;
	var $sort2;
	var $page;
	var $showranking;
	var $numperpage;
	var $var_page;
	var $var_sort;
	var $var_sortorder;
	var $sorthash;

	var $columnlist;
	var $startitem;

	var $maxpagenumbers = 20;


	function Table ($columns, $keycol, $sort_default, $sort_default2,
	                $showranking=false, $numperpage=50, $var_page="page",
	                $var_sort="sort", $var_sortorder="sortorder", $sorthash="",
	                $sort_default_order="desc")
	{

		$this->columns = $columns;
		$this->keycol  = $keycol;
		$this->showranking = $showranking;
		$this->numperpage  = $numperpage;
		$this->var_page = $var_page;
		$this->var_sort = $var_sort;
		$this->var_sortorder = $var_sortorder;
		$this->sorthash = $sorthash;
		$this->sort_default_order = $sort_default_order;

		$this->page = intval($_GET[$var_page]);
		$this->sort = sanitize($_GET[$var_sort]);
		$this->sortorder = sanitize($_GET[$var_sortorder]);


		if ($this->page < 1) $this->page = 1;
		$this->startitem = ($this->page - 1) * $this->numperpage;


		foreach ($columns as $col)
		{
			if ($col->sort != "no")
				$this->columnlist[] = $col->name;
		}


		if (!is_array($this->columnlist) || !in_array($this->sort, $this->columnlist))
		{
			$this->sort = $sort_default;
		}

		if ($this->sortorder != "asc" && $this->sortorder != "desc")
		{
			$this->sortorder = $this->sort_default_order;
		}

		if ($this->sort == $sort_default2)
		{
			$this->sort2 = $sort_default;
		}
		else
		{
			$this->sort2 = $sort_default2;
		}
	}

	function draw ($result, $numitems, $width=100, $align="center")
	{
		global $g_options, $game, $db;

		$numpages = ceil($numitems / $this->numperpage);
?>

<table width="<?php echo $width; ?>%" align="<?php echo $align; ?>" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $g_options["table_border"]; ?>">

<tr>
<td><table width="100%" border="0" cellspacing="1" cellpadding="4">

	<tr valign="bottom" bgcolor="<?php echo $g_options["table_head_bgcolor"]; ?>">
<?php
		$totalwidth = 0;

		if ($this->showranking)
		{
			$totalwidth += 5;

			echo "<td width=\"5%\" align=\"right\">"
				. "<font color=\"" . $g_options["table_head_text"] . "\">"
				. $g_options["font_small"] . "Rank" . "</font>"
				. $g_options["fontend_small"] . "</td>\n";
		}

		foreach ($this->columns as $col)
		{
			$totalwidth += $col->width;

			echo "<td width=\"" . $col->width . "%\" align=\"$col->align\">";

			if ($col->sort != "no")
			{
				echo getSortArrow($this->sort, $this->sortorder, $col->name,
					$col->title, $this->var_sort, $this->var_sortorder,
					$this->sorthash);
			}
			else
			{
				echo $g_options["font_small"];
				echo "<font color=\"" . $g_options["table_head_text"] . "\">";
				echo $col->title;
				echo "</font>";
				echo $g_options["fontend_small"];
			}
			echo "</td>\n";
		}
?>
	</tr>

<?php
		if ($totalwidth != 100)
		{
			error("Warning: Column widths do not add to 100%! (=$totalwidth%)", false);
		}

		$rank = ($this->page - 1) * $this->numperpage + 1;

		while ($rowdata = $db->fetch_array($result))
		{
			echo "<tr>\n";
			$i = 0;

			if ($this->showranking)
			{
				$c = ($i % 2) + 1;
				$i++;

				echo "<td align=\"right\" bgcolor=\""
					. $g_options["table_bgcolor$c"] . "\">"
					. $g_options["font_normal"] . "$rank."
					. $g_options["fontend_normal"] . "</td>\n";
			}

			foreach ($this->columns as $col)
			{
				$c = ($i % 2) + 1;

				$cellbody = "";
				$colval = $rowdata[$col->name];

				if ($col->align != "left")
					$colalign = " align=\"$col->align\"";
				else
					$colalign = "";

				$bgcolor = $g_options["table_bgcolor$c"];



				if ($col->icon)
				{
					$cellbody = "&nbsp;";
				}

				if($col->name == "skill") {
					// check if we have a top or flop
					if($rowdata['skill'] > $rowdata['oldSkill']) {
						$cellbody .= "<img src=\"" . $g_options["imgdir"]
						. "/skill_up.gif\" width='16' height='16' hspace='4' "
						. "border='0' align=\"middle\" alt=\"$col->icon.gif\">";
					}
					elseif ($rowdata['skill'] < $rowdata['oldSkill']) {
						$cellbody .= "<img src=\"" . $g_options["imgdir"]
						. "/skill_down.gif\" width='16' height='16' hspace='4' "
						. "border='0' align=\"middle\" alt=\"$col->icon.gif\">";
					}
					else {
						$cellbody .= "<img src=\"" . $g_options["imgdir"]
						. "/skill_stay.gif\" width='16' height='16' hspace='4' "
						. "border='0' align=\"middle\" alt=\"$col->icon.gif\">";
					}
				}

				if ($col->link)
				{
					$link = ereg_replace("%k", urlencode($rowdata[$this->keycol]), $col->link);
					$cellbody .= "<a href=\"" . $g_options["scripturl"] . "?$link\">";
				}

				if ($col->icon)
				{
					$cellbody .= "<img src=\"" . $g_options["imgdir"]
						. "/$col->icon.gif\" width='16' height='16' hspace='4' "
						. "border='0' align=\"middle\" alt=\"$col->icon.gif\">";
				}

				switch ($col->type)
				{
					case "weaponimg":
						$colval = strtolower(ereg_replace("[ \r\n\t]*", "", $colval));

						$bgcolor = $g_options["table_wpnbgcolor"];

						$image = getImage("/weapons/$game/$colval");

						// check if image exists
						if ($image)
						{
							$cellbody .= "<img src=\"" . $image["url"] . "\" " . $image["size"] . " border='0' alt=\"" . strToUpper($colval) . "\">";
						}
						else
						{
							$cellbody .= $g_options["font_small"];
							$cellbody .= "<font color=\"#FFFFFF\" class=\"weapon\"><b>";
							$cellbody .= strToUpper($colval);
							$cellbody .= "</b></font>";
							$cellbody .= $g_options["fontend_small"];
						}

						break;

					case "bargraph":
						$cellbody .= "<img src=\"" . $g_options["imgdir"] . "/bar";

						if ($colval > 40)
							$cellbody .= "6";
						elseif ($colval > 30)
							$cellbody .= "5";
						elseif ($colval > 20)
							$cellbody .= "4";
						elseif ($colval > 10)
							$cellbody .= "3";
						elseif ($colval > 5)
							$cellbody .= "2";
						else
							$cellbody .= "1";

						$cellbody .= ".gif\" width=\"";

						if ($colval < 1)
							$cellbody .= "1%";
						elseif ($colval > 100)
							$cellbody .= "100%";
						else
							$cellbody .= sprintf("%d%%", $colval + 0.5);

						$cellbody .= "\" height=10 border='0' alt=\"$colval%\">";

						break;

					default:
						if ($this->showranking && $rank == 1 && $i == 1)
							$cellbody .= "<b>";

						$colval = nl2br(htmlentities($colval, ENT_COMPAT, "UTF-8"));

						if ($col->embedlink == "yes")
						{
							$colval = ereg_replace("%A%([^ %]+)%", "<a href=\"\\1\">", $colval);
							$colval = ereg_replace("%/A%", "</a>", $colval);
						}

						$cellbody .= $colval;

						if ($this->showranking && $rank == 1 && $i == 1)
							$cellbody .= "</b>";

						break;
				}

				if ($col->link)
				{
					$cellbody .= "</a>";
				}

				if ($col->append)
				{
					$cellbody .= $col->append;
				}

				echo "<td$colalign bgcolor=\"$bgcolor\">"
					. $g_options["font_normal"]
					. $cellbody
					. $g_options["fontend_normal"] . "</td>\n";

				$i++;
			}

			echo "</tr>\n\n";

			$rank++;
		}
?>
	</table></td>
</tr>

</table>
<?php
		if ($numpages > 1)
		{
?>
<p>
<table width="<?php echo $width; ?>%" align="<?php echo $align; ?>" border="0" cellspacing="0" cellpadding="0">

<tr valign="top">
<td width="100%" align="right"><?php
			echo $g_options["font_normal"];
			echo "Page: ";

			$start = $this->page - intval($this->maxpagenumbers / 2);
			if ($start < 1) $start=1;

			$end = $numpages;
			if ($end > $this->maxpagenumbers + $start-1)
				$end = $this->maxpagenumbers + $start-1;

			if ($end - $start + 1 < $this->maxpagenumbers)
				$start = $end - $this->maxpagenumbers + 1;

			if ($start < 1) $start=1;

			if ($start > 1)
			{
				if ($start > 2)
					$this->_echoPageNumber(1, "First page", "", " ...");
				else
					$this->_echoPageNumber(1, 1);
			}

			for ($i=$start; $i <= $end; $i++)
			{
				if ($i == $this->page)
				{
					echo "<b>$i</b> ";
				}
				else
				{
					$this->_echoPageNumber($i, $i);
				}

				if ($i == $end && $i < $numpages)
				{
					if ($i < $numpages - 1)
						$this->_echoPageNumber($numpages, "Last page", "... ");
					else
						$this->_echoPageNumber($numpages, 10);
				}
			}
			echo $g_options["fontend_normal"];
	?></td>
</tr>

</table><p>
<?php
		}
	}

	function _echoPageNumber ($number, $label, $prefix="", $postfix="")
	{
		global $g_options;

		echo "$prefix<a href=\"" . $g_options["scripturl"] . "?"
			. makeQueryString($this->var_page, $number);
		if ($this->sorthash)
			echo "#$this->sorthash";
		echo "\">$label</a>$postfix ";
	}
}


//
// TableColumn
//
// Data structure for the properties of a column in a Table
//

class TableColumn
{
	var $name;
	var $title;

	var $align = "left";
	var $width = 20;
	var $icon;
	var $link;
	var $sort = "yes";
	var $type = "text";
	var $embedlink = "no";

	function TableColumn ($name, $title, $attrs="")
	{
		$this->name = $name;
		$this->title= $title;

		$allowed_attrs = array(
			"align",
			"width",
			"icon",
			"link",
			"sort",
			"append",
			"type",
			"embedlink"
		);

		parse_str($attrs);

		foreach ($allowed_attrs as $a)
		{
			if (isset($$a))
			{
				$this->$a = $$a;
			}
		}
	}
}
?>
