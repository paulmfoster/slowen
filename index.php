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

// load basic error/messaging code
load('errors');
load('messages');

// load the front controller
load('controller');

// include optional user code an definitions here, before being routed to
// the page controller
if (file_exists(APPDIR . 'bootstrap.php')) {
	include APPDIR . 'bootstrap.php';
}

// branch to the router
$rtr = load('router');

