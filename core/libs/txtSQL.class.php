<?php
/************************************************************************
* txtSQL                                                 ver. 2.2 Final *
*************************************************************************
* A php class of functions which simulats, and acts almost like a mySQL *
* service                                                               *
*-----------------------------------------------------------------------*
* This program is free software; you can redistribute it and/or         *
* modify it under the terms of the GNU General Public License           *
* as published by the Free Software Foundation; either version 2        *
* of the License, or (at your option) any later version.                *
*                                                                       *
* This program is distributed in the hope that it will be useful,       *
* but WITHOUT ANY WARRANTY; without even the implied warranty of        *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
* GNU General Public License for more details.                          *
*                                                                       *
* You should have received a copy of the GNU General Public License     *
* along with this program; if not, write to the Free Software           *
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307 *
* USA.                                                                  *
*-----------------------------------------------------------------------*
*  NOTE- Tab size in this file: 8 spaces/tab                            *
*-----------------------------------------------------------------------*
*  ©2003 Faraz Ali, ChibiGuy Production [http://txtsql.sourceforge.net] *
*  File: txtsql.core.php                                                *
************************************************************************/

/**
 * Extracts data from a flatfile database via a limited SQL
 *
 * @package txtSQL
 * @author Faraz Ali <Faraz87@comcast.net>
 * @version 2.2 Final
 * @access public
 */
class txtSQL
{
	/**
	 * If set to true, prints all errors and warnings
	 * @var bool
	 * @access public
	 * @see strict()
	 */
	var $_STRICT        = TRUE;

	/**
	 * Holds the path of the txtSQL data directory
	 * @var string
	 * @access private
	 */
	var $_LIBPATH       = NULL;

	/**
	 * Holds the name of the currently logged in user
	 * @var string
	 * @access private
	 * @see _isconnected()
	 */
	var $_USER          = NULL;

	/**
	 * Holds the md5() hash of the password of the currently logged in user
	 * @var string
	 * @access private
	 * @see _isconnected()
	 * @see disconnect()
	 */
	var $_PASS          = NULL;

	/**
	 * Contains a cache of any files that have been read to increase execution time
	 * @var array
	 * @access private
	 * @see readFile()
	 */
	var $_CACHE         = array();

	/**
	 * Holds the name of the currently selected database
	 * @var string
	 * @access private
	 * @see selectdb()
	 */
	var $_SELECTEDDB    = NULL;

	/**
	 * Holds the number of queries sent to txtSQL
	 * @var int
	 * @access private
	 * @see query_count()
	 */
	var $_QUERYCOUNT    = 0;

	/**
	 * The constructor of the txtSQL class
	 * @param string $path The path to which the databases are located
	 * @return void
	 * @access public
	 */
	function txtSQL ($config)
	{
		$this->_LIBPATH = $config['path'];
		
		$this->connect($config['dbuser'],$config['dbpass']);
		$sql->selectdb ($config['dbname']);
		return TRUE;
	}

	/**
	 * Connects a user to the txtSQL service
	 * @param string $user The username of the user
	 * @param string $pass The corressponding password of the user
	 * @return void
	 * @access public
	 */
	function connect ($user, $pass)
	{
		/* Check to see if our data exists */
		if ( !is_dir($this->_LIBPATH) )
		{
			$this->_error(E_USER_ERROR, 'Invalid data directory specified');
		}

		/* Instantiate parser and core class */
		$this->_query            = new txtSQLCore;
		$this->_query->_LIBPATH  = $this->_LIBPATH;

		/* Read in the user/pass information */
		if ( ($DATA = $this->_readFile("$this->_LIBPATH/txtsql/txtsql.MYI")) === FALSE )
		{
			$this->_error(E_USER_WARNING, 'Database file is corrupted!');
			return FALSE;
		}
		$this->_data = $DATA;

		/* Check to see if the username exists, and for a matching password */
		if ( !isset($DATA[strtolower($user)]) || $DATA[strtolower($user)] != md5($pass) )
		{
			$this->_error(E_USER_NOTICE, 'Access denied for user \''.$user.'\' (using password: '.(!empty($pass)?'yes':'no').')');
			return FALSE;
		}

		$this->_USER = $user;
		$this->_PASS = $pass;
		return TRUE;
	}

	/**
	 * Disconnects a user from the txtSQL Service
	 * @return void
	 * @access public
	 */
	function disconnect ()
	{
		/* Check to see that we are already connected */
		if( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Can only disconnect when connected!');
			return FALSE;
		}

		/* Unset user, pass variables
		 * Then remove the core executer object and the parser object
		 * And finally return */
		unset($this->_USER, $this->_PASS, $this->_query);
		return TRUE;
	}

