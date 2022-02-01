<?php

include 'functions.php';
fork('s1', 'P', 'index.php');

$cfg = parse_ini_file('config/config.ini');
include $cfg['incdir'] . 'messages.inc.php';
include $cfg['incdir'] . 'errors.inc.php';
$cfg['dbdata'] = $_POST['database_name'];
$db = library('database', $cfg);

make_tables($db);

if (array_key_exists('entity_name', $cfg)) {
	$nbr = count($cfg['entity_name']) + 1;
}
else {
	$nbr = 1;
}

$file = file_get_contents('config/config.ini');
$file .= "\nentity_name[" . $nbr . "] = \"${_POST['app_title']}\"";
$file .= "\nentity_data[" . $nbr . "] = ${_POST['database_name']}";

file_put_contents('config/config.ini', $file);

redirect('index.php');

