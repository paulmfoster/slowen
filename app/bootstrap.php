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

load('errors');
load('messages');
$form = load('form');
load('numbers');
load('pdate');

$nav_links = [
	'Accounts' => [
		'Register' => url('register', 'select'),
		'Reconcile' => url('recon', 'prelim'),
		'Add Account' => url('acct', 'add'),
        'List Accounts' => url('acct', 'list'),
		'Search By Account' => url('acct', 'search')
	],
	'Help' => [
		'Introduction' => 'index.php',
		'History' => url('welcome', 'history')
	],
    'Home' => 'index.php',
	'Payees' => [
		'Add Payee' => url('pay', 'add'),
        'List Payees' => url('pay', 'list'),
		'Search By Payee' => url('pay', 'search')
	],
    'Register' => [],
	'Reports' => [
		'Balances' => url('rpt', 'balances'),
		'Register' => url('register', 'select'),
		'Budget' => url('rpt', 'budget'),
		'Weekly Expenses' => url('rpt', 'expenses'),
		'Monthly Audit' => url('aud', 'monthly'),
		'Yearly Audit' => url('aud', 'yearly')
	],
	'Scheduled' => [
		'Add Transaction' => url('sched', 'add'),
		'Delete Transaction' => url('sched', 'delete'),
        'List Transactions' => url('sched', 'list'),
		'Activate Transaction' => url('sched', 'activate')
	],
	'Search' => [
		'Accounts/Categories' => url('acct', 'search'),
		'Payees' => url('pay', 'search')
	],
    'Transaction' => url('atxn', 'add')
];

$reg = model('reg', $db);
$accts = $reg->get_from_accounts();
if ($accts != FALSE) {
    foreach ($accts as $acct) {
        $nav_links['Register'][$acct['name']] = url('register', 'show', $acct['id']);
    }
}

$nav = load('navigation');
$nav->init('T', $nav_links);

