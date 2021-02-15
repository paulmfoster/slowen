<?php

include 'init.php';

$action = $_POST['s1'] ?? 'virgin';

if ($action == 'virgin') {
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
}
else {
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

		header('Location: ' . $cfg['base_url'] . 'index.php');
		exit();
	}
}

$focus_field = 'name';
$page_title = 'Bug/Feature Report';
$view_file = view_file('bugs');

include 'view.php';

