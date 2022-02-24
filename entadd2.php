<?php

$cfg = parse_ini_file('config/config.ini');
include $cfg['grottodir'] . 'misc.inc.php';
fork('s1', 'P', 'index.php');

grotto('messages');
grotto('errors');

$cfg['dbdata'] = $_POST['database_name'];
$db = grotto('database', $cfg);

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

