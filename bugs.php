<?php

include 'init.php';

$fields = [
	'app_title' => [
		'name' => 'app_title',
		'type' => 'hidden',
		'value' => $cfg['app_name']
	],
	'name' => [
		'name' => 'name',
		'type' => 'text',
		'size' => 50,
		'maxlength' => 50
	],
	'email' => [
		'name' => 'email',
		'type' => 'text',
		'size' => 50,
		'maxlength' => 50,
		'required' => 1
	],
	'remark' => [
		'name' => 'remark',
		'type' => 'textarea',
		'rows' => 20,
		'cols' => 50,
		'size' => 1024,
		'required' => 1
	],
	's1' => [
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Submit'
	]
];

$form->set($fields);

$action = $_POST['s1'] ?? NULL;

if (!is_null($action)) {

	if (empty($_POST['email'])) {
		emsg('F', 'You must include a name and email');
	}
	elseif (empty($_POST['remark'])) {
		emsg('F', 'No comment = no response. Aborted.');
	}
	else {

		$msg = 'Application = ' . $cfg['app_name'] . "\n";
		$msg .= 'Name = ' . $_POST['name'] . "\n";
		$msg .= 'Email = ' . $_POST['email'] . "\n\n";
		$msg .= 'Remarks = ' . $_POST['remark'] . "\n\n";

		mail($cfg['programmer_email'], 'Bug Report or Feature Request for ' . $cfg['app_name'] , $msg);
		emsg('S', 'Thanks for your feedback. It is appreciated.');

		redirect('index.php');
	}
}

view('Bug/Feature Report', ['app_title' => $cfg['app_name']], 'bugs.php', 'bugs', 'name');

