<?php

/**
 * @copyright 2018 Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 */

$cfg = parse_ini_file('config/config.ini');

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

$css = $base_url . $cfg['app_nick'] . '.css';
$favicon = $base_url . 'favicon.ico';


// one month
ini_set('session.gc_maxlifetime', 2592000);
ini_set('session.cookie_lifetime', 2592000);
session_set_cookie_params(2592000);
session_name($cfg['session_cookie_name']);
session_start();

if (empty($_SESSION['entity_num'])) {
	header('Location: entity.php');
	exit();
}

$common_dir = 'common/';
include $common_dir . 'errors.inc.php';
include $common_dir . 'numbers.inc.php';
include $common_dir . 'messages.inc.php';
include $common_dir . 'navigation.inc.php';
include $common_dir . 'form.lib.php';
include $common_dir . 'database.lib.php';
include $common_dir . 'pdate.lib.php';
include $common_dir . 'slowen.mdl.php';

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

$cfg['dbdata'] = $cfg['app_nick'] . $_SESSION['entity_num'] . '.sq3';
$db = new database($cfg);

$sm = new slowen($db);

include 'navlinks.php';

$date_template = $cfg['date_template'];

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
