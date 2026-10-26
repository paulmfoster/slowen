<?php

error_reporting(E_ALL);

function mklink($filename, $linkdir, $extdir)
{
    $success = true;
    if (!file_exists($linkdir . $filename)) {
        if (file_exists($extdir . $filename)) {
            if (!symlink($extdir . $filename, $linkdir . $filename)) {
                echo "Creating symlink failed: " . $linkdir . $filename . ' => ' . $extdir . $filename . '<br>';
                $success = false;
            }
        }
    }
    return $success;
}

// define system directories
define('SYSDIR', 'system/');

// define application directories
define('APPDIR', 'app/');
define('CFGDIR', APPDIR . 'config/');
define('PRINTDIR', APPDIR . 'printq/');
define('IMGDIR', APPDIR . 'images/');
define('DATADIR', APPDIR . 'data/');
define('MODELDIR', APPDIR . 'models/');
define('VIEWDIR', APPDIR . 'views/');
define('CTLDIR', APPDIR . 'controllers/');

// This code is common to many/most controllers
session_start();
if (!file_exists(CFGDIR . 'config.ini')) {
    copy(CFGDIR . 'config.sample', CFGDIR . 'config.ini');
}
$cfg = parse_ini_file(CFGDIR . 'config.ini');

// set up includes directory
$incdir = SYSDIR . 'includes';
if (!is_link($incdir)) {
    if (isset($cfg['extincdir'])) {
        if (!symlink($cfg['extincdir'], $incdir)) {
            dir("<h1>Fail on symlinking includes dir.</h1>");
        }
    }
    else {
        die("<h1><code>extincdir<code> not set in config file.</h1>");
    }
}
define('INCDIR', SYSDIR . 'includes/');

// set up library directory
$libdir = SYSDIR . 'libraries';
if (!is_link($libdir)) {
    if (isset($cfg['extlibdir'])) {
        if (!symlink($cfg['extlibdir'], $libdir)) {
            die("<h1>Fail on symlinking libraries dir.</h1>");
        }
    }
    else {
        die("<h1><code>extlibdir<code> not set in config file.</h1>");
    }
}
define('LIBDIR', SYSDIR . 'libraries/');

// provide common utilities
include INCDIR . 'utils.inc.php';

// database
$dsn = explode(':', $cfg['dsn']);
$not_there = FALSE;
if (!file_exists($dsn[1])) {
    $not_there = TRUE;
}
if ($not_there)
    $db = make_tables($cfg['dsn'], APPDIR . 'coldstart.sqlite');
else
    $db = load('database', $cfg['dsn']);

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


