<?php

/*
 * Purpose to take the existing database and modify it according to the
 * parameters of version 2.
 *
 * 2020-01-23
 *
 * Code, so far as tested is working.
 *
 * NOTE: This script takes a LONG TIME to run.
 *
 */

function instrument($label, $value)
{
	echo $label;
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}

include 'classes/database.lib.php';

///////////////////////// CONNECTION SECTION

// database connections

$fromcfg = array('dbdriv' => 'SQLite3', 'dbdata' => '/var/www/html/slowen/slowen.dta');
$from = new database($fromcfg);

$to1cfg = array('dbdriv' => 'SQLite3', 'dbdata' => '/home/paulf/public_html/slowen/slowen1.sq3');
$to1 = new database($to1cfg);

$to2cfg = array('dbdriv' => 'SQLite3', 'dbdata' => '/home/paulf/public_html/slowen/slowen2.sq3');
$to2 = new database($to2cfg);

echo 'Database connections made.<br/>';

///////////////////////// CREATION SECTION

// accounts table

$sql = 'DROP TABLE IF EXISTS accounts';
$to1->query($sql);
$to2->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS accounts (id integer primary key autoincrement, acct_id integer, parent integer, lft integer, rgt integer, open_dt date, recon_dt date, acct_type char(1), name varchar(35), descrip varchar(50), open_bal integer default 0, rec_bal integer default 0)';
$to1->query($sql);
$to2->query($sql);

echo 'Accounts tables created.<br/>';

// payees table

$sql = 'DROP TABLE IF EXISTS payees';
$to1->query($sql);
$to2->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS payees (id integer primary key autoincrement, payee_id integer, name varchar(35) not null)';
$to1->query($sql);
$to2->query($sql);

echo 'Payees table created.<br/>';

// transactions/journal table

$sql = 'DROP TABLE IF EXISTS journal';
$to1->query($sql);
$to2->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS journal (id integer primary key autoincrement, txnid integer not null, from_acct integer not null, txn_dt date not null, checkno varchar(12), split boolean default 0, payee_id integer, to_acct integer, memo varchar(35), status char(1) not null default ' ', recon_dt date, amount integer not null)";
$to1->query($sql);
$to2->query($sql);

echo 'Journal table created.<br/>';

// splits
		
$sql = 'DROP TABLE IF EXISTS splits';
$to1->query($sql);
$to2->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS splits (id integer primary key autoincrement, txnid integer not null references journal(txnid), to_acct integer not null references accounts(acct_id), memo varchar(35), payee_id integer references payees(payee_id), amount integer not null)';
$to1->query($sql);
$to2->query($sql);

echo 'Splits table created.<br/>';

///////////////// POPULATION SECTION

$sql = 'SELECT * FROM slaccounts01';
$accounts = $from->query($sql)->fetch_all();

foreach ($accounts as $acct) {
	$rec = array(
		'acct_id' => $acct['id'],
		'parent' => $acct['parent'],
		'lft' => $acct['lft'],
		'rgt' => $acct['rgt'],
		'open_dt' => $acct['open_dt'],
		'recon_dt' => $acct['recon_dt'],
		'acct_type' => $acct['acct_type'],
		'name' => $acct['name'],
		'descrip' => $acct['descrip'],
		'open_bal' => $acct['open_bal'],
		'rec_bal' => $acct['balance']
	);
	$to2->insert('accounts', $rec);
}

$sql = 'SELECT * FROM slaccounts02';
$accounts = $from->query($sql)->fetch_all();

foreach ($accounts as $acct) {
	$rec = array(
		'acct_id' => $acct['id'],
		'parent' => $acct['parent'],
		'lft' => $acct['lft'],
		'rgt' => $acct['rgt'],
		'open_dt' => $acct['open_dt'],
		'recon_dt' => $acct['recon_dt'],
		'acct_type' => $acct['acct_type'],
		'name' => $acct['name'],
		'descrip' => $acct['descrip'],
		'open_bal' => $acct['open_bal'],
		'rec_bal' => $acct['balance']
	);
	$to1->insert('accounts', $rec);
}

