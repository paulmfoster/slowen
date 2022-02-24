<?php

$cfgfile = 'config/config.ini';
if (!file_exists($cfgfile)) {
	copy('config/config.sample', 'config/config.ini');
}
$cfg = parse_ini_file('config/config.ini');

include 'grotto_check.php';
include $cfg['grottodir'] . 'misc.inc.php';

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

$db = grotto('database', $cfg);
if (!$db->status()) {
	make_tables($db);
}

grotto('errors');
grotto('messages');
grotto('memory');
grotto('numbers');
grotto('pdate');
$nav = grotto('navigation');
include 'navlinks.php';
$nav->init('A', $nav_links);

$form = grotto('form');

