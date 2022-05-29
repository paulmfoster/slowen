<?php

// This class is a shell around the specific PDO driver class for a
// specific database.

class database
{
	var $dbh, $driver;

	/**
	 * Constructor.
	 *
	 * You must pass a $cfg array to this function which contains all the
	 * necessary fields to implement a connection to the database type you
	 * choose. At a minimum (for SQLite3):
	 * 
	 * 'dbdriv' => 'SQLite3',
	 * 'dbdata' => 'mydatabasefile'
	 * 
	 * See the PDO driver for your database to determine which fields must
	 * be defined.
	 *
	 */

	function __construct($dsn)
	{
        $parms = explode(':', $dsn);

		switch ($parms[0]) {
		case 'sqlite':
		case 'sqlite3':
            if (!class_exists('pdosqlite3')) {
			    include LIBDIR . 'pdosqlite3.lib.php';
            }
			$this->dbh = new pdosqlite3($dsn);
			break;
		case 'pg':
		case 'postgresql':
			include LIBDIR . 'pdopgsql.lib.php';
			$this->dbh = new dbpostgresql($dsn);
			break;
		case 'mysql':
			include LIBDIR . 'pdomysql.lib.php';
			$this->dbh = new dbmysql($dsn);
			break;
		}

		$this->driver = $parms[0];
	}

	function status()
	{
		return $this->dbh->status();
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
		$this->dbh->begin_transaction();
	}

	function query($sql)
	{
		return $this->dbh->query($sql);
	}

	function fetch()
	{
		return $this->dbh->fetch();
	}

	function fetch_all()
	{
		return $this->dbh->fetch_all();
	}

	function lastid($table)
	{
		return $this->dbh->lastid($table);
	}

	function insert($table, $record)
	{
		return $this->dbh->insert($table, $record);
	}

	function update($table, $fields, $where_clause)
	{
		return $this->dbh->update($table, $fields, $where_clause);
	}

	function delete($table, $where_clause = NULL)
	{
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
		return 2.5;
	}
};

