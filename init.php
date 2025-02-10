<?php

error_reporting(E_ALL);

// CAUTION: Edit this file at your own risk. Upgrades to Grotworx may
// overwrite any changes made.

// define system directories
define('SYSDIR', 'system/');
define('INCDIR', SYSDIR . 'includes/');
define('LIBDIR', SYSDIR . 'libraries/');

// define application directories
define('APPDIR', 'app/');
define('CFGDIR', APPDIR . 'config/');
define('PRINTDIR', APPDIR . 'printq/');
define('IMGDIR', APPDIR . 'images/');
define('DATADIR', APPDIR . 'data/');
define('MODELDIR', APPDIR . 'models/');
define('VIEWDIR', APPDIR . 'views/');
define('CTLDIR', APPDIR . 'controllers/');

// provide common utilities
include INCDIR . 'utils.inc.php';
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
load('xdate'); // added 2023-07-08

$nav_links = [
	'Accounts' => [
		'Register' => 'register.php',
		'Reconcile' => 'prerecon.php',
		'Add Account' => 'addacct.php',
        'List Accounts' => 'listacct.php',
		'Search By Account' => 'srchacct.php'
	],
    'Budget' => [
        'Show Budget' => 'showbgt.php',
        'Add Account' => 'addbline.php',
        'Edit Account' => 'editbline.php',
        'Delete Account' => 'delbline.php',
        'New Week' => 'editbgt.php',
        'Print Budget' => 'printbgt.php',
        'Help/Design' => 'helpbgt.php'
    ],
	'Help' => [
		'Introduction' => 'index.php',
		'History' => 'history.php'
	],
    'Home' => 'index.php',
	'Payees' => [
		'Add Payee' => 'addpayee.php',
        'List Payees' => 'listpayee.php',
		'Search By Payee' => 'srchpayee.php'
	],
    'Register' => [],
	'Reports' => [
		'Balances' => 'balances.php',
		'Register' => 'register.php',
		'Budget' => 'bgtrpt.php',
		'Weekly Expenses' => 'showexp.php',
		'Monthly Audit' => 'maudit.php',
        'Yearly Audit' => 'yaudit.php'
	],
	'Scheduled' => [
		'Add Transaction' => 'addsched.php',
		'Delete Transaction' => 'delsched.php',
        'Edit Transaction' => 'editsched.php',
        'List Transactions' => 'listsched.php',
		'Activate Transaction' => 'actsched.php'
	],
	'Search' => [
		'Accounts/Categories' => 'srchacct.php',
		'Payees' => 'srchpayee.php'
	],
    'Transaction' => 'addtxn.php'
];

// used to populate "Register" menu choice
$reg = model('reg', $db);
$accts = $reg->get_from_accounts();
if ($accts != FALSE) {
    foreach ($accts as $acct) {
        $nav_links['Register'][$acct['name']] = 'register.php?id=' . $acct['id'];
    }
}

$nav = load('navigation');
$nav->init('T', $nav_links);

// NOTE: This code taken from system/controller.lib.php.

/**
 * Model
 *
 * Instantiate the model and return the object.
 *
 * @param string The model
 * @param mixed Parameters
 * @return object The constructed class object
 */

// function model($model, $params = NULL)
// {
//     include MODELDIR . $model . '.php';
//     if (is_null($params)) {
//         return new $model();
//     }
//     else {
//         return new $model($params);
//     }
// }

/**
 * View
 *
 * Include the view file with any parameters
 *
 * @param string View file
 * @param array Any data to pass
 */

function view($view_file, $data = NULL)
{
    if (file_exists(VIEWDIR . $view_file)) {
        include VIEWDIR . $view_file;
    }
    else {
        die('View file ' . VIEWDIR . $view_file . ' does not exist');
    }
    exit();
}


