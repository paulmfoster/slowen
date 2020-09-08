<?php

$cfg = parse_ini_file('config/config.ini');

// session duration: one month

ini_set('session.gc_maxlifetime', 2592000);
session_set_cookie_params(2592000);

session_name($cfg['session_name']);
session_start();

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

$common_dir = 'common/';
include $common_dir . 'errors.inc.php';
include $common_dir . 'navigation.inc.php';
include $common_dir . 'messages.inc.php';

include 'navlinks.php';

$entities = array();
foreach ($cfg['entity'] as $index => $value) {
	$entities[] = array('entity_num' => $index, 'entity_name' => $value);
}

// process user input
	
if (!empty($_POST)) {
	$num = count($entities);
	for ($i = 0; $i < $num; $i++) {
		if ($_POST['entity_num'] == $entities[$i]['entity_num']) {
			$_SESSION['entity_num'] = $_POST['entity_num'];
			$_SESSION['entity_name'] = $entities[$i]['entity_name'];
			emsg('S', "Entity has been set to {$_SESSION['entity_name']}.");
			break;
		}
	}
}

// set up for page display

$page_title = 'Select Entity';
$view_file = 'views/entity.view.php';
include 'view.php';

