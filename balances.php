<?php

include 'init.php';

$fields = [
	'last_dt' => [
		'name' => 'last_dt',
		'type' => 'date'
	],
	's1' => [
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Compute'
	]
];

$form->set($fields);

$today = pdate::now2iso();

$focus_field = 'last_dt';
$page_title = 'List Balances';
$view_file = view_file('balances');
include 'view.php';

