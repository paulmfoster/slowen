<?php

require_once 'classes/db.ifc.php';

/**
 * pdosqlite3 class
 *
 * This class applies the PDO class to the SQLite3 database
 * type. PDO has a design weakness under normal use, where
 * its internal design uses a PDO object and a PDOStatement
 * object. If you used this class as is, your database
 * operations would be needlessly complex because of this.
 * So this class acts as a cover over the PDO classes, to make
 * the programmer's life easier.
 *
 */

/*
 * 2020-03-05 In development, I've had numerous errors where
 * It's not clear at the outset what the error is. In almost
 * all cases, they are SQL or PDO errors which this driver was
 * not set up to handle. On this date, I've added the fatal()
 * function, and blocks of code in query(), insert() and
 * update() to catch these errors and insert the information
 * into the log. The user will see only that there is an error.
 */

class pdosqlite3 implements dbiface
{
	var $handle, $dd = array();

	function __construct($filename)
	{
		$connect_string = 'sqlite:' . $filename;
		try {
			$this->handle = new PDO($connect_string);
		}
		catch (PDOException $e) {
			die('Cannot connect to database!');
		}
	}

	function fatal($function, $sql)
	{
		error_log("DATE: " . date('c') . "\n", 3, 'error.log');
		error_log("ERROR: Failure in $function:\n", 3, 'error.log');
		error_log("SQL: $sql\n", 3, 'error.log');
		$pdo_message = var_export($this->handle->errorInfo()[2], TRUE);
		error_log("PDO error: $pdo_message\n", 3, 'error.log');

		$msg = "\nTRACE:\n--------------------------\n";

		$backtrace = debug_backtrace();
		foreach ($backtrace as $key => $trace) {
			if ($key != 0) {
				$msg .= "#$key ";
				if (array_key_exists('file', $trace)) {
					$msg .= $trace['file'];
				}
				if (array_key_exists('line', $trace)) {
					$msg .= ":$trace[line]";
				}
				if (array_key_exists('class', $trace)) {
					$msg .= ":$trace[class]";
				}
				if (array_key_exists('type', $trace)) {
					$msg .= " $trace[type] " ; // ->, ::, or nothing
				}
				if (array_key_exists('function', $trace)) {
					$msg .= " $trace[function](";
					if (array_key_exists('args', $trace)) {
						$first = true;
						foreach ($trace['args'] as $arg) {
							if (!$first) {
								$msg .= ', ';
							}
							if (is_object($arg))
								$msg .= get_class($arg) . ' OBJECT ';
							else {
								if (is_string($arg)) {
									$msg .= $arg;
								}
								else {
									$msg .= 'TYPE: ' . gettype($arg);
								}
							}
							if ($first)
								$first = false;
						}
					}	
					$msg .= ')';
				}
				$msg .= "\n";
			}
		}
		$msg .= "\n";

		error_log($msg, 3, 'error.log');
		error_log("-\n", 3, 'error.log');
		die('FATAL database error, Aborting');
	}
	
	/**
	 * datadict()
	 *
	 * Because SQLite does not implement INFORMATION_SCHEMA, we have to
	 * find some other way to obtain metadata about tables and columns.
	 * This code is based on a StackOverflow answer.
	 *
	 * NOTE: I learned the hard way-- the pragma_table_info() and
	 * similar functions were added in SQLite version 3.16.0. If PHP's
	 * version of SQLite is less than this, the query will fail with
	 * inscrutable errors. Run phpinfo() to determine your version of
	 * PHP and the SQLite3 module.
	 *
	 * result table looks like this:
	 *
	 * table_name|column_name|column_type|is_nullable|column_default|pkey
	 *
	 * @return array Array of column descriptions
	 *
	 */

	function datadict()
	{
		$sql = "SELECT m.name AS table_name, p.name AS column_name, p.type AS column_type, not `notnull` AS is_nullable, p.dflt_value AS column_default, p.pk AS pkey FROM sqlite_master AS m JOIN pragma_table_info(m.name) AS p WHERE m.type = 'table' ORDER BY m.name, p.cid";
		$this->dd = $this->query($sql)->fetch_all();
	}

	/**
	 * quote()
	 *
	 * Adds single quotes before and after string values,
	 * turns internal single quotes into double single quotes
	 *
	 * NOTE: This is a static function
	 *
	 * @param string $value The value to be quoted
	 *
	 * @return string The quoted value
	 *
	 */

