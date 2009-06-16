<?php
/**
 * $Id: db.inc.php 525 2008-07-23 07:11:52Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/tags/v1.40/web/hlstatsinc/db.inc.php $
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
// DB_mysql
//
// Database abstraction class for MySQL databases.
//
class DB_mysql
{
	var $db_addr;
	var $db_user;
	var $db_pass;
	var $db_name;

	var $link;
	var $result;
	var $rowdata;
	var $insert_id;
	var $numrows;
	var $query;

	function DB_mysql ($db_addr=-1, $db_user=-1, $db_pass=-1, $db_name=-1)
	{
		if ($db_addr == -1) $db_addr = DB_ADDR;
		if ($db_user == -1) $db_user = DB_USER;
		if ($db_pass == -1) $db_pass = DB_PASS;
		if ($db_name == -1) $db_name = DB_NAME;
		if ($db_prefix == -1) $db_prefix = DB_PREFIX;

		$this->db_addr = $db_addr;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->db_name = $db_name;
		$this->db_prefix = $db_prefix;

		if (DB_PCONNECT == true)
		{
			$connectfunc = "mysql_pconnect";
		}
		else
		{
			$connectfunc = "mysql_connect";
		}

		$this->link = $connectfunc($db_addr, $db_user, $db_pass)
			or $this->error("Could not connect to database server. Check that the values of DB_ADDR, DB_USER and DB_PASS in hlstats.php are set correctly.");
		mysql_select_db($db_name, $this->link)
			or $this->error("Could not select database '$db_name'. Check that the value of DB_NAME in hlstats.php is set correctly.");
	}

	function data_seek ($row_number, $result=-1)
	{
		if ($result < 0) $result = $this->result;
		return mysql_data_seek($result, $row_number);
	}

	function fetch_array ($result=-1)
	{
		if ($result < 0) $result = $this->result;
		$this->rowdata = mysql_fetch_array($result);
		return $this->rowdata;
	}

	function fetch_row ($result=-1)
	{
		if ($result < 0) $result = $this->result;
		$this->rowdata = mysql_fetch_row($result);
		return $this->rowdata;
	}

	function free_result ($result=-1)
	{
		if ($result < 0) $result = $this->result;
		return mysql_free_result($result);
	}

	function insert_id ()
	{
		$this->insert_id = mysql_insert_id($this->link);
		return $this->insert_id;
	}

	function num_rows ($result=-1)
	{
		if ($result < 0) $result = $this->result;
		$this->numrows = mysql_num_rows($result);
		return $this->numrows;
	}

	function query ($query, $showerror=true)
	{
		global $db_debug;

		$this->query = $query;
		$this->result = mysql_query($query, $this->link);

		if ($db_debug)
		{
			echo "<p><pre>$query</pre><hr></p>";
		}

		if (!$this->result)
		{
			if ($showerror)
			{
				$this->error("Bad query.");
			}
			else
			{
				return 0;
			}
		}

		return $this->result;
	}

	function result ($row, $field, $result=-1)
	{
		if ($result < 0) $result = $this->result;

		return mysql_result($result, $row, $field);
	}

	function error ($message, $exit=true)
	{
		error(
			"<b>Database Error</b><br>\n<br>\n" .
			"<i>Server Address:</i> $this->db_addr<br>\n" .
			"<i>Server Username:</i> $this->db_user<p>\n" .
			"<i>Error Diagnostic:</i><br>\n$message<p>\n" .
			"<i>Server Error:</i> (" . mysql_errno() . ") " . mysql_error() . "<p>\n" .
			"<i>Last SQL Query:</i><br>\n<pre><font size=2>$this->query</font></pre>",
			$exit
		);
	}

	function dberror ()
	{
		return mysql_error();
	}
}
?>
