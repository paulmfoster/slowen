<?php

/* #############################################################
 * Copyrights and Licenses
 * ############################################################# */

/**
 * @copyright 2018 Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 * @version 
 */

/* #############################################################
 * Configuration
 * ############################################################# */

$cfg = parse_ini_file('config/config.ini');

/* #############################################################
 * Session start
 * ############################################################# */

ini_set('session.gc_maxlifetime', 2592000);
ini_set('session.cookie_lifetime', 2592000);
session_set_cookie_params(2592000);
session_name($cfg['session_cookie_name']);
session_start();

if (empty($_SESSION['entity_num'])) {
	header('Location: entity.php');
	exit();
}

/* #############################################################
 * Names and Locations
 * ############################################################# */


$app_subdir = 'slowen';
$app_nick = 'slowen';
$app_name = 'Slowen';
$app_prefix = 'sl';
$app_title = 'Slowen';

$protocol = 'http://';
$http_host = $_SERVER['HTTP_HOST'];

$base_dir = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR;
$base_url = sprintf("%s%s/%s%s", $protocol, $http_host, $app_subdir, DIRECTORY_SEPARATOR);

$css = $base_url . $app_nick . '.css';
$favicon = $base_url . 'favicon.ico';


/* #############################################################
 * App-specific Includes
 * ############################################################# */
 
include 'includes/errors.inc.php';
include 'includes/numbers.inc.php';
include 'includes/messages.inc.php';
include 'includes/navigation.inc.php';
include 'classes/form.lib.php';

/* #############################################################
 * Messages
 * ############################################################# */

include 'messages.php';

/* #############################################################
 * Configuration
 * ############################################################# */

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

/* #############################################################
 * Database Connection (if any)
 * ############################################################# */

include 'classes/database.lib.php';
$cfg['dbdata'] = 'slowen' . $_SESSION['entity_num'] . '.sq3';
$db = new database($cfg);

include 'classes/slowen.mdl.php';
$sm = new slowen($db);

/* #############################################################
 * Miscellaneous Definitions
 * ############################################################# */

include 'navlinks.php';

include 'classes/date.lib.php';
$date_template = $cfg['date_template'];

/* #############################################################
 * Common functions
 * ############################################################# */

