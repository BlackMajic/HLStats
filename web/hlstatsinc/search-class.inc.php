<?php
/**
 * $Id: search-class.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/search-class.inc.php $
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


	// Search Class

	class Search
	{
		var $query;
		var $type;
		var $game;

		var $uniqueid_string = "Unique ID";
		var $uniqueid_string_plural = "Unique IDs";


		function Search ($query, $type, $game)
		{
			$this->query = $query;
			$this->type  = $type;
			$this->game  = $game;

			if (MODE == "LAN")
			{
				$this->uniqueid_string = "IP Address";
				$this->uniqueid_string_plural = "IP Addresses";
			}
		}

		function drawForm ($getvars=array(), $searchtypes=-1)
		{
			global $g_options, $db;

			if (!is_array($searchtypes))
			{
				$searchtypes = array(
					"player"=>"Player names",
					"uniqueid"=>"Player " . $this->uniqueid_string_plural,
					"clan"=>"Clan names"
				);
			}
?>

<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr valign="top">
	<td width="100%"><?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Find a Player or Clan</b><?php echo $g_options["fontend_normal"]; ?><p>

		<form method="GET" action="<?php echo $g_options["scripturl"]; ?>">
<?php
			foreach ($getvars as $var=>$value)
			{
				echo "<input type=\"hidden\" name=\"$var\" value=\"$value\">\n";
			}
?>

		<table width="100%" border="0" cellspacing="0" cellpadding="0">

		<tr>
			<td width="10%">&nbsp;</td>
			<td width="90%">
				<table width="40%" border="0" cellspacing="0" cellpadding="2">
				<tr valign="top" bgcolor="<?php echo $g_options["table_border"]; ?>">
					<td nowrap width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?>Search For:<?php echo $g_options["fontend_normal"]; ?></td>
					<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><input type="text" name="q" size=20 maxlength=128 value="<?php echo htmlentities(strip_tags($this->query), ENT_NOQUOTES, "UTF-8"); ?>" class="textbox"></td>
					<td valign="middle" rowspan="3" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
						<input type="submit" value=" Find Now " class="submit">
					</td>
				</tr>
				<tr valign="middle">
					<td nowrap width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?>In:<?php echo $g_options["fontend_normal"]; ?></td>
					<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
						<?php
							echo getSelect("st",
								$searchtypes,
								$this->type
							);
						?></td>
				</tr>
				<tr valign="middle">
					<td nowrap width="45%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>"><?php echo $g_options["font_normal"]; ?>Game:<?php echo $g_options["fontend_normal"]; ?></td>
					<td width="55%" bgcolor="<?php echo $g_options["table_bgcolor1"]; ?>">
						<?php
							$games[""] = "(All)";

							$db->query("SELECT code, name FROM ".DB_PREFIX."_Games WHERE hidden='0' ORDER BY name");
							while ($db->fetch_row())
							{
								$games[$db->rowdata[0]] = $db->rowdata[1];
							}

							echo getSelect("game", $games, $this->game);
						?></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</form></td>
</tr>
</table><p>


<?php
		}

		function drawResults ($link_player=-1, $link_clan=-1)
		{
			global $g_options, $db;

			if ($link_player == -1) $link_player = "mode=playerinfo&amp;player=%k";
			if ($link_clan   == -1) $link_clan   = "mode=claninfo&amp;clan=%k";
?>
<table width="90%" align="center" border="0" cellspacing="0" cellpadding="0">

<tr valign="top">
	<td width="100%"><a name="results"></a>
		<?php echo $g_options["font_normal"]; ?>&nbsp;<img src="<?php echo $g_options["imgdir"]; ?>/downarrow.gif" width="9" height="6" border="0" align="middle" alt="downarrow.gif"><b>&nbsp;Search Results</b><?php echo $g_options["fontend_normal"]; ?></td>
</tr>

</table><p>
<?php
			$sr_query = ereg_replace(" ", "%", $this->query);
			$sr_query = htmlentities(strip_tags($sr_query), ENT_NOQUOTES, "UTF-8");

			if ($this->type == "player")
			{
				$table = new Table(
					array(
						new TableColumn(
							"playerId",
							"ID",
							"width=5align=right"
						),
						new TableColumn(
							"name",
							"Name",
							"width=65&icon=player&link=" . urlencode($link_player)
						),
						new TableColumn(
							"gamename",
							"Game",
							"width=30"
						)
					),
					"playerId",
					"name",
					"playerId",
					false,
					50,
					"page",
					"sort",
					"sortorder",
					"results",
					"asc"
				);

				if ($this->game)
					$andgame = "AND ".DB_PREFIX."_Players.game='" . $this->game . "'";
				else
					$andgame = "";

				$result = $db->query("
					SELECT
						".DB_PREFIX."_PlayerNames.playerId,
						".DB_PREFIX."_PlayerNames.name,
						".DB_PREFIX."_Games.name AS gamename
					FROM
						".DB_PREFIX."_PlayerNames
					LEFT JOIN ".DB_PREFIX."_Players ON
						".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerNames.playerId
					LEFT JOIN ".DB_PREFIX."_Games ON
						".DB_PREFIX."_Games.code = ".DB_PREFIX."_Players.game
					WHERE
						".DB_PREFIX."_Games.hidden='0' AND
						".DB_PREFIX."_PlayerNames.name LIKE '%$sr_query%'
						$andgame
					ORDER BY
						$table->sort $table->sortorder,
						$table->sort2 $table->sortorder
					LIMIT $table->startitem,$table->numperpage
				");

				$resultCount = $db->query("
					SELECT
						COUNT(*)
					FROM
						".DB_PREFIX."_PlayerNames
					LEFT JOIN ".DB_PREFIX."_Players ON
						".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerNames.playerId
					WHERE
						".DB_PREFIX."_PlayerNames.name LIKE '%$sr_query%'
						$andgame
				");

				list($numitems) = $db->fetch_row($resultCount);

				$table->draw($result, $numitems, 90);
			}
			elseif ($this->type == "uniqueid")
			{
				$table = new Table(
					array(
						new TableColumn(
							"uniqueId",
							$this->uniqueid_string,
							"width=15&align=right"
						),
						new TableColumn(
							"lastName",
							"Name",
							"width=50&icon=player&link=" . urlencode($link_player)
						),
						new TableColumn(
							"gamename",
							"Game",
							"width=30"
						),
						new TableColumn(
							"playerId",
							"ID",
							"width=5&align=right"
						)
					),
					"playerId",
					"lastName",
					"uniqueId",
					false,
					50,
					"page",
					"sort",
					"sortorder",
					"results",
					"asc"
				);

				if ($this->game)
					$andgame = "AND ".DB_PREFIX."_PlayerUniqueIds.game='" . $this->game . "'";
				else
					$andgame = "";

				$result = $db->query("
					SELECT
						".DB_PREFIX."_PlayerUniqueIds.uniqueId,
						".DB_PREFIX."_PlayerUniqueIds.playerId,
						".DB_PREFIX."_Players.lastName,
						".DB_PREFIX."_Games.name AS gamename
					FROM
						".DB_PREFIX."_PlayerUniqueIds
					LEFT JOIN ".DB_PREFIX."_Players ON
						".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerUniqueIds.playerId
					LEFT JOIN ".DB_PREFIX."_Games ON
						".DB_PREFIX."_Games.code = ".DB_PREFIX."_PlayerUniqueIds.game
					WHERE
						".DB_PREFIX."_Games.hidden='0' AND
						".DB_PREFIX."_PlayerUniqueIds.uniqueId LIKE '%$sr_query%'
						$andgame
					ORDER BY
						$table->sort $table->sortorder,
						$table->sort2 $table->sortorder
					LIMIT $table->startitem,$table->numperpage
				");

				$resultCount = $db->query("
					SELECT
						COUNT(*)
					FROM
						".DB_PREFIX."_PlayerUniqueIds
					LEFT JOIN ".DB_PREFIX."_Players ON
						".DB_PREFIX."_Players.playerId = ".DB_PREFIX."_PlayerUniqueIds.playerId
					WHERE
						".DB_PREFIX."_PlayerUniqueIds.uniqueId LIKE '%$sr_query%'
						$andgame
				");

				list($numitems) = $db->fetch_row($resultCount);

				$table->draw($result, $numitems, 90);
			}
			elseif ($this->type == "clan")
			{
				$table = new Table(
					array(
						new TableColumn(
							"tag",
							"Tag",
							"width=15"
						),
						new TableColumn(
							"name",
							"Name",
							"width=50&icon=clan&link=" . urlencode($link_clan)
						),
						new TableColumn(
							"gamename",
							"Game",
							"width=30"
						),
						new TableColumn(
							"clanId",
							"ID",
							"width=5&align=right"
						)
					),
					"clanId",
					"name",
					"tag",
					false,
					50,
					"page",
					"sort",
					"sortorder",
					"results",
					"asc"
				);

				if ($this->game)
					$andgame = "AND ".DB_PREFIX."_Clans.game='" . $this->game . "'";
				else
					$andgame = "";

				$result = $db->query("
					SELECT
						".DB_PREFIX."_Clans.clanId,
						".DB_PREFIX."_Clans.tag,
						".DB_PREFIX."_Clans.name,
						".DB_PREFIX."_Games.name AS gamename
					FROM
						".DB_PREFIX."_Clans
					LEFT JOIN ".DB_PREFIX."_Games ON
						".DB_PREFIX."_Games.code = ".DB_PREFIX."_Clans.game
					WHERE
						".DB_PREFIX."_Games.hidden='0' AND
						(
							".DB_PREFIX."_Clans.tag LIKE '%$sr_query%'
							OR ".DB_PREFIX."_Clans.name LIKE '%$sr_query%'
						)
						$andgame
					ORDER BY
						$table->sort $table->sortorder,
						$table->sort2 $table->sortorder
					LIMIT $table->startitem,$table->numperpage
				");

				$resultCount = $db->query("
					SELECT
						COUNT(*)
					FROM
						".DB_PREFIX."_Clans
					WHERE
						(
							tag LIKE '%$sr_query%'
							OR name LIKE '%$sr_query%'
						)
						$andgame
				");

				list($numitems) = $db->fetch_row($resultCount);

				$table->draw($result, $numitems, 90);
			}

			echo "<p><center>"
				. $g_options["font_normal"]
				. "Search results: <b>$numitems</b> items matching \"" . htmlentities(strip_tags($sr_query), ENT_NOQUOTES, "UTF-8") . "\"."
				. $g_options["fontend_normal"]
				. "</center>";
		}
	}
?>