	/**
	 * Selects rows of information from a selected database and a table
	 * that fits the given 'where' clause
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'select', 'where', 'limit'
	 *                         and 'orderby'
	 * @return mixed $results An array that txtSQL returns that matches the given criteria
	 * @access public
	 */
	function select ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->select($arguments);
	}

	/**
	 * Inserts a new row into a table with the given information
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'values'
	 * @return int $inserted The number of rows inserted into the table
	 * @access public
	 */
	function insert ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->insert($arguments);
	}

	/**
	 * Updates a row that matches a 'where' clause, with new information
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'where', 'limit',
	 *                         and 'values'
	 * @return int $inserted The number of rows updated
	 * @access public
	 */
	function update ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->update($arguments);
	}

	/**
	 * Deletes a row from a table that matches a 'where' clause
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'where', 'limit'
	 * @return int $inserted The number of rows deleted
	 * @access public
	 */
	function delete ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->delete($arguments);
	}

	/**
	 * Returns a list containing the current valid txtSQL databases
	 * @return mixed $databases A list containing the databases
	 * @access public
	 */
	function showdbs ()
	{
		/* Check for a connection, and valid arguments */
		$this->_validate(array());
		$this->_QUERYCOUNT++;

		return $this->_query->showdatabases();
	}

	/**
	 * Creates a new database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db'
	 * @return void
	 * @access public
	 */
	function createdb ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->createdatabase($arguments);
	}

	/**
	 * Drops a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db'
	 * @return void
	 * @access public
	 */
	function dropdb ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->dropdatabase($arguments);
	}

	/**
	 * Renames a database
	 * @param mixed $arguments The arguments in form of "[old db name], [new db name]"
	 * @return void
	 * @access public
	 */
	function renamedb ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->renamedatabase($arguments);
	}

	/**
	 * Returns an array containing a list of tables inside of a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db'
	 * @return mixed $tables   An array with a list of tables
	 * @access public
	 */
	function showtables ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->showtables($arguments);
	}

	/**
	 * Creates a new table with the given criteria inside a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'columns'
	 * @return int $deleted The number of rows deleted
	 * @access public
	 */
	function createtable ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->createtable($arguments);
	}

	/**
	 * Drops a table from a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table'
	 * @return void
	 * @access public
	 */
	function droptable ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->droptable($arguments);
	}

	/**
	 * Alters a database by working with its columns
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table', 'action',
	 *                         'name', and 'values'
	 * @return void
	 * @access public
	 */
	function altertable ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->altertable($arguments);
	}

	/**
	 * Returns a description of a table using an array
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *                         where $key can be 'db', 'table'
	 * @return int $columns An array with the description of a table
	 * @access public
	 */
	function describe ($arguments)
	{
		/* Check for a connection, and valid arguments */
		$this->_validate($arguments);
		$this->_QUERYCOUNT++;

		return $this->_query->describe($arguments);
	}

	/**
	 * Checks for a connection, and valid arguments
	 * @param mixed $arguments The arguments to validify
	 * @return void
	 * @access private
	 */
	function _validate ($arguments)
	{
		/* Check to see user is connected */
		if ( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Can only perform queries when connected!');
			return FALSE;
		}

		/* Arguments have to be inside of an array */
		if ( !empty($arguments) && !is_array($arguments) )
		{
			$this->_error(E_USER_ERROR, 'txtSQL can only accept arguments in an array');
		}

		return TRUE;
	}

	/**
	 * Evaluates a query with manually inputted arguments.
	 * The $action can be either 'show databases', 'create databases', 'drop database', 'rename database'
	 * 'show tables', 'create table', 'drop table', 'alter table', 'describe', 'select', 'insert', 'delete',
	 * and 'insert'. See the readme for more information.
	 *
	 * @param string $action The command txtSQL is to perform
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 * @return mixed $results The results that txtSQL returned
	 * @access public
	 */
	function execute ($action, $arguments = NULL)
	{
		/* Check to see user is connected */
		if ( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Can only perform queries when connected!');
			return FALSE;
		}

		/* If there is no action */
		if ( empty($action) )
		{
			$this->_error(E_USER_NOTICE, 'You have an error in your txtSQL query');
			return FALSE;
		}

		/* Arguments have to be inside of an array */
		if ( !empty($arguments) && !is_array($arguments) )
		{
			$this->_error(E_USER_ERROR, 'txtSQL Can only accept arguments in an array');
		}

		/* Depending on what type of action it is, then perform right query */
		switch ( strtolower($action) )
		{
			/* ----- Database Related ----- */
			case 'show databases':
				$results = $this->_query->showdatabases();
				break;
			case 'create database':
				$results = $this->_query->createdatabase($arguments);
				break;
			case 'drop database':
				$results = $this->_query->dropdatabase($arguments);
				break;
			case 'rename database':
				$results = $this->_query->renamedatabase($arguments);
				break;

			/* ----- Table Related ----- */
			case 'show tables':
				$results = $this->_query->showtables($arguments);
				break;
			case 'create table':
				$results = $this->_query->createtable($arguments);
				break;
			case 'drop table':
				$results = $this->_query->droptable($arguments);
				break;
			case 'alter table':
				$results = $this->_query->altertable($arguments);
				break;
			case 'describe':
				$results = $this->_query->describe($arguments);
				break;

			/* ----- Main functions ----- */
			case 'select':
				$results = $this->_query->select($arguments);
				break;
			case 'insert':
				$results = $this->_query->insert($arguments);
				break;
			case 'update':
				$results = $this->_query->update($arguments);
				break;
			case 'delete':
				$results = $this->_query->delete($arguments);
				break;

			default:
				$this->_error(E_USER_NOTICE, 'Unknown action: '.$action);
				return FALSE;
		}

		/* Return whatever results we got back */
		$this->_QUERYCOUNT++;
		return isset($results) ? $results : '';
	}

	/**
	 * Turns strict property of txtSQL off/on
	 * @param bool $strict The value of the strict property
	 * @return void
	 * @access public
	 */
	function strict ($strict = FALSE)
	{
		$strict        = (bool) $strict;
		$this->_STRICT = $strict;

		if ( $this->_isconnected() )
		{
			$this->_query->strict($strict);
		}
		return TRUE;
	}

	/**
	 * To set username and/or passwords, or create/delete users
	 * @param string $action The action to perform (add, drop, edit)
	 * @param string $user The username to be added/modified
	 * @param string $pass The password of the username
	 * @param string $pass1 The new password of the username (optional if editing)
	 * @return void
	 * @access public
	 */
	function grant_permissions($action, $user, $pass = NULL, $pass1 = NULL)
	{
		/* Are we connected? */
		if ( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Not connected');
			return FALSE;
		}

		/* Can only work with strings */
		if ( !is_string($action) || !is_string($user) || (!empty($pass) && !is_string($pass)) || (!empty($pass1) && !is_string($pass1)) )
		{
			$this->_error(E_USER_NOTICE, 'The arguments must be a string');
			return FALSE;
		}

		/* Read in user database */
		if ( ($DATA = $this->_readFile("$this->_LIBPATH/txtsql/txtsql.MYI")) === FALSE )
		{
			$this->_error(E_USER_WARNING, 'Database file is corrupted!');
			return FALSE;
		}

		/* Need a username */
		if ( empty($user) )
		{
			$this->_error(E_USER_NOTICE, 'Forgot to input username');
			return FALSE;
		}

		/* Perform the correct operation */
		switch ( strtolower($action) )
		{
			case 'add':
				if ( isset($DATA[strtolower($user)]) )
				{
					$this->_error(E_USER_NOTICE, 'User already exists');
					return FALSE;
				}
				$DATA[strtolower($user)] = md5($pass);
				break;
			case 'drop':
				if ( strtolower($user) == strtolower($this->_USER) )
				{
					$this->_error(E_USER_NOTICE, 'Can\'t drop yourself');
					return FALSE;
				}
				elseif ( strtolower($user) == 'root' )
				{
					$this->_error(E_USER_NOTICE, 'Can\'t drop user root');
					return FALSE;
				}
				elseif ( !isset($DATA[strtolower($user)]) )
				{
					$this->_error(E_USER_NOTICE, 'User doesn\'t exist');
					return FALSE;
				}
				elseif ( md5($pass) != $DATA[strtolower($user)] )
				{
					$this->_error(E_USER_NOTICE, 'Incorrect password');
					return FALSE;
				}
				unset($DATA[strtolower($user)]);
				break;
			case 'edit':
				if ( !isset($DATA[strtolower($user)]) )
				{
					$this->_error(E_USER_NOTICE, 'User doesn\'t exist');
					return FALSE;
				}
				if ( md5($pass) != $DATA[strtolower($user)] )
				{
					$this->_error(E_USER_NOTICE, 'Incorrect password');
					return FALSE;
				}
				$DATA[strtolower($user)] = md5($pass1);
				break;
			default: $this->_error(E_USER_NOTICE, 'Invalid action specified');
			         return FALSE;
		}

		/* Save the new information */
		$fp = @fopen("$this->_LIBPATH/txtsql/txtsql.MYI", 'w') or $this->_error(E_USER_FATAL,  "Couldn't open $this->_LIBPATH/txtsql/txtsql.MYI for writing");
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, serialize($DATA))                   or $this->_error(E_USER_FATAL,  "Couldn't write to $this->_LIBPATH/txtsql/txtsql.MYI");
		      @flock($fp, LOCK_UN);
		      @fclose($fp)                                     or $this->_error(E_USER_NOTICE, "Error closing $this->_LIBPATH/txtsql/txtsql.MYI");

		/* Save it in the cache */
		$this->_CACHE["$this->_LIBPATH/txtsql/txtsql.MYI"] = $DATA;
		return TRUE;
	}

	/**
	 * Returns an array filled with a list of current txtSQL users
	 * @return mixed $users
	 * @access public
	 */
	function getUsers ()
	{
		/* Are we connected? */
		if ( !$this->_isconnected() )
		{
			$this->_error(E_USER_NOTICE, 'Not connected');
			return FALSE;
		}

		/* Read in user database */
		if ( ($DATA = $this->_readFile("$this->_LIBPATH/txtsql/txtsql.MYI")) === FALSE )
		{
			$this->_error(E_USER_WARNING, 'Database file is corrupted!');
			return FALSE;
		}

		$users = array();
		foreach ( $DATA as $key => $value )
		{
			$users[] = $key;
		}
		return $users;
	}

	/**
	 * Check whether a database is locked or not
	 * @param string $db The database to check
	 * @return bool $locked Whether it is locked or not
	 * @access public
	 */
	function isLocked ($db)
	{
		if ( !$this->_dbexist($db) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$db.' doesn\'t exist');
			return FALSE;
		}
		return is_file("$this->_LIBPATH/$db/txtsql.lock") ? TRUE : FALSE;
	}

	/**
	 * To put a file lock on the database
	 * @param string $db The database to have a file lock placed on
	 * @return void
	 * @access public
	 */
	function lockdb ($db)
	{
		/* Make sure that the user is connected */
		if ( !$this->_isConnected() )
		{
			$this->_error(E_USER_NOTICE, 'You must be connected');
			return FALSE;
		}
		elseif ( $this->isLocked($db) )
		{
			$this->_error(E_USER_NOTICE, 'Lock for database '.$db.' already exists');
			return FALSE;
		}

		$fp = fopen("$this->_LIBPATH/$db/txtsql.lock", 'a') or $this->_error(E_USER_ERROR, 'Err1or creating a lock for database '.$db);
		      fclose($fp) or $this->_error(E_USER_ERROR, 'Error creating a lock for database '.$db);

		return TRUE;
	}

	/**
	 * To remove a file lock from the database
	 * @param string $db The database to have a file lock removed from
	 * @return void
	 * @access public
	 */
	function unlockdb ($db)
	{
		/* Make sure that the user is connected */
		if ( !$this->_isConnected() )
		{
			$this->_error(E_USER_NOTICE, 'You must be connected');
			return FALSE;
		}
		elseif ( !$this->isLocked($db) )
		{
			$this->_error(E_USER_NOTICE, 'Lock for database '.$db.' doesn\'t exist');
			return FALSE;
		}

		if ( !@unlink("$this->_LIBPATH/$db/txtsql.lock") )
		{
			$this->_error(E_USER_ERROR, 'Error removing lock for database '.$db);
		}
		return TRUE;
	}

	/**
	 * To select a database for txtsql to use as a default
	 * @param string $db The name of the database that is to be selected
	 * @return void
	 * @access public
	 */
	function selectdb ($db)
	{
		/* Valid db name? */
		if ( empty($db) )
		{
			$this->_error(E_USER_NOTICE, 'Cannot select database '.$db);
			return FALSE;
		}

		/* Does it exist? */
		if ( !$this->_dbexist($db) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$db.' doesn\'t exist');
			return FALSE;
		}

		/* Select the database */
		$this->_SELECTEDDB = $db;
		$this->_query->_SELECTEDDB = $db;
		return TRUE;
	}

	/**
	 * An alias (but public) of the private function _tableexist()
	 * @param $table Table to be checked for existence
	 * @param $db The database the table is in
	 * @return bool Whether it exists or not
	 */
	function table_exists ($table, $db)
	{
		return $this->_tableexist($table, $db);
	}

	/**
	 * An alias (public) of the private function _dbexist()
	 * @param $table DB to be checked for existence
	 * @return bool Whether it exists or not
	 */
	function db_exists ($db)
	{
		return $this->_dbexist($db);
	}

	/**
	 * To retrieve the number of records inside of a table
	 * @param string $table The name of the table
	 * @param string $database The database the table is inside of (optional)
	 * @return int $count The number of records in the table
	 * @access public
	 */
	function table_count ($table, $database=NULL)
	{
		/* Inside of another database? */
		if ( !empty($database) )
		{
			if ( !$this->selectdb($database) )
			{
				return FALSE;
			}
		}

		/* No database or no table specified means that we stop here */
		if ( empty($this->_SELECTEDDB) || empty($table) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Does table exist? */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";
		if ( !is_file($filename.'.MYD') || !is_file($filename.'.FRM') )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$table.' doesn\'t exist');
			return FALSE;
		}

		/* Read in the table's records */
		if ( ($rows = @file($filename.'.MYD')) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$table.' doesn\'t exist');
			return FALSE;
		}
		$count = substr($rows[0], 2, strpos($rows[0], '{') - 3);

		/* Return the count */
		return $count;
	}

	/**
	 * To retrieve the last ID generated by an auto_increment field in a table
	 * @param string $table The name of the table
	 * @param string $db The database the table is inside of (optional)
	 * @return string $column Get the last ID generated by this column instead of the priamry key (optional)
	 * @access public
	 */
	function last_insert_id( $table, $db = '', $column = '' )
	{
		/* Select a database if one is given */
		if ( !empty($db) )
		{
			if ( !$this->selectdb($db) )
			{
				return FALSE;
			}
		}

		/* Check for a selected database */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Read in the column definitions */
		if ( ( $cols = $this->_readFile("$this->_LIBPATH/$this->_SELECTEDDB/$table.FRM") ) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table "'.$table.'" doesn\'t exist');
			return FALSE;
		}

		/* Check for a valid column that is auto_increment */
		if ( !empty($column) )
		{
			if ( $this->_getColPos($column, $cols) === FALSE )
			{
				$this->_error(E_USER_NOTICE, 'Column '.$column.' doesn\'t exist');
				return FALSE;
			}
			elseif ( $cols[$column]['auto_increment'] != 1 )
			{
				$this->_error(E_USER_NOTICE, 'Column '.$column.' is not an auto_increment field');
				return FALSE;
			}

			$cols['primary'] = $column;
		}

		/* If we are using the primary key, make sure it exists */
		elseif ( empty($cols['primary']) && empty($column) )
		{
			$this->_error(E_USER_NOTICE, 'There is no primary key defined for table "'.$table.'"');
			return FALSE;
		}

		return $cols[$cols['primary']]['autocount'];
	}

	/**
	 * To return the number of queries sent to txtSQL
	 * @return int $_QUERYCOUNT
	 * @access public
	 */
	function query_count()
	{
		return $this->_QUERYCOUNT;
	}

	/**
	 * To print the last error that occurred
	 * @return void
	 * @access public
	 */
	function last_error()
	{
		if ( !empty($this->_query->_ERRORS) )
		{
			print '<pre>'.$this->_query->_ERRORSPLAIN[count($this->_query->_ERRORS)-1].'</pre>';
		}
		elseif ( !empty($this->_ERRORS) )
		{
			print '<pre>'.$this->_ERRORSPLAIN[count($this->_ERRORS)-1].'</pre>';
		}
	}

	/**
	 * To return the last error that occurred
	 * @return string $error The last error
	 * @access public
	 */
	function get_last_error()
	{
		if ( !empty($this->_query->_ERRORS) )
		{
			return $this->_query->_ERRORSPLAIN[count($this->_query->_ERRORS)-1];
		}
		elseif ( !empty($this->_ERRORS) )
		{
			return $this->_ERRORSPLAIN[count($this->_ERRORS)-1];
		}		
	}

	/**
	 * To print any errors that occurred during script execution so far
	 * @return void
	 * @access public
	 */
	function errordump()
	{
		/* No errors? */
		if ( empty($this->_ERRORS) && empty($this->_query->_ERRORS) )
		{
			echo 'No errors occurred during script execution';
			return TRUE;
		}

		/* Errors during this part of script */
		if ( !empty($this->_ERRORS) )
		{
			foreach ( $this->_ERRORS as $key => $value )
			{
				echo 'ERROR #['.$key.'] '.$value;
			}
		}

		/* Errors during query execution portion */
		elseif ( !empty($this->_query->_ERRORS) )
		{
			foreach ( $this->_query->_ERRORS as $key => $value )
			{
				echo 'ERROR #['.$key.'] '.$value;
			}
		}

		return TRUE;
	}

	/**
	 * Removes any cache that is being stored
	 * @return void
	 * @access public
	 */
	function emptyCache()
	{
		$this->_CACHE = array();
		return TRUE;
	}

	// PRIVATE FUNCTIONS //////////////////////////////////////////////////////////////////////////////////////
	/**
	 * To retrieve the number of records inside of a table
	 * @param int $errno The error type (number form)
	 * @param string $errstr The error message that will be shown
	 * @param string $errtype Prints this string before the message
	 * @return void
	 * @access private
	 */
	function _error ($errno, $errstr, $errtype=NULL)
	{
		/* If this error is not an internal error, then generate a backtrace
		 * to the line that originally caused the error */
		$backtrace = array_reverse(@debug_backtrace());
		$errfile   = $backtrace[0]['file'];
		$errline   = $backtrace[0]['line'];

		/* Determine what kind of error this is, so we can display it. */
		switch ($errno)
		{
			case E_USER_ERROR:
				$type = 'Fatal Error';
				break;
			case E_USER_NOTICE:
				$type = "Warning";
				break;
			default:
				$type = "Error";
				break;
		}
		$type = isset($errtype) ? $errtype : $type;

		/* Print the message to the screen, if strict is on */
		$this->_ERRORSPLAIN[] = $errstr;
		$errormsg = "<BR />\n<B>txtSQL $type:</B> $errstr in <B>$errfile</B> on line <B>$errline</B>\n<BR /></DIV>";
		$this->_ERRORS[] = $errormsg;
		if ( $this->_STRICT === TRUE )
		{
			echo $errormsg;
		}

		/* If this is a fatal error, then we are forced to exit and stop execution */
		if ( $errno == E_USER_ERROR )
		{
			exit;
		}
		return TRUE;
	}

	/**
	 * To Read a file into a string and return it
	 * @param string $filename The path to the file needed to be opened
	 * @param bool $useCache Whether to save/retrieve this file from a cache
	 * @param bool $unserialize Whether to unserialize the string or not
	 * @return string $contents The file's contents
	 * @access private
	 */
	function _readFile ( $filename, $useCache = TRUE, $unserialize = TRUE )
	{
		if ( is_file($filename) )
		{
			if ( $useCache === TRUE )
			{
				if ( isset($this->_CACHE[$filename]) )
				{
					return $this->_CACHE[$filename];
				}
			}

			if ( ( $contents = @implode('', @file($filename)) ) !== FALSE )
			{
				if ( $unserialize === TRUE )
				{
					if ( ( $contents = @unserialize($contents) ) === FALSE )
					{
						return FALSE;
					}
				}

				if ( $useCache === TRUE )
				{
					$this->_CACHE[$filename] = $contents;
				}
				return $contents;
			}
		}
		return FALSE;
	}

	/**
	 * Check to see whether a user is connected or not
	 * @return bool $connected Whether the user is connected or not
	 * @access private
	 */
	function _isconnected ()
	{
		/* If either one of the user or pass vars are empty, then return false; */
		if ( empty($this->_USER) )
		{
			return FALSE;
		}

		/* Are we authenticated? */
		if ( $this->_data[strtolower($this->_USER)] != md5($this->_PASS) )
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * To check whether a database exists or not
	 * @param string $db The name of the database
	 * @return bool Whether the db exists or not
	 * @access private
	 */
	function _dbexist ($db)
	{
		return is_dir("$this->_LIBPATH/$db") ? TRUE : FALSE;
	}

	/**
	 * To check whether a table exists or not
	 * @param string $table The name of the table
	 * @param string $db The name of the database the table is in
	 * @return bool Whether the db exists or not
	 * @access private
	 */
	function _tableexist ($table, $db)
	{
		/* Check to see if the database exists */
		if ( !empty($db) )
		{
			if ( !$this->selectdb($db) )
			{
				$this->_error(E_USER_NOTICE, 'Database, \''.$db.'\', doesn\'t exist');
				return FALSE;
			}
		}

		/* Check to see if the table exists */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";

		if ( is_file($filename.'.MYD') && is_file($filename.'.FRM') )
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * To build an if-statement which can be used to see if a row
	 * fits the given credentials
	 * @param mixed $where The array containing the where clause
	 * @param mixed $cols The array containing the column definitions
	 * @return string $query The string which contains the php-equivelent to the where clause
	 * @access private
	 */
	function _buildIf ($where, $cols)
	{
		/* We can only work with a string containing where */
		if ( !is_array($where) || empty($where) )
		{
			$this->_error(E_USER_NOTICE, 'Where clause must be an array');
			return FALSE;
		}
		$query = '';

		/* Start creating the query */
		foreach ( $where as $key => $value )
		{
			/* Are we on an 'and|or'? */
			if ( $key % 2 == 1 )
			{
				/* Check for a valid "and|or" */
				$and = strtolower($value) == 'and';
				$or  = strtolower($value) == 'or';
				$xor = strtolower($value) == 'xor';
				if ( $and === FALSE && $or === FALSE && $xor === FALSE )
				{
					$this->_error(E_USER_NOTICE, 'Only boolean seperators AND, and OR are allowed');
					return FALSE;
				}
				$query .= ( $and === TRUE ) ? ' && ' : ( ( $xor === TRUE ) ? ' XOR ' : ' || ' );
				continue;
			}

			/* Find out which operator we are going to use to create the if
			 * NOTE: I'm pretty sure the order in which these operators are checked
			 *       are correct. If anyone notices a bug in the order, let me know*/
			$f1 = '(';
			$f2 = ') ';
			switch ( TRUE )
			{
				case strpos($value, '!='): $type = 1; $op = '!='; break;
				case strpos($value, '!~'): $type = 3; $op = '!~'; break;
				case strpos($value, '=~'): $type = 3; $op = '=~'; break;
				case strpos($value, '<='): $type = 2; $op = '<='; break;
				case strpos($value, '>='): $type = 2; $op = '>='; break;
				case strpos($value, '=' ): $type = 1; $op = '=';  break;
				case strpos($value, '<>'): $type = 1; $op = '<>'; break;
				case strpos($value, '<' ): $type = 2; $op = '<';  break;
				case strpos($value, '>' ): $type = 2; $op = '>';  break;
				case strpos($value, '!?'): $type = 5; $op = '!?'; break;
				case strpos($value, '?' ): $type = 5; $op = '?';  break;
				default:
					/* Check for a valid function that requires no operator */
					$val = 'TRUE';
					if ( substr(trim($value), 0, 1) == '!' )
					{
						$val   = 'FALSE';
						$value = substr($value, strpos($value, '!')+1);
					}

					$function = substr($value, 0, strpos($value, '('));
					$col      = substr($value, strlen($function) + 1, strlen($value) - strlen($function) - 2 );

					if ( $function !== FALSE )
					{
						$type = 4;
						$op   = '===';
						switch ( strtolower($function) )
						{
							case 'isnumeric':  $f1 = 'is_numeric('; break 2;
							case 'isstring':   $f1 = 'is_string('; break 2;
							case 'isfile':     $f1 = 'is_file('; break 2;
							case 'isdir':      $f1 = 'is_dir('; break 2;
							case 'iswritable': $f1 = 'is_writable(';  break 2;
						}
					}

					/* There is an error in your where clause */
					$this->_error(E_USER_NOTICE, 'You have an error in your where clause, (operators allowed: =, !=, <>, =~, !~, <, >, <=, >=)'); return FALSE;
			}

			/* Split string by the proper operator, as long as there is an operator */
			if ( !isset($function) )
			{
				list ( $col, $val ) = explode($op, $value, 2);
			}

			/* Check to see if we are utilizing a function */
			if ( substr_count($col, '(') == 1 && substr_count($col, ')') == 1 )
			{
				$function = substr($col, 0, strpos($col, '('));

				if ( $val != '' && $col{strlen($col)-1}.$val{0} == "  " )
				{
					$col  = substr($col, strlen($function) + 1, strlen($col) - strlen($function) - ( ($col{strlen($col)-1} != " " ) ? 2 : 3 ) )." ";
					$val  = $val;
				}
				else
				{
					$col = substr($col, strlen($function) + 1, strlen($col) - strlen($function) - ( ($col{strlen($col)-1} != " " ) ? 2 : 3 ) );
				}

				/* Check for a valid function call */
				switch ( strtolower($function) )
				{
					case 'strlower':   $f1 = 'strtolower(';         break;
					case 'strupper':   $f1 = 'strtoupper(';         break;
					case 'chop':
					case 'rtrim':      $f1 = 'rtrim(';              break;
					case 'ltrim':      $f1 = 'ltrim(';              break;
					case 'trim':       $f1 = 'trim(';               break;
					case 'md5':        $f1 = 'md5(';                break;
					case 'stripslash': $f1 = 'stripslashes(';       break;
					case 'strlength':  $f1 = 'strlen(';             break;
					case 'strreverse': $f1 = 'strrev(';             break;
					case 'ucfirst':    $f1 = 'ucfirst(';            break;
					case 'ucwords':    $f1 = 'ucwords(';            break;
					case 'bin2hex':    $f1 = 'bin2hex(';            break;
					case 'entdecode':  $f1 = 'html_entity_decode('; break;
					case 'entencode':  $f1 = 'htmlentities(';       break;
					case 'soundex':    $f1 = 'soundex(';            break;
					case 'ceil':       $f1 = 'ceil(';               break;
					case 'floor':      $f1 = 'floor(';              break;
					case 'round':      $f1 = 'round(';              break;

					/* These are functions that should NOT have an operator */
					case 'isnumeric':
					case 'isstring':
					case 'isfile':
					case 'isdir':
						$this->_error(E_USER_NOTICE, 'Function, '.$function.', requires that NO operator be present in the clause');
						return FALSE;

					default:
						$this->_error(E_USER_NOTICE, 'Function, '.$function.', hasn\'t been implemented');
						return FALSE;
				}
			}

			/* What if the column name is primary? */
			if ( strtolower(trim($col)) == 'primary' )
			{
				/* Make sure there is a primary key */
				if ( empty($cols['primary']) )
				{
					$this->_error(E_USER_NOTICE, 'No primary key has been assigned to this table');
					return FALSE;
				}
				$col = $cols['primary'];
			}

			/* Does the specified column exist? */
			if ( ( $position = $this->_getColPos(rtrim($col), $cols) ) === FALSE )
			{
				$this->_error(E_USER_NOTICE, 'Column \''.rtrim($col).'\' doesn\'t exist');
				return FALSE;
			}

			/* Create/Add-To the queries */
			$val = str_replace("\'", "'", addslashes($val));
			$val = ( $col{strlen($col)-1}.$val{0} == "  " ) ? substr($val, 1) : $val;

			if ( empty($val) && ( $type == '5' || $f1 != '(' ) )
			{
				$this->_error(E_USER_NOTICE, 'Forgot to specify a value to match in your where clause');
				return FALSE;
			}

			switch ( $type )
			{
				/* Test for equality */
				case 1:
				case 2: $quotes = ( !is_numeric($val) || $cols[rtrim($col)]['type'] != 'int' ) ? '"' : '';
					$query .= ' ( '.$f1.'$value['.$position.']'.$f2.' '.( $op == '=' ? '==' : $op ).' '.$quotes.$val.$quotes.' ) ';
					break;

				/* Test using regex, with[out] a function */
				case 3:	$val    = str_replace(array('(',   ')',  '{',  '}', '.',  '$',  '/',       '\%',  '*',     '%', '$$PERC$$'),
					                      array('\(', '\)', '\{', '\}', '\.', '\$', '\/', '$$PERC$$', '\*', '(.+)?',       '%'), $val);
					$query .= ' ( '.($op == '!~' ? '!' : '').'preg_match("/^'.$val.'$/iU", '.$f1.'$value['.$position.']'.$f2.') ) ';
					break;

				/* Test involving a function */
				case 4: $query .= ' ( '.$f1.'$value['.$position.']'.$f2.' === '.$val.' ) ';
				        break;

				/* Test involving a strpos with[out] function */
				case 5: $query .= ' ( strpos('.$f1.'\' \'.$value['.$position.']), \''.$val.'\') '.(($op == '!?') ? '=' : '!' ).'== FALSE ) ';
			}
			unset($function, $f1, $f2, $quotes, $position, $val, $col, $op);
		}

		/* Make sure that we have a valid query ending */
		$andor = substr($query, -3, -1);
		if ( $andor == '&&' || $andor == '||' || $andor == 'OR' )
		{
			$this->_error(E_USER_NOTICE, 'You have an error in your where clause, cannot end statement with an AND, OR, or XOR');
			return FALSE;
		}
		return $query;
	}

	/**
	 * To retrieve the index of the column from the columns' array
	 * @param string $colname The name of the column to be searched for
	 * @param mixed $cols The column definitions array
	 * @return int $position The index of the column in the array
	 * @access private
	 */
	function _getColPos ($colname, $cols)
	{
		/* Make sure array is not empty, and the parameter is an array */
		if ( empty($cols) || !is_array($cols) || !array_key_exists($colname, $cols) )
		{
			return FALSE;
		}
		unset($cols['primary']);

		/* Get the index for the column */
		if ( ( $position = array_search($colname, array_keys($cols)) ) === FALSE )
		{
			return FALSE;
		}
		return $position;
	}

	/**
	 * To sort a multi-dimensional array by a key
	 * @author fmmarzoa@gmx.net <fmmarzoa@gmx.net>
	 * @param mixed $array The array to be sorted
	 * @param string $num The name of the key to sort the array by
	 * @return string $order Either a 'ASC' or 'DESC' for sorting order
	 * @access private
	 */
	function _qsort($array, $num = 0, $order = "ASC", $left = 0, $right = -1)
	{
		if ( count($array) >= 1 )
		{
			if ( $right == -1 )
			{
				$right = count($array) - 1;
			}

			$links  = $left;
			$rechts = $right;
			$mitte  = $array[($left + $right) / 2][$num];
			if ( $rechts > $links )
			{

				do {
					if ( strtolower($order) == 'asc' )
					{
						while ( $array[$links][$num] < $mitte )
						{
							$links++;
						}
						while ( $array[$rechts][$num] > $mitte )
						{
							$rechts--;
						}
					}
					else
					{
						while ( $array[$links][$num] > $mitte )
						{
							$links++;
						}
						while ( $array[$rechts][$num] < $mitte)
						{
							$rechts--;
						}
					}

					if ( $links <= $rechts )
					{
						$tmp              = $array[$links];
						$array[$links++]  = $array[$rechts];
						$array[$rechts--] = $tmp;
					}

				}
				while ( $links <= $rechts );

				if ( $left < $rechts )
				{
					$array = $this->_qsort($array,$num,$order,$left, $rechts);
				}
				if ( $links < $right )
				{
					$array = $this->_qsort($array,$num,$order,$links,$right);
				}
			}
			return $array;
		}
		return FALSE;
	}

	/**
	 * Does what unique_array() does but with multidimensional arrays
	 * @param mixed $array The array that will be filtered
	 * @param string $sub_key The $key that will be examined for duplicates
	 */
	function unique_multi_array ( $array, $sub_key )
	{
		$target                  = array();
		$existing_sub_key_values = array();

		foreach ( $array as $key => $sub_array )
		{
			if ( !in_array($sub_array[$sub_key], $existing_sub_key_values) )
			{
				$existing_sub_key_values[] = $sub_array[$sub_key];
				$target[$key]              = $sub_array;
			}
		}
		return $target;
	}

	/**
	 * Returns the current txtSQL version
	 * @return string $version The current version of txtSQL
	 * @access public
	 */
	function version()
	{
		return '2.2 Final';
	}
}

/************************************************************************
* txtSQL                                                 ver. 2.2 Final *
*************************************************************************
* A php class of functions which simulates and acts almost like a mySQL *
* service.                                                              *
*-----------------------------------------------------------------------*
* This program is free software; you can redistribute it and/or         *
* modify it under the terms of the GNU General Public License           *
* as published by the Free Software Foundation; either version 2        *
* of the License, or (at your option) any later version.                *
*                                                                       *
* This program is distributed in the hope that it will be useful,       *
* but WITHOUT ANY WARRANTY; without even the implied warranty of        *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
* GNU General Public License for more details.                          *
*                                                                       *
* You should have received a copy of the GNU General Public License     *
* along with this program; if not, write to the Free Software           *
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307 *
* USA.                                                                  *
*-----------------------------------------------------------------------*
*  NOTE- Tab size in this file: 8 spaces/tab                            *
*-----------------------------------------------------------------------*
*  ©2003 Faraz Ali, ChibiGuy Production [http://txtsql.sourceforge.net] *
*  File: txtsql.core.php                                                *
************************************************************************/

/**
 * The core file of the txtSQL package, this is the meat of the script
 * @package txtSQL::core
 * @author Faraz Ali <Faraz87@comcast.net>
 * @version 2.2 Final
 * @access public
 */
class txtSQLCore extends txtSQL
{
	/**
	 * To extract data from a database, given that the row fits the given credentials
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return mixed selected An array containing the rows that matched the where clause
	 * @access private
	 */
	function select ($arg)
	{
		/* If the user specified a different database, we must
		 * then automatically select it for them */
		if ( !empty($arg['db']) )
		{
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* If we have no database selected, we have no table specified
		 * stop execution of script and issue an error */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}
		elseif ( empty($arg['table']) )
		{
			$this->_error(E_USER_NOTICE, 'No table specified');
			return FALSE;
		}

		/* If no selection is specified, then we will select
		 * all of the listed column */
		elseif ( empty($arg['select']) )
		{
			$arg['select'] = array('*');
		}

		/* Read in the records and column definitions, and if an error occurs
		 * then we issue a warning saying table doesn't exist */
		$filename   = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		if ( ($rows = $this->_readFile($filename.'.MYD')) === FALSE || ($cols = $this->_readFile($filename.'.FRM')) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table "'.$arg['table'].'" doesn\'t exist');
			return FALSE;
		}
		if ( empty($rows) )
		{
			return array();
		}

		/* Check to see if we have a where clause to work with */
		$matches = 'TRUE';
		if ( isset($arg['where']) )
		{
			/* Create the rule to match records, this goes inside the $rowmatches()
			 * function statement and tells us whether the current row matches the
			 * given criteria or not */
			if ( ($matches = $this->_buildIf($arg['where'], $cols)) === FALSE )
			{
				return FALSE;
			}
		}

		/* Parse the limit clause, looking for any complications, like finish
		 * value larger than the start value, non-numeric values, if no 
		 * limit is specified, or is it is not an array. */
		if ( empty($arg['limit']) || (!empty($arg['limit']) && !is_array($arg['limit'])) )
		{
			$arg['limit']['0'] = 0;
			$arg['limit']['1'] = count($rows)-1;
		}
		elseif ( isset($arg['limit'][0]) && !isset($arg['limit'][1]) )
		{
			$arg['limit'][1] = $arg['limit'][0];
			$arg['limit'][0] = 0;
		}
		elseif ( !isset($arg['limit'][0]) || !isset($arg['limit'][1]) || $arg['limit'][0] > $arg['limit'][1] )
		{
			$arg['limit']['0'] = 0;
			$arg['limit']['1'] = count($rows)-1;
		}
		$arg['limit'][0] = ( int ) $arg['limit'][0];
		$arg['limit'][1] = ( int ) $arg['limit'][1];

		/* If we have a wildcard as a select, then we need to
		 * create the selection list ourselves */
		if ( $arg['select'][0] == '*' )
		{
			$col = $cols;
			unset($col['primary']);
			$arg['select'] = array_keys($col);
		}

		/* Create the selection index, this speeds things up tremendously
		 * because it saves calls to _getColPos() */
		foreach ( $arg['select'] as $key => $value )
		{
			if ( strtolower($value) == 'primary' )
			{
				if ( empty($cols['primary']) )
				{
					$this->_error(E_USER_NOTICE, 'No primary key assigned to table '.$arg['table']);
					return FALSE;
				}
				$value = $cols['primary'];
			}

			if ( ($colPos = $this->_getColPos($value, $cols)) === FALSE )
			{
				$this->_error(E_USER_NOTICE, 'Column \''.$value.'\' doesn\'t exist');
				return FALSE;
			}

			$temp[$value] = $colPos;
		}
		$arg['select'] = $temp;

		/* Initialize Some Variables */
		$found      = -1;
		$added      = -1;
		$selected   = array();

		/* Go through each record, if the row matches and we are in our limits
		 * then select the row with the proper type (string, boolean, or integer) */
		$function  = '  foreach ( $rows as $key => $value )
				{
					if ( '.$matches.' )
					{
						$found++;
						if ( $found >= $arg[\'limit\'][0] && $found <= $arg[\'limit\'][1] )
						{
							$added++;';
							foreach ( $arg['select'] as $key => $value )
							{
								$function .= "\$selected[\$added]['$key'] = \$value[$value];";
							}
		$function .= '				if ( $found >= $arg[\'limit\'][1] )
							{
								break;
							}
						}
					}
				}  ';
		eval($function);

		/* Sort the results by a key, this is a very expensive
		 * operation and can take quite some time which is why
		 * it is not reccomended for large amounts of data */
		if ( !empty($arg['orderby']) && !empty($selected) && count($selected) > 0 )
		{
			/* We need a valid array to sort the results correctly */
			if ( !is_array($arg['orderby']) || count($arg['orderby']) != 2 )
			{
				$this->_error(E_USER_NOTICE, 'Invalid Order By Clause; Must be array, with two values. array(string "column name", [ASC|DESC])');
				return FALSE;
			}

			/* We cannot sort the results by a non-existing key */
			if ( !array_key_exists($arg['orderby'][0], $selected[0]) )
			{
				$this->_error(E_USER_NOTICE, 'Cannot sort results by column \''.$arg['orderby'][0].'\'; Column not in result set');
				return FALSE;
			}

			/* We can only sort results by ascending order or 
			 * descending order */
			if ( strtolower($arg['orderby'][1]) != 'asc' && strtolower($arg['orderby'][1]) != 'desc' )
			{
				$this->_error(E_USER_NOTICE, 'Results can only be sorted \'asc\' (ascending) or \'desc\' (descending)');
				return FALSE;
			}

			$selected = $this->_qsort($selected, $arg['orderby'][0], $arg['orderby'][1]);
		}

		/* Apply the DISTINCT feature to the result set */
		if ( !empty($arg['distinct']) )
		{
			if ( $this->_getColPos($arg['distinct'], $cols) === FALSE )
			{
				$this->_error(E_USER_NOTICE, 'Column \''.$arg['distinct'].'\' doesn\'t exist');
				return FALSE;
			}

			$selected = $this->unique_multi_array($selected, $arg['distinct']);
		}

		/* Save changes in the cache */
		$this->_CACHE[$filename.'.MYD'] = $rows;
		$this->_CACHE[$filename.'.FRM'] = $cols;

		/* Return the selected records */
		return $selected;
	}

	/**
	 * To insert a row of data into a table.
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return void
	 * @access private
	 */
	function insert ($arg)
	{
		/* If the user specifies a different database, then
		 * automatically select it for them */
		if ( !empty($arg['db']) )
		{
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* If we have no database selected, or no table to work with
		 * then stop script execution */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}
		elseif ( empty($arg['table']) )
		{
			$this->_error(E_USER_NOTICE, 'No table specified');
			return FALSE;
		}

		/* Make sure the database isn't locked */
		if ( $this->isLocked($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$this->_SELECTEDDB.' is locked');
			return FALSE;
		}

		/* Check to see if the tables exist or not, if not then we cannot
		 * continue, so we issue an error message */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		if ( ($rows = $this->_readFile($filename.'.MYD')) === FALSE || ($cols = $this->_readFile($filename.'.FRM')) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$arg['table'].' doesn\'t exist');
			return FALSE;
		}

		/* Create the model of the row */
		$model = array();
		foreach ( $cols as $key => $value )
		{
			if ( $key == 'primary' ) continue;

			if ( $value['auto_increment'] == 1 )
			{
				$model[] = ($cols[$key]['autocount']++)+1;
			}
			elseif ( $value['type'] == 'date' )
			{
				$arg['values'][$key] = '';
			}
			else
			{
				$model[] = $value['default'];
			}
		}

		/* We first create the selection indexes inside the foreach loop,
		 * inside the same one, we check that max values have not been
		 * exceeded, the table isn't permanent, and auto increment features */
		$max = count($rows);
		foreach ( $arg['values'] as $key => $value )
		{
			unset($arg['values'][$key]);

			/* If the user is referring to the primary column, then
			 * we substitute it with the actual primary column. We
			 * also check to see if the column exists or not */
			if ( strtolower($key) == 'primary' )
			{
				if ( empty($cols['primary']) )
				{
					$this->_error(E_USER_NOTICE, 'No primary key assigned to table '.$arg['table']);
					return FALSE;
				}
				$key = $cols['primary'];
			}
			if ( ($colPos = $this->_getColPos($key, $cols)) === FALSE )
			{
				$this->_error(E_USER_NOTICE, 'Column \''.$key.'\' doesn\'t exist');
				return FALSE;
			}
			$value = array($colPos, $value);

			/* Make sure that the max value for this column has not
			 * yet been exceeded */
			if ( $cols[$key]['type'] == 'int' && $cols[$key]['max'] > 0 && $value[1] > $cols[$key]['max'] )
			{
				$this->_error(E_USER_NOTICE, 'Cannot exceed maximum value for column '.$key);
				return FALSE;
			}
			elseif ( $cols[$key]['max'] > 0 && strlen($value[1]) > $cols[$key]['max'] )
			{
				$this->_error(E_USER_NOTICE, 'Cannot exceed maximum value for column '.$key);
				return FALSE;
			}

			/* If the value is empty, and there is a default value
			 * set for this column, then we substitute the value
			 * with the default */
			if ( empty($value[1]) && !empty($cols[$key]['default']) )
			{
				$value[1] = $cols[$key]['default'];
			}

			/* If this is an auto increment column, then we will
			 * will use the already incremented column value */
			if ( $cols[$key]['auto_increment'] == 1 )
			{
				$value[1] = $model[$colPos];
			}

			/* Insert the new row of data into the rows of information
			 * with the right data type */
			switch ( strtolower($cols[$key]['type']) )
			{
				case 'enum':   if ( empty($cols[$key]['enum_val']) )
					       {
						       $cols[$key]['enum_val'] = serialize(array(''));
					       }
					       $enum_val = unserialize($cols[$key]['enum_val']);
					       foreach ( $enum_val as $key => $value1 )
					       {
						       if ( strtolower($value[1]) == strtolower($value1) )
						       {
								break;
						       }
						       if ( $key == ( count($enum_val) - 1 ) )
						       {
							       $value[1] = $enum_val[$key];
							       break;
						       }
					       }
				case 'text':
				case 'string': $model[$value[0]] = (string)  $value[1]; break;
				case 'int':    $model[$value[0]] = (integer) $value[1]; break;
				case 'bool':   $model[$value[0]] = (boolean) $value[1]; break;
				case 'date':   $model[$value[0]] = time();    break;
			}
		}
		$rows[] = $model;

		/* Save the new information in their proper files */
		$fp = @fopen($filename.".MYD", 'w')  or $this->_error(E_USER_ERROR, 'Error opening table '.$arg['table']);
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, serialize($rows)) or $this->_error(E_USER_ERROR, 'Error writing to table '.$arg['table']);
		      @flock($fp, LOCK_UN);
		      @fclose($fp);
		$fp = @fopen($filename.".FRM", 'w')  or $this->_error(E_USER_ERROR, 'Error opening table '.$arg['table']);
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Error writing to table '.$arg['table']);
		      @flock($fp, LOCK_UN);
		      @fclose($fp);

		/* Save files to cache */
		$this->_CACHE[$filename.'.MYD'] = $rows;
		$this->_CACHE[$filename.'.FRM'] = $cols;

		/* Return the new number of records in the database */
		return TRUE;
	}

	/**
	 * Removes (a) row(s) that fit(s) the given credentials from a table. If none
	 * are specified, it will empty out the table.
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return int deleted The number of rows deleted
	 * @access private
	 */
	function delete ($arg)
	{
		/* If the user specifies a different database, then
		 * automatically select it for them */
		if ( !empty($arg['db']) )
		{
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* If no database is selected, or we have no table to
		 * work with, then stop execution of script */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}
		elseif ( empty($arg['table']) )
		{
			$this->_error(E_USER_NOTICE, 'No table specified');
			return FALSE;
		}

		/* Make sure the database isn't locked */
		if ( $this->isLocked($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$this->_SELECTEDDB.' is locked');
			return FALSE;
		}

		/* Check to see if the tables exist or not, if not then we cannot
		 * continue, so we issue an error message */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		if ( ($rows = $this->_readFile($filename.'.MYD')) === FALSE || ($cols = $this->_readFile($filename.'.FRM')) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$arg['table'].' doesn\'t exist');
			return FALSE;
		}

		/* Check to see if we have a where clause to work with */
		if ( isset($arg['where']) )
		{
			/* Create the rule to match records, this goes inside the eval()
			 * statement and tells us whether the current row matches or not */
			if ( ($matches = $this->_buildIf($arg['where'], $cols)) === FALSE )
			{
				return FALSE;
			}
		}
		else
		{
			$rows = array();
		}

		/* Parse the limit clause looking for any complications
		 * like it not being an array, or if we don't have a numeric
		 * value */
		if ( !isset($arg['limit']) || empty($arg['limit']) || !is_numeric($arg['limit'][0]) )
		{
			$arg['limit']['0'] = count($rows);
		}

		/* Initialize some variables */
		$found = 0;
		$deleted = 0;

		/* Go through each record, if the row matches and we are in our limits
		 * then delete the row */
		$function = '
		foreach ( $rows as $key => $value )
		{
			if ( '.( isset($matches) ? $matches : 'TRUE' ).' )
			{
				$found++;
				if ( $found <= $arg[\'limit\'][0] )
				{
					$deleted++;
					unset($rows[$key]);
					if ( $found >= $arg[\'limit\'][0] )
					{
						break;
					}
					continue;
				}
				break;
			}
		}';
		eval($function);

		/* Save the new record information */
		$fp = @fopen($filename.".MYD", 'w')  or $this->_error(E_USER_ERROR, 'Error opening table '.$arg['table']);
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, serialize($rows)) or $this->_error(E_USER_ERROR, 'Error writing to table '.$arg['table']);
		      @flock($fp, LOCK_UN);
		      @fclose($fp);

		/* Save files to cache */
		$this->_CACHE[$filename.'.MYD'] = $rows;
		$this->_CACHE[$filename.'.FRM'] = $cols;

		/* Return the number of deleted rows */
		return $deleted;
	}

	/**
	 * Updates a row that matches the given credentials with
	 * the new data
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return int updated The number of rows that were updated
	 * @access private
	 */
	function update ($arg)
	{
		/* If the user specifies a different database
		 * then we must automatically select it for them. */
		if ( !empty($arg['db']) )
		{
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* If there is no database selected, or we have no table
		 * selected, then stop execution of script */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}
		elseif ( empty($arg['table']) )
		{
			$this->_error(E_USER_NOTICE, 'No table specified');
			return FALSE;
		}

		/* Make sure the database isn't locked */
		if ( $this->isLocked($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$this->_SELECTEDDB.' is locked');
			return FALSE;
		}

		/* Check to see if the tables exist or not, if not then we cannot
		 * continue, so we issue an error message */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		if ( ($rows = $this->_readFile($filename.'.MYD')) === FALSE || ($cols = $this->_readFile($filename.'.FRM')) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$arg['table'].' doesn\'t exist');
			return FALSE;
		}

		/* Check to see if we have a where clause to work with */
		if ( !isset($arg['where']) )
		{
			$this->_error(E_USER_NOTICE, 'Must specify a where clause');
			return FALSE;
		}

		/* Create the rule to match records, this goes inside the eval()
		 * statement and tells us whether the current row matches or not */
		elseif ( ($matches = $this->_buildIf($arg['where'], $cols)) === FALSE )
		{
			return FALSE;
		}

		/* If we have no values to substitute, issue a warning and return */
		elseif ( !isset($arg['values']) || empty($arg['values']) )
		{
			$this->_error(E_USER_NOTICE, 'Must specify values to update');
			return FALSE;
		}

		/* Parse the limit looking for any complications like
		 * non-numeric values, and not being an array */
		if ( empty($arg['limit']) )
		{
			$arg['limit']['0'] = count($rows);
		}
		elseif ( !is_array($arg['limit']) || !is_numeric($arg['limit'][0]) || $arg['limit'][0] <= 0 )
		{
			$arg['limit']['0'] = count($rows);
		}

		/* Create the selection index, this little thing saves calls
		 * to _getColPos() about 10000 times, and speeds things up */
		foreach ( $arg['values'] as $key => $value )
		{
			/* If the user specifies the primary column,
			 * substitute the actual column name for it. */
			if ( strtolower($key) == 'primary' )
			{
				if ( empty($cols['primary']) )
				{
					$this->_error(E_USER_NOTICE, 'No primary key assigned to table '.$arg['table']);
					return FALSE;
				}
				$key = $cols['primary'];
			}

			/* If the column doesn't exist */
			if ( ($colPos = $this->_getColPos($key, $cols)) === FALSE )
			{
				$this->_error(E_USER_NOTICE, 'Column \''.$key.'\' doesn\'t exist');
				return FALSE;
			}

			/* If the column is permanent */
			if ( $cols[$key]['permanent'] == 1 )
			{
				$this->_error(E_USER_NOTICE, 'Column '.$key.' is set to permanent');
				unset($arg['values'][$key]);
				continue;
			}

			/* does it exceed max val? */
			if ( $cols[$key]['type'] == 'int' && $cols[$key]['max'] > 0 && $value > $cols[$key]['max'] )
			{
				$this->_error(E_USER_NOTICE, 'Cannot exceed maximum value for column '.$key);
				return FALSE;
			}
			elseif ( $cols[$key]['max'] > 0 && strlen($value) > $cols[$key]['max'] )
			{
				$this->_error(E_USER_NOTICE, 'Cannot exceed maximum value for column '.$key);
				return FALSE;
			}
			$arg['values'][$key] = array($colPos, $value);
			unset($key, $value);
		}
		
		/* Initialize some variables */
		$found        = 0;
		$updated      = 0;

		/* Start going through each row of information looking for a match,
		 * and if it matches then updates the row with the proper information */

		$function = '	foreach ( $rows as $key => $value )
				{
					if ( '.$matches.' )
					{
						$found++;
						if ( $found <= $arg[\'limit\'][0] )
						{
							$updated++;';
							foreach ( $arg['values'] as $key1 => $value1 )
							{
								switch ( strtolower($cols[$key1]['type']) )
								{
									case 'enum':   if ( empty($cols[$key1]['enum_val']) )
										       {
											       $cols[$key1]['enum_val'] = serialize(array(''));
										       }
										       $enum_val = unserialize($cols[$key1]['enum_val']);
										       foreach ( $enum_val as $key2 => $value2 )
										       {
											       if ( strtolower($arg['values'][$key1][1]) == strtolower($value2) )
											       {
													break;
											       }
											       if ( $key2 == ( count($enum_val) - 1 ) )
											       {
												       $arg['values'][$key1][1] = $enum_val[$key2];
												       break;
											       }
										       }
									case 'text':
									case 'string': $type = "string"; break;
									case 'int':    $type = "integer"; break;
									case 'bool':   $type = "boolean"; break;
									default:       $type = "string";
								}

								$function .= "\$rows[\$key][$value1[0]] = ( $type ) \$arg['values']['$key1'][1];";
							}
		$function .= '				continue;
						}
						break;
					}
				}
		';
		eval($function);

		/* Save the new row information */
		$fp = @fopen($filename.".FRM", 'w')  or $this->_error(E_USER_ERROR, 'Error opening table '.$arg['table']);
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Error writing to table '.$arg['table']);
		      @flock($fp, LOCK_UN);
		      @fclose($fp);

		$fp = @fopen($filename.".MYD", 'w')  or $this->_error(E_USER_ERROR, 'Error opening table '.$arg['table']);
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, serialize($rows)) or $this->_error(E_USER_ERROR, 'Error writing to table '.$arg['table']);
		      @flock($fp, LOCK_UN);
		      @fclose($fp);

		/* Save files to cache */
		$this->_CACHE[$filename.'.MYD'] = $rows;
		$this->_CACHE[$filename.'.FRM'] = $cols;

		/* Return the number of rows that were updated */
		return $updated;
	}

	/**
	 * Returns an array with a list of tables inside of a database
	 * @param mixed arg The arguments that are passed to the txtSQL as an array. 
	 * @return mixed tables An array containing the tables inside of a db
	 * @access private
	 */
	function showtables ($arg = NULL)
	{
		/* Are we showing tables inside of another database? */
		if ( !empty($arg['db']) )
		{
			/* Does it exist? */
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* Is a database selected? */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Can we open the directory up? */
		if ( ($fp = @opendir("$this->_LIBPATH/$this->_SELECTEDDB")) === FALSE )
		{
			$this->_error(E_USER_ERROR, 'Could not open directory, '.$this->_LIBPATH.'/'.$this->_SELECTEDDB.', for reading');
		}

		/* Make sure that it's a directory, and not a '..' or '.' */
		$table = array();
		while ( ($file = @readdir($fp)) !== FALSE )
		{
			if ( $file != "." && $file != ".." && $file != 'txtsql.MYI')
			{
				/* If it's a valid txtsql table */
				$extension = substr($file, strrpos($file, '.')+1);
				if ( ($extension == 'MYD' || $extension == 'FRM') && is_file("$this->_LIBPATH/$this->_SELECTEDDB/$file") )
				{
					$table[] = substr($file, 0, strrpos($file, '.'));
				}
			}
		}
		@closedir($fp);

		/* Get only the tables that are valid */
		$tables = array();
		foreach ( $table as $key => $value )
		{
			if ( isset($temp[$value]) )
			{
				$tables[] = $value;
			}
			else
			{
				$temp[$value] = TRUE;
			}
		}

		/* Return only the names of the tables */
		return !empty($tables) ? $tables : array();
	}

	/**
	 * Creates a table inside of a database, with the specified credentials of the column
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return void
	 * @access private
	 */
	function createtable ($arg=NULL)
	{
		/* Inside another database? */
		if ( !empty($arg['db']) )
		{
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* Do we have a selected database? */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Make sure the database isn't locked */
		if ( $this->isLocked($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$this->_SELECTEDDB.' is locked');
			return FALSE;
		}

		/* Do we have a valid table name? */
		if ( empty($arg['table']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['table']) )
		{
			$this->_error(E_USER_NOTICE, 'Table name can only contain letters, and numbers');
			return FALSE;
		}

		/* Do we have any columns? */
		if ( empty($arg['columns']) || !is_array($arg['columns']) )
		{
			$this->_error(E_USER_NOTICE, 'Invalid columns for table '.$arg['table']);
			return FALSE;
		}

		/* Start creating an array and populating it with
		 * the column names, and types */
		$cols       = array('primary' => '');
		$primaryset = FALSE;
		foreach ( $arg['columns'] as $key => $value )
		{
			/* What an untouched column looks like */
			$model = array('permanent'      => 0,
				       'auto_increment' => 0,
				       'max'            => 0,
				       'type'           => 'string',
				       'default'        => '',
				       'autocount'      => (int) 0,
				       'enum_val'       => '');

			/* Column cannot be named primary */
			if ( $key == 'primary' )
			{
				$this->_error(E_USER_NOTICE, 'Use of reserved word [primary]');
				return FALSE;
			}

			/* $value has to be an array */
			if ( (!empty($value) && !is_array($value)) || empty($key) )
			{
				$this->_error(E_USER_NOTICE, 'Invalid columns for table '.$arg['table']);
				return FALSE;
			}

			/* Go through each column type */
			foreach ( $value as $key1 => $value1 )
			{
				switch ( strtolower($key1) )
				{
					case 'auto_increment':
						/* Need either a 1 or 0 */
						$value1 = (int) $value1;
						if ( $value1 < 0 || $value1 > 1 )
						{
							$this->_error(E_USER_NOTICE, 'Auto_increment must be a boolean 1 or 0');
							return FALSE;
						}

						/* Has to be an integer type */
						if ( isset($value['type']) && $value['type'] != 'int' && $value1 == 1 )
						{
							$this->_error(E_USER_NOTICE, 'auto_increment must be an integer type');
							return FALSE;
						}
						$model['auto_increment'] = $value1;
						break;
					case 'permanent':
						/* Need either a 1 or 0 */
						$value1 = (int) $value1;
						if ( $value1 < 0 || $value1 > 1 )
						{
							$this->_error(E_USER_NOTICE, 'Permanent must be a boolean 1 or 0');
							return FALSE;
						}
						$model['permanent'] = $value1;
						break;
					case 'max':
						/* Need an integer value greater than -1, less than 1,000,000 */
						$value1 = (int) $value1;
						if ( $value1 < 0 || $value1 > 1000000 )
						{
							$this->_error(E_USER_NOTICE, 'Max must be less than 1,000,000 and greater than -1');
							return FALSE;
						}
						$model['max'] = $value1;
						break;
					case 'type':
						/* Can only accept an integer, string, boolean */
						switch ( strtolower($value1) )
						{
							case 'text':
								$model['type'] = 'text';
								break;
							case 'string':
								$model['type'] = 'string';
								break;
							case 'int':
								$model['type'] = 'int';
								break;
							case 'bool':
								$model['type'] = 'bool';
								break;
							case 'enum':
								if ( !isset($value['enum_val']) || !is_array($value['enum_val']) || empty($value['enum_val']) )
								{
									$this->_error(E_USER_NOTICE, 'Missing enum\'s list of values or invalid list inputted');
									return FALSE;
								}
								$model['type'] = 'enum';
								$model['enum_val'] = serialize($value['enum_val']);
								break;
							case 'date':
								$model['type'] = 'date';
								break;
							default:
								$this->_error(E_USER_NOTICE, 'Invalid column type, can only accept integers, strings, and booleans');
								return FALSE;
						}
						break;
					case 'default':
						$model['default'] = $value1;
						break;
					case 'primary':
						/* Need either a 1 or 0 */
						$value1 = (int) $value1;
						if ( $value1 < 0 || $value1 > 1 )
						{
							$this->_error(E_USER_NOTICE, 'Primary must be a boolean 1 or 0');
							return FALSE;
						}

						/* Make sure primary hasn't already been set */
						if ( $primaryset === TRUE && $value1 == 1 )
						{
							$this->_error(E_USER_NOTICE, 'Only one primary column can be set');
							return FALSE;
						}

						if ( $value1 == 1 )
						{
							/* Primary keys have to be integer and auto_increment */
							$value['auto_increment'] = isset($value['auto_increment']) ? $value['auto_increment'] : 0;
							$value['type']           = isset($value['type'])           ? $value['type']           : 0;

							if ( $value['auto_increment'] != 1 || $value['type'] != 'int' )
							{
								$this->_error(E_USER_NOTICE, 'Primary keys must be of type \'integer\' and auto_increment');
								return FALSE;
							}

							$cols['primary'] = $key;
						}
						break;
					case 'enum_val':
						break;
					default:
						$this->_error(E_USER_NOTICE, 'Invalid column definition, ["'.$key1.'"], specified');
						return FALSE;
						break;
				}
			}
			$cols[$key] = $model;
		}

		/* Create two files, $name.myd (empty), and $name.frm (the column defintions) */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$arg[table]";

		/* Make sure table doesn't exist already */
		if ( is_file($filename.".MYD") || is_file($filename.".FRM") )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$arg['table'].' already exists');
			return FALSE;
		}

		/* Go ahead and create the files */
		$fp = @fopen($filename.".MYD", 'w')  or $this->_error(E_USER_ERROR, 'Error creating table '.$arg['table']);
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, 'a:0:{}') or $this->_error(E_USER_ERROR, 'Error writing to table '.$arg['table'].' while creating it');
		      @flock($fp, LOCK_UN);
		      @fclose($fp);
		      @chmod($filename.".MYD", 0777);

		$fp = @fopen($filename.".FRM", 'w')  or $this->_error(E_USER_ERROR, 'Error creating table '.$arg['table']);
		      @flock($fp, LOCK_EX);
		      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Error creating table '.$arg['table']);
		      @flock($fp, LOCK_UN);
		      @chmod($filename.".FRM", 0777);
		      @fclose($fp);

		/* Save files to cache */
		$this->_CACHE[$filename.'.FRM'] = $cols;
		return TRUE;
	}

	/**
	 * Drops a table given that it already exists within a database
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return void
	 * @access private
	 */
	function droptable ($arg=NULL)
	{
		/* Make sure that we have a name, and that it's valid */
		if ( empty($arg['table']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['table']) )
		{
			$this->_error(E_USER_NOTICE, 'Database name can only contain letters, and numbers');
			return FALSE;
		}

		/* Does the table exist in another database? */
		if ( !empty($arg['db']) )
		{
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* Do we have selected database? */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Make sure the database isn't locked */
		if ( $this->isLocked($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$this->_SELECTEDDB.' is locked');
			return FALSE;
		}

		/* Does table exist? */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$arg[table]";
		if ( !is_file($filename.'.MYD') || !is_file($filename.'.FRM') )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$arg['table'].' doesn\'t exist');
			return FALSE;
		}

		/* Delete two files $name.myd, $name.frm */
		if ( !@unlink($filename.'.MYD') || !@unlink($filename.'.FRM') )
		{
			$this->_error(E_USER_ERROR, 'Could not delete table '.$arg['table']);
		}
		return TRUE;
	}

	/**
	 * Alters a table by working with its columns. You can rename, insert, edit, delete columns.
	 * Also allows for manipulation of primary keys.
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return void
	 * @access private
	 */
	function altertable ($arg=NULL)
	{
		/* Is inside another database? */
		if ( !empty($arg['db']) )
		{
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* Do we have a selected database? */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Make sure the database isn't locked */
		if ( $this->isLocked($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$this->_SELECTEDDB.' is locked');
			return FALSE;
		}

		/* Check to see if action is not empty, and name is valid */
		if ( !empty($arg['name']) && !preg_match('/^[A-Za-z0-9_]+$/', $arg['name']) )
		{
			$this->_error(E_USER_NOTICE, 'Names can only contain letters, numbers, and underscored');
			return FALSE;
		}
		elseif ( empty($arg['action']) )
		{
			$this->_error(E_USER_NOTICE, 'No action specified in alter table query');
			return FALSE;
		}
	
		/* Check to see if the table exists */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$arg[table]";
		if ( !is_file($filename.'.MYD') || !is_file($filename.'.FRM') )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$arg['table'].' doesn\'t exist');
			return FALSE;
		}

		/* Read in the information for the table */
		if ( ($rows = $this->_readFile($filename.'.MYD')) === FALSE || ($cols = $this->_readFile($filename.'.FRM')) === FALSE )
		{
			$this->_error(E_USER_NOTICE, 'Table "'.$arg['table'].'" doesn\'t exist');
			return FALSE;
		}

		/* Check for a primary key */
		$primaryset = !empty($cols['primary']) ? TRUE : FALSE;

		/* Are we allowed to change the column? */
		$action = strtolower($arg['action']);

		/* Perform the proper action */
		switch (strtolower($arg['action']))
		{
			/* ======================================================================
			 * Insert A Column Into The Table
			 * ======================================================================*/
			case 'insert':
				/* Make sure we have a column name */
				if ( empty($arg['name']) )
				{
					$this->_error(E_USER_NOTICE, 'Forgot to input new column\'s name');
					return FALSE;
				}

				/* Cannot name column primary */
				if ( $arg['name'] == 'primary' )
				{
					$this->_error(E_USER_NOTICE, 'Cannot name column primary (use of reserved words)');
					return FALSE;
				}

				/* Check whether the column exists already or not */
				elseif ( isset($cols[$arg['name']]) )
				{
					$this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' already exists');
					return FALSE;
				}

				/* Check to see if we have a column to insert after */
				if ( empty($arg['after']) )
				{
					$colNames = array_keys($cols);
					$arg['after'] = $colNames[count($cols)-1];
				}

				/* Parse the types for this column */
				$model = array('permanent'      => 0,
				               'auto_increment' => 0,
				               'max'            => 0,
				               'type'           => 'int',
				               'default'        => '',
				               'autocount'      => 0,
				               'enum_val'       => '');

				foreach ( $arg['values'] as $key => $value )
				{
					switch (strtolower($key))
					{
						case 'auto_increment':
							/* Need either a 1 or 0 */
							$value = (int) $value;
							if ( $value < 0 || $value > 1 )
							{
								$this->_error(E_USER_NOTICE, 'Auto_increment must be a boolean 1 or 0');
								return FALSE;
							}

							/* Has to be an integer type */
							if ( isset($arg['values']['type']) && $arg['values']['type'] != 'int' && $value == 1 )
							{
								$this->_error(E_USER_NOTICE, 'auto_increment must be an integer type');
								return FALSE;
							}
							$model['auto_increment'] = $value;
							break;
						case 'permanent':
							/* Need either a 1 or 0 */
							$value = (int) $value;
							if ( $value < 0 || $value > 1 )
							{
								$this->_error(E_USER_NOTICE, 'Permanent must be a boolean 1 or 0');
								return FALSE;
							}
							$model['permanent'] = $value;
							break;
						case 'max':
							/* Need an integer value greater than -1, less than 1,000,000 */
							$value = (int) $value;
							if ( $value < 0 || $value > 1000000 )
							{
								$this->_error(E_USER_NOTICE, 'Max must be less than 1,000,000 and greater than -1');
								return FALSE;
							}
							$model['max'] = $value;
							break;
						case 'type':
							/* Can only accept an integer, string, boolean */
							switch ( strtolower($value) )
							{
								case 'text':
									$model['type'] = 'text';
									break;
								case 'string':
									$model['type'] = 'string';
									break;
								case 'int':
									$model['type'] = 'int';
									break;
								case 'bool':
									$model['type'] = 'bool';
									break;
								case 'enum':
									if ( !isset($arg['values']['enum_val']) || !is_array($arg['values']['enum_val']) || empty($arg['values']['enum_val']) )
									{
										$this->_error(E_USER_NOTICE, 'Missing enum\'s list of values or invalid list inputted');
										return FALSE;
									}
									$model['type'] = 'enum';
									$model['enum_val'] = serialize($arg['values']['enum_val']);
									break;
								case 'date':
									$model['type'] = 'date';
									break;
								default:
									$this->_error(E_USER_NOTICE, 'Invalid column type, can only accept integers, strings, and booleans');
									return FALSE;
							}
							break;
						case 'default':
							$model['default'] = $value;
							break;
						case 'primary':
							/* Need either a 1 or 0 */
							$value = (int) $value;
							if ( $value < 0 || $value > 1 )
							{
								$this->_error(E_USER_NOTICE, 'Primary must be a boolean 1 or 0');
								return FALSE;
							}

							/* Make sure primary hasn't already been set */
							if ( $primaryset === TRUE && $value == 1 )
							{
								$this->_error(E_USER_NOTICE, 'Only one primary column can be set');
								return FALSE;
							}

							if ( $value == 1 )
							{
								$cols['primary'] = $arg['name'];
							}
							break;
						case 'enum_val':
							break;
						default:
							$this->_error(E_USER_NOTICE, 'Invalid column definition, ["'.$key.'"], specified');
							return FALSE;
					}
				}

				/* Determine the column in which we insert after */
				if ( $arg['after'] == 'primary' )
				{
					$afterColPos = 1;
				}
				else
				{
					if ( ($afterColPos = $this->_getColPos($arg['after'], $cols)+2) === FALSE )
					{
						$this->_error(E_USER_NOTICE, 'Column \''.$arg['after'].'\' doesn\'t exist');
						return FALSE;
					}
				}

				/* Add the column to the list of already existing columns,
				 * but after the specified column */
				$i = 0;
				foreach ( $cols as $key => $value )
				{
					$temp[$key] = $value;
					$i++;
					if ( $i == $afterColPos )
					{
						$temp[$arg['name']] = $model;
					}
				}
				$cols = $temp;

				/* Add the column to each row of data */
				if ( !empty($rows) )
				{
					foreach ( $rows as $key => $value )
					{
						$i = 0;
						foreach ( $value as $key1 => $value1 )
						{
							if ( $i < $afterColPos-1 )
							{
								$temp1[$key][$key1] = $value1;
							}
							if ( $i == $afterColPos - 1 || ( $i == count($value) - 1 && $i == $afterColPos - 2 ) )
							{
								$temp1[$key][ ( ( $i == count($value) - 1 && $i == $afterColPos - 2) ? $key1 + 1 : $key1 ) ] = '';
								$i++;
							}
							if ( $i > $afterColPos-1 )
							{
								$temp1[$key][$key1+1] = $value1;
							}
							$i++;
						}
					}
					$rows = $temp1;
				}

				/* Save the information */
				$fp = @fopen($filename.'.FRM', w)    or $this->_error(E_USER_ERROR, 'Could not open '.$filename.'.FRM for writing');
				      @flock($fp, LOCK_EX);
				      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Could not write to file '.$filename.'.FRM');
				      @flock($fp, LOCK_UN);
				      @fclose($fp)                   or $this->_error(E_USER_ERROR, 'Could not close '.$filename.'.FRM');

				$fp = @fopen($filename.'.MYD', w)    or $this->_error(E_USER_ERROR, 'Could not open '.$filename.'.MYD for writing');
				      @flock($fp, LOCK_EX);
				      @fwrite($fp, serialize($rows)) or $this->_error(E_USER_ERROR, 'Could not write to file '.$filename.'.MYD');
				      @flock($fp, LOCK_UN);
				      @fclose($fp)                   or $this->_error(E_USER_ERROR, 'Could not close '.$filename.'.MYD');

				/* Save files to cache */
				$this->_CACHE[$filename.'.MYD'] = $rows;
				$this->_CACHE[$filename.'.FRM'] = $cols;
				return TRUE;
				break;

			/* ======================================================================
			 * MODIFY A TABLE'S COLUMN
			 * ======================================================================*/
			case 'modify':
				/* Are we allowed to change this column? */
				if ( $arg['name'] == 'primary' )
				{
					$this->_error(E_USER_NOTICE, 'Column primary doesn\'t exist');
					return FALSE;
				}

				/* Check whether the column exists already or not */
				elseif ( !isset($cols[$arg['name']]) )
				{
					$this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' doesn\'t exist');
					return FALSE;
				}

				/* Do we have any values to work with? */
				elseif ( empty($arg['values']) )
				{
					$this->_error(E_USER_NOTICE, 'Empty column set given');
					return FALSE;
				}

				/* Are we allowed to modify the column? */
				/*if ( $cols[$arg['name']]['permanent'] == 1 && !isset($arg['values']['permanent']) )
				{
					$this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' is set to permanent');
					return FALSE;
				}*/

				/* Parse the types for this column */
				$model = array('permanent'      => $cols[$arg['name']]['permanent'],
				               'auto_increment' => $cols[$arg['name']]['auto_increment'],
				               'max'            => $cols[$arg['name']]['max'],
				               'type'           => $cols[$arg['name']]['type'],
				               'default'        => $cols[$arg['name']]['default'],
				               'autocount'      => $cols[$arg['name']]['autocount'],
				               'enum_val'       => $cols[$arg['name']]['enum_val']);

				foreach ( $arg['values'] as $key => $value )
				{
					switch (strtolower($key))
					{
						case 'auto_increment':
							/* Need either a 1 or 0 */
							$value = (int) $value;
							if ( $value < 0 || $value > 1 )
							{
								$this->_error(E_USER_NOTICE, 'Auto_increment must be a boolean 1 or 0');
								return FALSE;
							}

							/* Has to be an integer type */
							if ( isset($arg['values']['type']) && $arg['values']['type'] != 'int' && $value == 1 )
							{
								$this->_error(E_USER_NOTICE, 'auto_increment must be an integer type');
								return FALSE;
							}
							$model['auto_increment'] = $value;
							break;
						case 'permanent':
							/* Need either a 1 or 0 */
							$value = (int) $value;
							if ( $value < 0 || $value > 1 )
							{
								$this->_error(E_USER_NOTICE, 'Permanent must be a boolean 1 or 0');
								return FALSE;
							}
							$model['permanent'] = $value;
							break;
						case 'max':
							/* Need an integer value greater than -1, less than 1,000,000 */
							$value = (int) $value;
							if ( $value < 0 || $value > 1000000 )
							{
								$this->_error(E_USER_NOTICE, 'Max must be less than 1,000,000 and greater than -1');
								return FALSE;
							}
							$model['max'] = $value;
							break;
						case 'type':
							/* Can only accept an integer, string, boolean */
							switch ( strtolower($value) )
							{
								case 'text':
									$model['type'] = 'text';
									break;
								case 'string':
									$model['type'] = 'string';
									break;
								case 'int':
									$model['type'] = 'int';
									break;
								case 'bool':
									$model['type'] = 'bool';
									break;
								case 'enum':
									if ( !isset($arg['values']['enum_val']) || !is_array($arg['values']['enum_val']) || empty($arg['values']['enum_val']) )
									{
										$this->_error(E_USER_NOTICE, 'Missing enum\'s list of values or invalid list inputted');
										return FALSE;
									}
									$model['type'] = 'enum';
									$model['enum_val'] = serialize($arg['values']['enum_val']);
									break;
								case 'date':
									$model['type'] = 'date';
									break;
								default:
									$this->_error(E_USER_NOTICE, 'Invalid column type, can only accept integers, strings, and booleans');
									return FALSE;
							}
							break;
						case 'default':
							$model['default'] = $value;
							break;
						case 'primary':
							/* Need either a 1 or 0 */
							$value = (int) $value;
							if ( $value < 0 || $value > 1 )
							{
								$this->_error(E_USER_NOTICE, 'Primary must be a boolean 1 or 0');
								return FALSE;
							}

							/* Make sure primary hasn't already been set */
							if ( $primaryset === TRUE && $value == 1 )
							{
								$this->_error(E_USER_NOTICE, 'Only one primary column can be set');
								return FALSE;
							}

							if ( $value == 1 )
							{
								$cols['primary'] = $arg['name'];
							}
							break;
						case 'enum_val':
							break;
						default:
							$this->_error(E_USER_NOTICE, 'Invalid column definition, ["'.$key.'"], specified');
							return FALSE;
					}
				}

				/* Check for a primary key */
				if ( ( $model['type'] != 'int' || $model['auto_increment'] != 1 ) && strtolower($cols['primary']) == strtolower($arg['name']) )
				{
					$cols['primary'] = '';
					$this->_error(E_USER_NOTICE, 'The primary key has been dropped, column must be auto_increment, and integer');
				}


				/* Add the column to the list of columns */
				$cols[$arg['name']] = $model;

				/* Save the results */
				$fp = @fopen($filename.'.FRM', w)    or $this->_error(E_USER_ERROR, 'Could not open '.$filename.'.FRM for writing');
				      @flock($fp, LOCK_EX);
				      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Could not write to file '.$filename.'.FRM');
				      @flock($fp, LOCK_UN);
				      @fclose($fp)                   or $this->_error(E_USER_ERROR, 'Could not close '.$filename.'.FRM');

				/* Save files to cache */
				$this->_CACHE[$filename.'.FRM'] = $cols;
				return TRUE;
				break;

			/* ======================================================================
			 * DROP A TABLE'S COLUMN
			 * ======================================================================*/
			case 'drop':
				/* Chcek for a valid name */
				if ( empty($arg['name']) or !preg_match('/^[A-Za-z0-9_]+$/', $arg['name']) )
				{
					$this->_error(E_USER_NOTICE, 'Column name can only contain letters, numbers, and underscores');
					return FALSE;
				}

				/* Does the column exist? */
				if ( !isset($cols[$arg['name']]) || $arg['name'] == 'primary' )
				{
					$this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' doesn\'t exist');
					return FALSE;
				}

				/* Make sure dropping this column doesn't jeopordize the table */
				if ( count($cols) - 2 <= 0 )
				{
					$this->_error(E_USER_NOTICE, 'Cannot drop column; There has to be at-least ONE column present');
					return false;
				}

				/* Get the position that the column was in */
				$i = -1;
				foreach ( $cols as $key => $value )
				{
					if ( $key == $arg['name'] && $i > -1 )
					{
						$position = $i;
						break;
					}
					$i++;
				}

				/* Drop the column from list of columns, including primary key */
				if ( $cols['primary'] == $arg['name'] )
				{
					$cols['primary'] = '';
				}
				unset($cols[$arg['name']]);

				/* Delete the column from each of the rows of data */
				if ( is_array($rows) && count($rows) > 0 )
				{
					foreach ( $rows as $key => $value )
					{
						unset($rows[$key][$position]);
						$rows[$key] = array_splice($rows[$key], 0);
					}
				}

				/* Save the results */
				$fp = @fopen($filename.'.FRM', w)    or $this->_error(E_USER_ERROR, 'Could not open '.$filename.'.FRM for writing');
				      @flock($fp, LOCK_EX);
				      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Could not write to file '.$filename.'.FRM');
				      @flock($fp, LOCK_UN);
				      @fclose($fp)                   or $this->_error(E_USER_ERROR, 'Could not close '.$filename.'.FRM');

				$fp = @fopen($filename.'.MYD', w)    or $this->_error(E_USER_ERROR, 'Could not open '.$filename.'.MYD for writing');
				      @flock($fp, LOCK_EX);
				      @fwrite($fp, serialize($rows)) or $this->_error(E_USER_ERROR, 'Could not write to file '.$filename.'.MYD');
				      @flock($fp, LOCK_UN);
				      @fclose($fp)                   or $this->_error(E_USER_ERROR, 'Could not close '.$filename.'.MYD');

				/* Save files to cache */
				$this->_CACHE[$filename.'.MYD'] = $rows;
				$this->_CACHE[$filename.'.FRM'] = $cols;
				return TRUE;
				break;

			/* ======================================================================
			 * RENAME A TABLE'S COLUMN
			 * ======================================================================*/
			case 'rename col':
				/* Check for valid names */
				if ( empty($arg['name']) || empty($arg['values']['name']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['values']['name']) )
				{
					$this->_error(E_USER_NOTICE, 'Column names can only contain letters, numbers, and underscores');
					return FALSE;
				}

				/* Check to make sure column exists */
				if ( !isset($cols[$arg['name']]) )
				{
					$this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' doesn\'t exist');
					return FALSE;
				}

				/* Are we allowed to modify the column?
				if ( $cols[$arg['name']]['permanent'] == 1 )
				{
					$this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' is set to permanent');
					return FALSE;
				}*/

				/* Check to see whether new column name doesn't exist */
				if ( isset($cols[$arg['values']['name']]) && $arg['values']['name'] != $arg['name'] )
				{
					$this->_error(E_USER_NOTICE, 'Column '.$arg['name'].' already exists');
					return FALSE;
				}

				/* If it was primary key, change primary key */
				if ( $cols['primary'] == $arg['name'] )
				{
					$cols['primary'] = $arg['values']['name'];
				}

				/* Rename column */
				$tmp = $cols;
				$cols = array();
				foreach ( $tmp as $key => $value )
				{
					if ( $key == $arg['name'] )
					{
						$key = $arg['values']['name'];
					}
					$cols[$key] = $value;
				}

				/* Save the results */
				$fp = @fopen($filename.'.FRM', w)    or $this->_error(E_USER_ERROR, 'Could not open '.$filename.'.FRM for writing');
				      @flock($fp, LOCK_EX);
				      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Could not write to file '.$filename.'.FRM');
				      @flock($fp, LOCK_UN);
				      @fclose($fp)                   or $this->_error(E_USER_ERROR, 'Could not close '.$filename.'.FRM');

				/* Save files to cache */
				$this->_CACHE[$filename.'.FRM'] = $cols;
				return TRUE;
				break;

			/* ======================================================================
			 * RENAME A TABLE COLLECTIVELY
			 * ======================================================================*/
			case 'rename table':
				/* Check for valid names */
				if ( !preg_match('/^[A-Za-z0-9_]+$/', $arg['name']) )
				{
					$this->_error(E_USER_NOTICE, 'Table name can only contain letters, numbers, and underscores');
					return FALSE;
				}

				/* Make sure new table doesn't exit */
				$fp1 = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['name']}";
				if ( (is_file($fp1.'.FRM') || is_file($fp1.'.MYD')) && strtolower($arg['name']) != strtolower($arg['table']) )
				{
					$this->_error(E_USER_NOTICE, 'Table '.$arg['name'].' already exists');
					return FALSE;
				}

				/* Do the renaming */
				@rename($filename.'.FRM', $fp1.'.FRM') or $this->_error(E_USER_ERROR, 'Error renaming file '.$filename.'.FRM');
				@rename($filename.'.MYD', $fp1.'.MYD') or $this->_error(E_USER_ERROR, 'Error renaming file '.$filename.'.MYD');

				return TRUE;
				break;

			/* ======================================================================
			 * ADD A PRIMARY KEY TO A TABLE
			 * ======================================================================*/
			case 'addkey':
				/* Check for a valid column name */
				if ( empty($arg['values']['name']) )
				{
					$this->_error(E_USER_NOTICE, 'Invalid Column Name');
					return FALSE;
				}
				if ( $this->_getColPos($arg['values']['name'], $cols) === FALSE )
				{
					$this->_error(E_USER_NOTICE, 'Column '.$arg['values']['name'].' doesn\'t exist');
					return FALSE;
				}

				/* Does the primary key already exist? */
				if ( !empty($cols['primary']) )
				{
					$this->_error(E_USER_NOTICE, 'Primary key already set to \''.$cols['primary'].'\'');
					return FALSE;
				}

				/* Primary key must be integer, and auto_increment */
				if ( ( $cols[$arg['values']['name']]['type'] != 'int' ) || ( $cols[$arg['values']['name']]['auto_increment'] === FALSE ) )
				{
					$this->_error(E_USER_NOTICE, 'Primary key must be integer type, and auto increment');
					return FALSE;
				}

				/* Set the column as the primary */
				$cols['primary'] = $arg['values']['name'];

				/* Save the results */
				$fp = @fopen($filename.'.FRM', w)    or $this->_error(E_USER_ERROR, 'Could not open '.$filename.'.FRM for writing');
				      @flock($fp, LOCK_EX);
				      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Could not write to file '.$filename.'.FRM');
				      @flock($fp, LOCK_UN);
				      @fclose($fp)                   or $this->_error(E_USER_ERROR, 'Could not close '.$filename.'.FRM');

				/* Save files to cache */
				$this->_CACHE[$filename.'.FRM'] = $cols;
				return TRUE;
				break;

			/* ======================================================================
			 * DROP THE TABLE'S PRIMARY KEY
			 * ====================================================================== */
			case 'dropkey':
				/* Does the table have a primary key? */
				if ( empty($cols['primary']) )
				{
					$this->_error(E_USER_NOTICE, 'No Primary key exists for table '.$arg['table']);
					return FALSE;
				}

				/* Delete the primary key */
				$cols['primary'] = '';

				/* Save the results */
				$fp = @fopen($filename.'.FRM', w)    or $this->_error(E_USER_ERROR, 'Could not open '.$filename.'.FRM for writing');
				      @flock($fp, LOCK_EX);
				      @fwrite($fp, serialize($cols)) or $this->_error(E_USER_ERROR, 'Could not write to file '.$filename.'.FRM');
				      @flock($fp, LOCK_UN);
				      @fclose($fp)                   or $this->_error(E_USER_ERROR, 'Could not close '.$filename.'.FRM');

				/* Save files to cache */
				$this->_CACHE[$filename.'.FRM'] = $cols;
				return TRUE;
				break;

			default:
				$this->_error(E_USER_NOTICE, 'Invalid action specified for alter table query');
				return FALSE;
		}
		return FALSE;
	}

	/**
	 * Returns an array containing a list of the columns, and their
	 * corresponding properties
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return mixed cols An array populated with details on the fields in a table
	 * @access private
	 */
	function describe ($arg=NULL)
	{
		/* Inside of another database? */
		if ( !empty($arg['db']) )
		{
			if ( !$this->selectdb($arg['db']) )
			{
				return FALSE;
			}
		}

		/* Do we have a selected database? */
		if ( empty($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'No database selected');
			return FALSE;
		}

		/* Does table exist? */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/{$arg['table']}";
		if ( !(is_file($filename.'.MYD') && is_file($filename.'.FRM')) )
		{
			$this->_error(E_USER_NOTICE, 'Table '.$arg['table'].' doesn\'t exist');
			return FALSE;
		}

		/* Read in the column definitions */
		if ( ($cols = $this->_readFile($filename.'.FRM')) === FALSE )
		{
			$this->_error(E_USER_ERROR, 'Couldn\'t open file '.$filename.'.FRM for reading');
		}

		/* Return the information */
		$errorLevel = error_reporting(0);
		foreach ( $cols as $key => $col )
		{
			if ( $cols[$key]['type'] == 'enum' )
			{
				$cols[$key]['enum_val'] = unserialize($cols[$key]['enum_val']);
			}
		}
		error_reporting($errorLevel);
		return $cols;
	}

	/**
	 * Returns a list of all the databases in the current working directory
	 * @return mixed db An array populated with the list of databases in the CWD
	 * @access private
	 */
	function showdatabases ()
	{
		/* Can we open the directory up? */
		if ( ($fp = @opendir("$this->_LIBPATH")) === FALSE )
		{
			$this->_error(E_USER_ERROR, 'Could not open directory, '.$this->_LIBPATH.', for reading');
		}

		/* Make sure that it's a directory, and not a '..' or '.' */
		while ( ($file = @readdir($fp)) !== FALSE )
		{
			if ( $file != "." && $file != ".."  && strtolower($file) != 'txtsql' && is_dir("$this->_LIBPATH/$file") )
			{
				$db[] = $file;
			}
		}
		@closedir($fp);

		return isset($db) ? $db : array();
	}

	/**
	 * Creates a database with the given name inside of the CWD
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return void
	 * @access private
	 */
	function createdatabase ($arg=NULL)
	{
		/* Make sure that we have a name, and that it's valid */
		if ( empty($arg['db']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['db']) )
		{
			$this->_error(E_USER_NOTICE, 'Database name can only contain letters, and numbers');
			return FALSE;
		}

		/* Does the database already exist? */
		if ( $this->_dbexist($arg['db']) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$arg['db'].' already exists');
			return FALSE;
		}

		/* Go ahead and create the database */
		if ( ! ( mkdir("$this->_LIBPATH/$arg[db]", 0755) && chmod("$this->_LIBPATH/$arg[db]", 0755) ) )
		{
			$this->_error(E_USER_NOTICE, 'Error creating database '.$arg['db']);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Drops a database given that it exists within the CWD
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return void
	 * @access private
	 */
	function dropdatabase ($arg=NULL)
	{
		/* Do we have a valid name? */
		if ( empty($arg['db']) || !preg_match('/^[A-Za-z0-9_]+$/', $arg['db']) )
		{
			$this->_error(E_USER_NOTICE, 'Database name can only contain letters, and numbers');
			return FALSE;
		}
		elseif ( strtolower($arg['db']) == 'txtsql' )
		{
			$this->_error(E_USER_NOTICE, 'Cannot delete database txtsql');
			return FALSE;
		}

		/* Does database exist? */
		if ( !$this->_dbexist($arg['db']) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$arg['db'].' doesn\'t exist');
			return FALSE;
		}

		/* Make sure the database isn't locked */
		if ( $this->isLocked($arg['db']) )
		{
			$this->_error(E_USER_NOTICE, 'Database \''.$arg['db'].'\' is locked');
			return FALSE;
		}

		/* Remove any files inside of the directory */
		if ( ($fp = @opendir("$this->_LIBPATH/$arg[db]")) === FALSE )
		{
			$this->_error(E_USER_ERROR, 'Could not delete database '.$arg['db']);
		}

		while ( ($file = @readdir($fp)) !== FALSE )
		{
			if ( $file != "." && $file != ".." )
			{
				if ( is_dir("$this->_LIBPATH/$arg[db]/$file") || !@unlink("$this->_LIBPATH/$arg[db]/$file") )
				{
					$this->_error(E_USER_ERROR, 'Could not delete database '.$arg['db']);
				}
			}
		}
		@closedir($fp);

		/* Go ahead and delete the database */
		if ( !@rmdir("$this->_LIBPATH/$arg[db]") )
		{
			$this->_error(E_USER_ERROR, 'Could not delete database '.$arg['db']);
		}
		return TRUE;
	}

	/**
	 * Updates a database by changing its name
	 * @param mixed arg The arguments that are passed to the txtSQL as an array.
	 * @return void
	 * @access private
	 */
	function renamedatabase ($arg=NULL)
	{
		/* Valid database names? */
		if ( empty($arg[0]) || empty($arg[1]) || !preg_match('/^[A-Za-z0-9_]+$/', $arg[0]) || !preg_match('/^[A-Za-z0-9_]+$/', $arg[1]) )
		{
			$this->_error(E_USER_NOTICE, 'Database name can only contain letters, and numbers');
			return FALSE;
		}
		elseif ( strtolower($arg[0]) == 'txtsql' )
		{
			$this->_error(E_USER_NOTICE, 'Cannot rename database txtsql');
			return FALSE;
		}

		/* Does the old or new database exist? */
		if ( !$this->_dbexist($arg[0]) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$arg[0].' doesn\'t exist');
			return FALSE;
		}
		elseif ( $this->_dbexist($arg[1]) && strtolower($arg[0]) != strtolower($arg[1]) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$arg[1].' already exists');
			return FALSE;
		}

		/* Make sure the database isn't locked */
		if ( $this->isLocked($this->_SELECTEDDB) )
		{
			$this->_error(E_USER_NOTICE, 'Database '.$this->_SELECTEDDB.' is locked');
			return FALSE;
		}

		/* Do the renaming */
		if ( !@rename("$this->_LIBPATH/$arg[0]", "$this->_LIBPATH/$arg[1]") )
		{
			$this->_error(E_USER_ERROR, 'Could not rename database '.$arg[0].', to '.$arg[1]);
		}
			return TRUE;
	}
}
?>