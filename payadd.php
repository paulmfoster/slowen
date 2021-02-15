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

$focus_field = 'name';
$page_title = 'Add Payee';
$view_file = view_file('payadd');
include 'view.php';