echo 'Accounts tables populated.<br/>';

$sql = 'SELECT * FROM slpayees01';
$payees = $from->query($sql)->fetch_all();

foreach ($payees as $payee) {
	$rec = array(
		'payee_id' => $payee['id'],
		'name' => $payee['name']
	);
	$to2->insert('payees', $rec);
}

$sql = 'SELECT * FROM slpayees02';
$payees = $from->query($sql)->fetch_all();

foreach ($payees as $payee) {
	$rec = array(
		'payee_id' => $payee['id'],
		'name' => $payee['name']
	);
	$to1->insert('payees', $rec);
}

echo 'Payees table populated.<br/>';

$sql = "SELECT * FROM sltransactions01 WHERE line_no = 0 OR (line_no = 1 AND txn_type = 'X')";
$txns = $from->query($sql)->fetch_all();

foreach ($txns as $txn) {
	$rec = array(
		'txnid' => $txn['txnid'],
		'from_acct' => $txn['from_acct'],
		'txn_dt' => $txn['txn_dt'],
		'checkno' => $txn['checkno'],
		'payee_id' => $txn['payee_id'],
		'to_acct' => $txn['to_acct'],
		'memo' => $txn['memo'],
		'status' => $txn['status'],
		'recon_dt' => $txn['recon_dt'],
		'amount' => $txn['amount']
	);

	$to2->insert('journal', $rec);
};

$sql = "SELECT * FROM sltransactions02 WHERE line_no = 0 OR (line_no = 1 AND txn_type = 'X')";
$txns = $from->query($sql)->fetch_all();

foreach ($txns as $txn) {
	$rec = array(
		'txnid' => $txn['txnid'],
		'from_acct' => $txn['from_acct'],
		'txn_dt' => $txn['txn_dt'],
		'checkno' => $txn['checkno'],
		'payee_id' => $txn['payee_id'],
		'to_acct' => $txn['to_acct'],
		'memo' => $txn['memo'],
		'status' => $txn['status'],
		'recon_dt' => $txn['recon_dt'],
		'amount' => $txn['amount']
	);

	$to1->insert('journal', $rec);
};

echo 'Journal table populated.<br/>';

$sql = "SELECT * FROM sltransactions01 WHERE txn_type = 'D' AND line_no != 0";
$splits = $from->query($sql)->fetch_all();

$txnids1 = array();
$txnids2 = array();

foreach ($splits as $split) {
	$txnids2[] = $split['txnid'];
	$rec = array(
		'txnid' => $split['txnid'],
		'to_acct' => $split['to_acct'],
		'memo' => $split['memo'],
		'payee_id' => $split['payee_id'],
		'amount' => $split['amount']
	);
	$to2->insert('splits', $rec);
}

$sql = "SELECT * FROM sltransactions02 WHERE txn_type = 'D' AND line_no != 0";
$splits = $from->query($sql)->fetch_all();

foreach ($splits as $split) {
	$txnids1[] = $split['txnid'];
	$rec = array(
		'txnid' => $split['txnid'],
		'to_acct' => $split['to_acct'],
		'memo' => $split['memo'],
		'payee_id' => $split['payee_id'],
		'amount' => $split['amount']
	);
	$to1->insert('splits', $rec);
}

echo 'Splits table populated.<br/>';

// update transactions table for splits

$max = count($txnids1);
for ($i = 0; $i < $max; $i++) {
	$to1->update('journal', array('split' => 1), "txnid = {$txnids1[$i]}");
}

$max = count($txnids2);
for ($i = 0; $i < $max; $i++) {
	$to2->update('journal', array('split' => 1), "txnid = {$txnids2[$i]}");
}

echo 'Transactions split field updated.<br/>';

$sql = "CREATE INDEX journal_ndx on journal (from_acct, txn_dt, checkno, txnid)";
$to1->query($sql);
$to2->query($sql);

echo 'Journal index created<br/>';

echo 'Program complete.<br/>';

echo date('c', time()) . '<br/>';