	static public function quote($value)
	{
		$quoted = str_replace("'", "''", $value);
		return "'" . $quoted . "'";
	}

	/**
	 * prepare()
	 *
	 * In earlier incarnations, this function quoted strings, converted
	 * boolean values to integer (SQLite3), etc.
	 *
	 * Currently, this function discards all fields not part of the
	 * table in question, and leaves only the fields which are actually
	 * part of the table.
	 *
	 * Typically, for functions like insert(), the function is fed the
	 * whole $_POST array. This function strips items from the array
	 * which might be buttons or somesuch, and leaves only appropriate
	 * fields.
	 *
	 * @param string $table which table in the $dd?
	 * @param array $rec Associative array ['field_name' => field_value, * ...]
	 *
	 * @return array An array with appropriately "repaired" values
	 *
	 */

	function prepare($table, $rec) 
	{
		if (empty($this->dd)) {
			$this->datadict();	
		}

		$prepped = array();
		foreach ($this->dd as $column) {
			if ($column['table_name'] != $table) {
				continue;
			}

			$column_name = $column['column_name'];
			if (isset($rec[$column_name])) {
				$prepped[$column_name] = $rec[$column_name];
			}
		}

		return $prepped;
	}

	function begin_transaction()
	{
		$this->handle->beginTransaction();
	}

	function begin()
	{
		$this->handle->beginTransaction();
	}

	/**
	 * query()
	 *
	 * Execute any sql statement
	 * NOTE: You may chain other routines from here.
	 *
	 * @param string $sql The SQL statement
	 *
	 * @return reference this object
	 */

	function query($sql)
	{
		$this->result = $this->handle->query($sql);

		if ($this->result === FALSE) {
			$this->fatal('PDO::query()', $sql);
		}

		return $this;
	}

	/**
	 * fetch()
	 *
	 * @return array A single row of query results or FALSE if no rows
	 *
	 */

	function fetch()
	{
		// PDOStatement::fetch() returns FALSE on no results
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * fetch_all()
	 *
	 * Fetches an indexed/associative array of all results from a query
	 *
	 * @return array Indexed/associative array of results
	 *
	 * @return array Indexed array of records returned from prior query
	 *
	 */

	function fetch_all()
	{
		// PDOStatement::fetchAll returns an empty array on no results
		$recs = $this->result->fetchAll(PDO::FETCH_ASSOC);
		// change return to FALSE for consistency with fetch()
		return empty($recs) ? FALSE : $recs;
	}


	/**
	 * lastid()
	 *
	 * Fetches the index number for the last record stored
	 *
	 * NOTE: This function does not deal with concurrency issues
	 *
	 * @param string $table Name of table to query
	 *
	 * @return integer Last ID
	 *
	 */

	function lastid($table)
	{
		$sql = "SELECT seq FROM sqlite_sequence WHERE name = '$table'";
		$seq_rec = $this->query($sql)->fetch();

		return ($seq_rec['seq']);
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
		$fields = array_keys($record);
		$fieldnames = implode(', ', $fields);

		$pms = array();
		foreach($record as $field => $value) {
			$pms[] = '?';
		}
		$placemarkers = implode(', ', $pms);

		$values = array_values($record);

		$sql = "INSERT INTO $table ($fieldnames) VALUES ($placemarkers)";

		$this->result = $this->handle->prepare($sql);

		if ($this->result === FALSE) {
			$this->fatal('PDO::prepare()', $sql);
		}

		$this->result->execute($values);
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

	function update($table, $record, $where_clause)
	{
		$fields = array_keys($record);
		$values = array_values($record);

		$max_terms = count($fields);
		for ($i = 0; $i < $max_terms; $i++) {
			$str = $fields[$i] . ' = ?';
			$terms[] = $str;
		}

		$fields_clause = implode(', ', $terms);
		$sql = "UPDATE $table SET $fields_clause WHERE $where_clause";
		$this->result = $this->handle->prepare($sql);

		if ($this->result === FALSE) {
			$this->fatal('PDO::prepare()', $sql);
		}

		$this->result->execute($values);
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
		if (!is_null($where_clause)) {
			$sql = "DELETE FROM $table WHERE $where_clause";
		}
		else {
			$sql = "DELETE FROM $table";
		}
		$this->query($sql);
	}

	function commit()
	{
		$this->handle->commit();
	}

	function end()
	{
		$this->handle->commit();
	}

	function rollback()
	{
		$this->handle->rollBack();
	}

	function version()
	{
		return 1.5;
	}

}

