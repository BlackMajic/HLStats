<?php
/**
 * $Id: db.inc.php 647 2009-01-16 21:34:39Z jumpin_banana $
 * $HeadURL: https://hlstats.svn.sourceforge.net/svnroot/hlstats/trunk/hlstats/web/hlstatsinc/db.inc.php $
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
class DB_mysql {
	/**
	 * address for mysql db server
	 * eg. localhost
	 * @var string
	 */
	var $db_addr;

	/**
	 * database user for the used table
	 *
	 * @var string
	 */
	var $db_user;

	/**
	 * database passwort for database user
	 *
	 * @var string
	 */
	var $db_pass;

	/**
	 * the database to use
	 *
	 * @var string
	 */
	var $db_name;

	/**
	 * the database connection resource
	 *
	 * @var resource
	 */
	var $link;

	/**
	 * query result
	 *
	 * @var resource
	 */
	var $result;

	/**
	 * mysql_fetch_array result
	 *
	 * @var array
	 */
	var $rowdata;

	/**
	 * last inserted id
	 *
	 * @var int
	 */
	var $insert_id;

	/**
	 * changed rows for query
	 *
	 * @var int
	 */
	var $numrows;

	/**
	 * the query
	 *
	 * @var string
	 */
	var $query;

	/**
	 * init the class and create the connection resource
	 *
	 * @return DB_mysql
	 */
	function DB_mysql () {
		$this->db_addr = DB_ADDR;
		$this->db_user = DB_USER;
		$this->db_pass = DB_PASS;
		$this->db_name = DB_NAME;
		$this->db_prefix = DB_PREFIX;

		if (DB_PCONNECT == true) {
			$connectfunc = "mysql_pconnect";
		}
		else {
			$connectfunc = "mysql_connect";
		}

		$this->link = $connectfunc($this->db_addr, $this->db_user, $this->db_pass)
			or $this->error("Could not connect to database server. Check that the values of DB_ADDR, DB_USER and DB_PASS in hlstats.php are set correctly.");
		mysql_select_db($this->db_name, $this->link)
			or $this->error("Could not select database '$this->db_name'. Check that the value of DB_NAME in hlstats.php is set correctly.");
		mysql_query("SET NAMES 'utf8'");
	}

	/**
	 * moves the internal row pointer of the MySQL result associated with the specified result identifier
	 *
	 * @param int $row_number
	 * @param resource $result
	 * @return bool
	 */
	function data_seek ($row_number, $result=-1) {
		if ($result < 0) $result = $this->result;
		return mysql_data_seek($result, $row_number);
	}

	/**
	 * fetch an array
	 *
	 * @param resource $result
	 * @return array
	 */
	function fetch_array ($result=-1) {
		if ($result < 0) $result = $this->result;
		$this->rowdata = mysql_fetch_array($result);
		return $this->rowdata;
	}

	/**
	 * fetch a single row
	 *
	 * @param resource $result
	 * @return array
	 */
	function fetch_row ($result=-1) {
		if ($result < 0) $result = $this->result;
		$this->rowdata = mysql_fetch_row($result);
		return $this->rowdata;
	}

	/**
	 * free the result memory
	 *
	 * @param resource $result
	 * @return bool
	 */
	function free_result ($result=-1) {
		if ($result < 0) $result = $this->result;
		return mysql_free_result($result);
	}

	/**
	 * return last insert id
	 *
	 * @return int
	 */
	function insert_id () {
		$this->insert_id = mysql_insert_id($this->link);
		return $this->insert_id;
	}

	/**
	 * return affected rows from query
	 *
	 * @param resource $result
	 * @return int
	 */
	function num_rows ($result=-1) {
		if ($result < 0) $result = $this->result;
		$this->numrows = mysql_num_rows($result);
		return $this->numrows;
	}

	/**
	 * execute given query
	 *
	 * @param string $query
	 * @param bool $showerror
	 * @return mixed
	 */
	function query ($query, $showerror=true) {
		$this->query = $query;
		$this->result = mysql_query($query, $this->link);

		if (!$this->result) {
			if ($showerror) {
				$this->error("Bad query.");
			}
			else {
				return 0;
			}
		}

		return $this->result;
	}

	/**
	 *  Retrieves the contents of one cell from a MySQL result set.
	 *
	 * @param int $row
	 * @param string $field
	 * @param resource $result
	 * @return string
	 */
	function result ($row, $field, $result=-1) {
		if ($result < 0) $result = $this->result;

		return mysql_result($result, $row, $field);
	}

	/**
	 * display an error using the error function from functions.inc.php
	 *
	 * @param string $message
	 * @param bool $exit
	 */
	function error ($message, $exit=true) {
		error(
			"<b>Database Error</b><br>\n<br>\n" .
			"<i>Error Diagnostic:</i><br>\n$message<p>\n" .
			"<i>Server Error:</i> (" . mysql_errno() . ") " . mysql_error() . "<p>\n" .
			"<i>Last SQL Query:</i><br>\n<pre><font size=2>$this->query</font></pre>",
			$exit
		);
	}

	/**
	 * return the mysql_error
	 *
	 * @return string
	 */
	function dberror () {
		return mysql_error();
	}
}
?>
