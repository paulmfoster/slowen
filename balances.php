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

view('List Balances', ['today' => $today], 'balances2.php', 'balances', 'last_dt');

