<?php

$cfgfile = 'config/config.ini';
if (!file_exists($cfgfile)) {
	copy('config/config.sample', 'config/config.ini');
}
$cfg = parse_ini_file('config/config.ini');

/* =========== GROTTO CODE CHECK ============= */

if (!file_exists($cfg['incdir']) || !file_exists($cfg['libdir'])) {
	$message = <<< EOT

This software relies on another package called "grotto", and I can't find
it on your system. It should be available from where you got this software.
Download it from there and install it, ideally located outside the tree for
this software. Optionally, you may locate it within the tree for this
software. In your main software, you should have a file called
<code>config/config.ini</code>.  Look for the following two lines in it:

incdir = "../grotto/"
libdir = "../grotto/"

Edit those lines to point to the the location where you downloaded the
"grotto" package.

EOT;
	
	die(nl2br($message));
}

/* ========== END GROTTO CODE CHECK =========== */

include $cfg['incdir'] . 'misc.inc.php';

// 2592000 = 30 days
ini_set('session.gc_maxlifetime', 2592000);
ini_set('session.cookie_lifetime', 2592000);
session_name($cfg['app_nick']);
session_set_cookie_params(2592000); // one month
session_start();

// handle entity
$post_entity = $_POST['entity_num'] ?? NULL;
$sess_entity = $_SESSION['entity_num'] ?? NULL;
$cfg_entity = array_key_exists('entity_name', $cfg);

if (!$cfg_entity) {
	redirect('entadd.php');
}
elseif (!is_null($post_entity)) {
	$_SESSION['entity_num'] = $_POST['entity_num'];
	$_SESSION['entity_name'] = $cfg['entity_name'][$_POST['entity_num']];
	$_SESSION['entity_data'] = $cfg['entity_data'][$_POST['entity_num']];
}
elseif (is_null($sess_entity)) {
	$_SESSION['entity_num'] = 1;
	$_SESSION['entity_name'] = $cfg['entity_name'][1];
	$_SESSION['entity_data'] = $cfg['entity_data'][1];
}

$cfg['dbdata'] = $_SESSION['entity_data'];

// definitions
define('DECIMALS', 2);
define('DECIMAL_SYMBOL', '.');

// account types
$acct_types = array(
	'I' => 'Income',
	'E' => 'Expense',
	'L' => 'Liability',
	'A' => 'Asset',
	'Q' => 'Equity',
	'R' => 'Credit Card',
	'C' => 'Checking',
	'S' => 'Savings'
);
$max_acct_types = count($acct_types);

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
$max_statuses = count($statuses);

$db = library('database', $cfg);
if (!$db->status()) {
	make_tables($db);
}

include $cfg['incdir'] . 'errors.inc.php';
include $cfg['incdir'] . 'messages.inc.php';
library('memory');
include $cfg['incdir'] . 'numbers.inc.php';
library('pdate');
$nav = library('navigation');
include 'navlinks.php';
$nav->init('A', $nav_links);

$form = library('form');

