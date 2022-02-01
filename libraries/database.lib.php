<?php

class database
{
	var $dbh, $driver;

	function __construct($cfg)
	{
		$dvr = strtolower($cfg['dbdriv']);
		switch ($dvr) {
		case 'sqlite':
		case 'sqlite3':
			require_once($cfg['libdir'] . 'pdosqlite3.lib.php');
			$this->dbh = new pdosqlite3($cfg);
			break;
		case 'pg':
		case 'postgresql':
			require_once($cfg['libdir'] . 'dbpostgresql.lib.php');
			$this->dbh = new dbpostgresql($cfg);
			break;
		case 'mysql':
			require_once($cfg['libdir'] . 'dbmysql.lib.php');
			$this->dbh = new dbmysql($cfg);
			break;
		}

		$this->driver = $dvr;
	}

	function status()
	{
		return $this->dbh->status();
	}

	function fatal($pgmr_message)
	{
		error_log("DATABASE ERROR: " . date('c') . "\n", 3, 'error.log');
		error_log($pgmr_message, 3, 'error.log');
		error_log(trace());
		die('FATAL DATABASE ERROR. Contact system adminstrator.');
		return false;
	}

	function datadict()
	{
		$this->dbh->datadict();
	}

	static public function quote($value)
	{
		$quoted = str_replace("'", "''", $value);
		return "'" . $quoted . "'";
	}

	/**
	 * prepare()
	 *
	 * Does what the above functions do, all in one step:
	 * quotes string values, turns boolean values into 0 or 1 integer
	 * values, or (default) simply copies them to our new array.
	 *
	 * @param string $table which table in the $dd?
	 * @param array $rec Associative array ['field_name' => field_value, * ...]
	 *
	 * @return array An array with appropriately "repaired" values
	 *
	 */

	function prepare($table, $rec) 
	{
		return $this->dbh->prepare($table, $rec);
	}

	function begin_transaction()
	{
		$this->dbh->begin_transaction();
	}

	function begin()
	{
		$this->begin_transaction();
	}

	/**
	 * query()
	 *
	 * Execute any sql statement
	 *
	 * @param string $sql The SQL statement
	 *
	 * @return reference this object
	 */

	function query($sql)
	{
		return $this->dbh->query($sql);
	}

	/**
	 * fetch()
	 *
	 * @return array A single row of query results or FALSE if no rows
	 *
	 */

	function fetch()
	{
		return $this->dbh->fetch();
	}

	/**
	 * fetch_all()
	 *
	 * Fetches an indexed/associative array of all results from a query
	 *
	 * @return array Indexed array of records returned from prior query
	 *
	 */

	function fetch_all()
	{
		return $this->dbh->fetch_all();
	}

	/**
	 * lastid()
	 *
	 * Fetches the index number for the last record stored
	 *
	 * NOTE: This function does not deal with concurrency issues
	 *
	 * @param string $table Name of table to query
	 * @param string $fieldname Name of index/ID field
	 *
	 * @return integer Last ID
	 *
	 */

	function lastid($table)
	{
		return $this->dbh->lastid($table);
	}


	/**
	 * insert()
	 *
	 * Special case of query(), where the input is, instead of a SQL
	 * statement, an associative array of fieldnames and values.
	 *
	 * For string and some other data types, values must be quoted, or
	 * SQLite3 will generate an exception.
	 *
	 * @param string $table The table to be inserted into
	 * @param array $records The indexed/associated table of
	 * fieldnames/values.
	 *
	 */

	function insert($table, $record)
	{
		if (empty($table)) {
			$this->fatal('INSERT statement with no table name');
		}
		if (empty($record)) {
			$this->fatal('INSERT statement with no field data');
		}
		$this->dbh->insert($table, $record);
	}

	/**
	 * update()
	 *
	 * Implements the UPDATE statement.
	 *
	 * NOTE: If a field should be quoted, ensure you do it beforehand.
	 *
	 * @param string $table Table name
	 * @param array Associative array of fields and values
	 * @param string Where clause
	 */

	function update($table, $fields, $where_clause)
	{
		if (empty($table))
			$this->fatal('UPDATE statement with no table name');
		if (empty($fields))
			$this->fatal('UPDATE statement with no field data');
		if (empty($where_clause))
			$this->fatal('UPDATE statement with no where clause');

		return $this->dbh->update($table, $fields, $where_clause);
	}

	/**
	 * delete()
	 *
	 * Simplified implementation of SQL DELETE command
	 *
	 * @param string $table The table
	 * @param string $where_clause The WHERE clause
	 */

	function delete($table, $where_clause = NULL)
	{
		if (empty($table)) {
			$this->fatal('DELETE statement with no table name');
		}

		$this->dbh->delete($table, $where_clause);
	}

	function commit()
	{
		$this->dbh->commit();
	}

	function end()
	{
		$this->dbh->end();
	}

	function rollback()
	{
		$this->dbh->rollback();
	}

	function version()
	{
		return 2.1;
	}
};

