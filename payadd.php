<?php

include 'init.php';

$fields = array(
	'name' => array(
		'name' => 'name',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Save'
	)
);

$form->set($fields);

view('Add Payee', [], 'payadd2.php', 'payadd', 'name');


