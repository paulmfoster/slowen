<?php

$cfg = parse_ini_file('config/config.ini');
include $cfg['grottodir'] . 'misc.inc.php';
grotto('errors');
grotto('messages');
grotto('numbers');
grotto('pdate');
$form = grotto('form');
$nav = grotto('navigation');
$nav->init('L', []);

$fields = [
	'app_title' => [
		'name' => 'app_title',
		'type' => 'text',
		'size' => 20,
		'maxlength' => 20,
		'value' => 'Slowen',
		'label' => 'Application Title'
	],
	'database_name' => [
		'name' => 'database_name',
		'type' => 'text',
		'size' => 10,
		'maxlength' => 10,
		'label' => 'Database Name<br/>(no spaces)'
	],
	's1' => [
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Submit'
	]
];

$form->set($fields);
view('New Entity', [], 'entadd2.php', 'entadd', 'app_title');

