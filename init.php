<?php

/**
 * @copyright 2021 Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license GPL2
 */

/**
 * instrument()
 *
 * Used in debugging. It shows the type and value of any variable.
 *
 * @param string $label What do you want to label this?
 * @param mixed $var What do you want to see?
 *
 */

function instrument($label, $var)
{
	echo $label . PHP_EOL;
	echo '<pre>' . PHP_EOL;
	print_r($var);
	echo '</pre>' . PHP_EOL;
}

function redirect($url)
{
	header("Location: $url");
	exit();
}

function model($name)
{
	global $cfg, $db;

	$filename = $cfg['modeldir'] . $name . '.mdl.php';
	if (!file_exists($filename)) {
		die("Model $name doesn't exist!");
	}
	require_once($filename);
	$obj = new $name($db);
	return $obj;
}

function view($page_title, $data, $return, $view_file, $focus_field = '')
{
	global $cfg, $nav, $form;

	extract($data);
	include $cfg['viewdir'] . 'head.view.php';
	include $cfg['viewdir'] . $view_file . '.view.php';
	include $cfg['viewdir'] . 'footer.view.php';
}

function fork($varname, $method, $failurl)
{
	if ($method == 'P') {
		$var = $_POST[$varname] ?? NULL;
	}
	elseif ($method == 'G') {
		$var = $_GET[$varname] ?? NULL;
	}
	if (is_null($var)) {
		header('Location: ' . $failurl);
		exit;
	}
	return $var;
}

$cfg = parse_ini_file('config/config.ini');

$entities = array();
foreach ($cfg['entity'] as $index => $value) {
	$entities[] = array('entity_num' => $index, 'entity_name' => $value);
}

// This code is called at the beginning of the application.
// It allows us to relocate our site anywhere without configuration.
// It defines $base_dir and $base_url.

$protocol = 'http://';
$http_host = $_SERVER['HTTP_HOST'];
$base_dir = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR;
$base_dir_len = strlen($base_dir);
$doc_root = $_SERVER['DOCUMENT_ROOT'];
$doc_root_len = strlen($doc_root);
if ($base_dir_len == $doc_root_len) {
	$app_subdir = '';
}                                                                                                                                               
else {
	$app_subdir = substr($base_dir, strlen($_SERVER['DOCUMENT_ROOT']) + 1);
}
$base_url = sprintf("%s%s/%s", $protocol, $http_host, $app_subdir);

$cfg['base_url'] = $base_url;
$cfg['base_dir'] = $base_dir;

// one month
ini_set('session.gc_maxlifetime', 2592000);
ini_set('session.cookie_lifetime', 2592000);
session_set_cookie_params(2592000);
session_name($cfg['session_name']);
session_start();

require_once $cfg['incdir'] . 'errors.inc.php';
require_once $cfg['incdir'] . 'numbers.inc.php';
require_once $cfg['incdir'] . 'messages.inc.php';
require_once $cfg['libdir'] . 'memory.lib.php';
require_once $cfg['libdir'] . 'form.lib.php';
$form = new form();
require_once $cfg['libdir'] . 'navigation.lib.php';
$nav = new navigation;
require_once 'links.php';
$nav->init('A', $links);
require_once $cfg['libdir'] . 'database.lib.php';
require_once $cfg['libdir'] . 'pdate.lib.php';

define('DECIMALS', 2);
define('DECIMAL_SYMBOL', '.');

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

$statuses = array(
	'C' => 'Cleared',
	'R' => 'Reconciled',
	'V' => 'Void',
	' ' => 'Uncleared'
);
$max_statuses = count($statuses);

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

// establish the entity

$sess_entity = $_SESSION['entity_num'] ?? NULL;
$post_entity = $_POST['entity_num'] ?? NULL;

if (is_null($post_entity)) {
	if (is_null($sess_entity)) {
		$_SESSION['entity_num'] = 1;
		$_SESSION['entity_name'] = $cfg['entity'][1];
	}
}
else {
	$_SESSION['entity_num'] = $_POST['entity_num'];
	$_SESSION['entity_name'] = $cfg['entity'][$_POST['entity_num']];
	emsg('S', "Entity has been set to {$_SESSION['entity_name']}.");
}

/*
$sess_entity = $_SESSION['entity_num'] ?? NULL;
$post_entity = $_POST['entity_num'] ?? NULL;

if (is_null($post_entity)) {
	if (is_null($sess_entity)) {
		$page_title = 'Set Entity';
		$view_file = view_file('entity');
		include 'view.php';
		exit();
	}
}
else {
	$_SESSION['entity_num'] = $_POST['entity_num'];
	$_SESSION['entity_name'] = $cfg['entity'][$_POST['entity_num']];
	emsg('S', "Entity has been set to {$_SESSION['entity_name']}.");
}

 */

// entity must be establish before this point, so we can instantiate the
// database

$cfg['dbdata'] = $cfg['app_nick'] . $_SESSION['entity_num'] . '.sq3';
$db = new database($cfg);


