<?php

// User has saved reconciliation.
// Do they want to continue it?

include 'init.php';
$rcn = model('recon');

$from_acct = fork('from_acct', 'G', 'reconcile.php');
$acct = $rcn->get_account($from_acct);

$fields = [
	'from_acct' => [
		'name' => 'from_acct',
		'type' => 'hidden',
		'value' => $from_acct
	],
	'continue' => [
		'name' => 'continue',
		'type' => 'checkbox',
		'value' => 1
	],
	's1' => [
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Continue'
	]
];
$form->set($fields);

view('Continue Reconciliation?', ['name' => $acct['name']], 'reconcont2.php', 'reconcont');

