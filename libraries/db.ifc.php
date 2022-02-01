<?php

interface dbiface
{
	function status();
	function begin_transaction();
	function begin();
	function query($sql);
	function fetch();
	function fetch_all();
	function lastid($table);
	function insert($table, $record);
	function update($table, $fields, $where_clause);
	function delete($table, $where_clause);
	function commit();
	function end();
	function rollback();
	function version();
}

