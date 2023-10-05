<?php

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

// load the front controller
load('controller');

// include optional user code an definitions here, before being routed to
// the page controller
if (file_exists(APPDIR . 'bootstrap.php')) {
	include APPDIR . 'bootstrap.php';
}

// 2023-09-24 change routing scheme
// branch to the router
// $rtr = load('router');

$controller = $_GET['c'] ?? 'welcome';
$method = $_GET['m'] ?? 'index';

if (!file_exists(CTLDIR . $controller . '.php')) {
    $controller = 'welcome';
}
include CTLDIR . $controller . '.php';
$controller = new $controller;

if (!method_exists($controller, $method)) {
    $msg = "Controller: " . $controller . " Method: " . $method . " don't exist.";
    emsg('F', $msg);
    exit;
}

unset($_GET['c']);
unset($_GET['m']);

// call_user_func_array([$controller, $method], $_GET);
call_user_func_array([$controller, $method], $_GET);

