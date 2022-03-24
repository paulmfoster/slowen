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

$nav_links = [
	'Accounts' => [
		'Register' => 'index.php?url=register/select',
		'Reconcile' => 'index.php?url=recon/prelim',
		'Add Account' => 'index.php?url=acct/add',
		'Edit Account' => 'index.php?url=acct/select/edit',
		'Delete Account' => 'index.php?url=acct/select/delete',
		'Search By Account' => 'index.php?url=acct/search'
	],
	'Payees' => [
		'Add Payee' => 'index.php?url=pay/add',
		'Edit Payee' => 'index.php?url=pay/select/edit',
		'Delete Payee' => 'index.php?url=pay/select/delete',
		'Search By Payee' => 'index.php?url=pay/search'
	],
	'Transactions' => [
		'Check' => 'index.php?url=atxn/check',
		'Deposit' => 'index.php?url=atxn/deposit',
		'Credit Card' => 'index.php?url=atxn/ccard',
		'Transfer' => 'index.php?url=atxn/transfer',
		'Other/Split' => 'index.php?url=atxn/other'
	],
	'Scheduled' => [
		'Add Transaction' => 'index.php?url=sched/add',
		'Delete Transaction' => 'index.php?url=sched/delete',
        'List Transactions' => 'index.php?url=sched/list',
		'Activate Transaction' => 'index.php?url=sched/activate'
	],
	'Search' => [
		'Accounts/Categories' => 'index.php?url=acct/search',
		'Payees' => 'index.php?url=pay/search'
	],
	'Reports' => [
		'Balances' => 'index.php?url=rpt/balances',
		'Register' => 'index.php?url=register/select',
		'Budget' => 'index.php?url=rpt/budget',
		'Weekly Expenses' => 'index.php?url=rpt/expenses',
		'Monthly Audit' => 'index.php?url=aud/monthly',
		'Yearly Audit' => 'index.php?url=aud/yearly'
	],
	'Help' => [
		'Introduction' => 'index.php',
		'History' => 'index.php?url=welcome/history'
	]
];

// This code is common to many/most controllers
session_start();
$cfg = parse_ini_file(CFGDIR . 'config.ini');
$dsn = explode(':', $cfg['dsn']);
if (!file_exists($dsn[1])) {
    $db = make_tables($cfg['dsn'], APPDIR . 'coldstart.sqlite');
}
else {
    $db = load('database', $cfg['dsn']);
}
load('errors');
load('messages');
$form = load('form');
$nav = load('navigation');
$nav->init('A', $nav_links);
load('numbers');
load('pdate');

$init[0] = $cfg;
$init[1] = $form;
$init[2] = $nav;
$init[3] = $db;

