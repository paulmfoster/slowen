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

function get_or_post($parm)
{
	if (isset($_GET[$parm])) {
		$method = 'G';
		$retval = $_GET[$parm];
	}
	elseif (isset($_POST[$parm])) {
		$method = 'P';
		$retval = $_POST[$parm];
	}
	else {
		$method = 'X';
		$retval = NULL;
	}

	return [$method, $retval];
}

function relocate($url)
{
	header("Location: $url");
	exit();
}

function load_model($name)
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

function in_get($varname = NULL, $destination = NULL)
{
	if (is_null($destination)) {
		relocate('index.php');
	}
	if (is_null($varname)) {
		relocate($destination);
	}

	$var = $_GET[$varname] ?? NULL;
	if (is_null($var)) {
		relocate($destination);
	}
	return $var;
}

function in_post($varname = NULL, $destination = NULL)
{
	if (is_null($destination)) {
		relocate('index.php');
	}
	if (is_null($varname)) {
		relocate($destination);
	}

	$var = $_POST[$varname] ?? NULL;
	if (is_null($var)) {
		relocate($destination);
	}
	return $var;
}

function view_file($name)
{
	global $cfg;
	return $cfg['viewdir'] . $name . '.view.php';
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

include $cfg['incdir'] . 'errors.inc.php';
include $cfg['incdir'] . 'numbers.inc.php';
include $cfg['incdir'] . 'messages.inc.php';
include $cfg['libdir'] . 'memory.lib.php';
include $cfg['libdir'] . 'form.lib.php';
$form = new form();
include $cfg['libdir'] . 'database.lib.php';
include $cfg['libdir'] . 'pdate.lib.php';

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

// entity must be establish before this point, so we can instantiate the
// database

$cfg['dbdata'] = $cfg['app_nick'] . $_SESSION['entity_num'] . '.sq3';
$db = new database($cfg);


