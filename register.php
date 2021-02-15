<?php

include 'init.php';

$acct = load_model('account');
$accounts = $acct->get_from_accounts();
$acct_options = [];
foreach ($accounts as $acct) {
	$acct_options[] = ['lbl' => $acct['name'], 'val' => $acct['acct_id']];
}

$fields = [
	'acct_id' => [
		'name' => 'acct_id',
		'type' => 'select',
		'options' => $acct_options
	],
	's1' => [
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Report'
	]
];

$form->set($fields);

$page_title = 'Register: Select Account';
$view_file = view_file('accounts');
$destination = 'register2.php';
include 'view.php';

