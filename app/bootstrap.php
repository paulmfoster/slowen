<?php

define('DECIMALS', 2);
define('DECIMAL_SYMBOL', '.');

// account types
$acct_types = array(
	'A' => 'Asset',
	'C' => 'Checking',
	'R' => 'Credit Card',
	'Q' => 'Equity',
	'E' => 'Expense',
	'I' => 'Income',
	'L' => 'Liability',
	'S' => 'Savings'
);

// account type abbreviations
$atnames = array(
	' ' => '',
	'I' => '(inc)',
	'E' => '(exp)',
	'L' => '(liab)',
	'A' => '(asset)',
	'Q' => '(eqty)',
	'R' => '(ccard)',
	'C' => '(chkg)',
	'S' => '(svgs)'
);

// transaction statuses
$statuses = array(
	'C' => 'Cleared',
	'R' => 'Reconciled',
	'V' => 'Void',
	' ' => 'Uncleared'
);

// This code is common to many/most controllers
session_start();
if (!file_exists(CFGDIR . 'config.ini')) {
    copy(CFGDIR . 'config.sample', CFGDIR . 'config.ini');
}
$cfg = parse_ini_file(CFGDIR . 'config.ini');

$dsn = explode(':', $cfg['dsn']);
$not_there = FALSE;
if (!file_exists($dsn[1])) {
    $not_there = TRUE;
}
$db = load('database', $cfg['dsn']);
if ($not_there) {
    genpop($db, APPDIR . 'coldstart.php');
}

$db->logging($cfg['logging']);

load('errors');
load('messages');
$form = load('form');
load('numbers');
load('pdate');
load('xdate'); // added 2023-07-08

$nav_links = [
	'Accounts' => [
		'Register' => 'index.php?c=register&m=select',
		'Reconcile' => 'index.php?c=recon&m=prelim',
		'Add Account' => 'index.php?c=acct&m=add',
        'List Accounts' => 'index.php?c=acct&m=list',
		'Search By Account' => 'index.php?c=acct&m=search'
	],
	'Help' => [
		'Introduction' => 'index.php',
		'History' => 'index.php?c=welcome&m=history'
	],
    'Home' => 'index.php',
	'Payees' => [
		'Add Payee' => 'index.php?c=pay&m=add',
        'List Payees' => 'index.php?c=pay&m=list',
		'Search By Payee' => 'index.php?c=pay&m=search'
	],
    'Register' => [],
	'Reports' => [
		'Balances' => 'index.php?c=rpt&m=balances',
		'Register' => 'index.php?c=register&m=select',
		'Budget' => 'index.php?c=rpt&m=budget',
		'Weekly Expenses' => 'index.php?c=rpt&m=expenses',
		'Monthly Audit' => 'index.php?c=aud&m=monthly',
        'Yearly Audit' => 'index.php?c=aud&m=yearly',
        'View DB Log' => 'index.php?c=dblog&m=index'
	],
	'Scheduled' => [
		'Add Transaction' => 'index.php?c=sched&m=add',
		'Delete Transaction' => 'index.php?c=sched&m=delete',
        'List Transactions' => 'index.php?c=sched&m=list',
		'Activate Transaction' => 'index.php?c=sched&m=activate'
	],
	'Search' => [
		'Accounts/Categories' => 'index.php?c=acct&m=search',
		'Payees' => 'index.php?c=pay&m=search'
	],
    'Transaction' => 'index.php?c=atxn&m=add'
];

$reg = model('reg', $db);
$accts = $reg->get_from_accounts();
if ($accts != FALSE) {
    foreach ($accts as $acct) {
        $nav_links['Register'][$acct['name']] = 'index.php?c=register&m=show&id=' . $acct['id'];
    }
}

$nav = load('navigation');
$nav->init('T', $nav_links);

