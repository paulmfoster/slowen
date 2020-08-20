<?php

// @copyright  2020, Paul M. Foster <paulf@quillandmouse.com>

include 'init.php';

$action = $_POST['s1'] ?? 'virgin';

if ($action == 'virgin') {
	$fields = [
		'app_title' => [
			'name' => 'app_title',
			'type' => 'hidden',
			'value' => $app_name
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
			'maxlength' => 50
		],
		'remark' => [
			'name' => 'remark',
			'type' => 'textarea',
			'rows' => 20,
			'cols' => 50,
			'size' => 1024
		],
		's1' => [
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Submit'
		]
	];

	$form = new form($fields);
}
else {
	if (empty($_POST['email'])) {
		emsg('F', 'You must include a name and email');
	}
	elseif (empty($_POST['remark'])) {
		emsg('F', 'No comment = no response. Aborted.');
	}
	else {

		$msg = 'Application = ' . $app_name . "\n";
		$msg .= 'Name = ' . $_POST['name'] . "\n";
		$msg .= 'Email = ' . $_POST['email'] . "\n\n";
		$msg .= 'Remarks = ' . $_POST['remark'] . "\n\n";

		mail('paulf@quillandmouse.com', 'Bug Report or Feature Request for ' . $app_name , $msg);
		emsg('S', 'Thanks for your feedback. It is appreciated.');

		header('Location: ' . $base_url . 'index.php');
		exit();
	}
}

$focus_field = 'name';
$page_title = 'Bug/Feature Report';
$view_file = 'views/bugs.view.php';

include 'view.php';

