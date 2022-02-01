<?php

$cfg = parse_ini_file('config/config.ini');

require_once 'functions.php';
require_once $cfg['incdir'] . 'errors.inc.php';
require_once $cfg['incdir'] . 'messages.inc.php';
require_once $cfg['incdir'] . 'numbers.inc.php';

library('pdate');
$form = library('form');
$nav = library('navigation');
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

