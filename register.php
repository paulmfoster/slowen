<?php

include 'init.php';

$acct = model('account');
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

view('Register: Select Account', [], 'register2.php', 'accounts');

