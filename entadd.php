<?php
include 'init.php';

$next = count($entities) + 1;

$fields = [
	'number' => [
		'name' => 'number',
		'type' => 'hidden',
		'value' => $next
	],
	'name' => [
		'name' => 'name',
		'type' => 'text',
		'size' => 25,
		'maxlength' => 25
	],
	's1' => [
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Add'
	]
];

$form->set($fields);

view('Add New Entity', ['entities' => $entities, 'next' => $next], 'entadd2.php', 'entadd');
