<?php

include 'init.php';
$rcn = model('recon');

// enter preliminary reconciliation data

$accts = $rcn->get_recon_accts();

$payees = $rcn->get_payees();
$payee_options = [];
foreach ($payees as $payee) {
	$payee_options[] = ['lbl' => $payee['name'], 'val' => $payee['payee_id']];
}

$to_accts = $rcn->get_to_accounts();
$to_options = [];
foreach ($to_accts as $to_acct)
{
	$to_options[] = ['lbl' => $to_acct['name'], 'val' => $to_acct['acct_id']];
}

$from_options = array();
foreach ($accts as $acct) {
	$from_options[] = array('lbl' => $acct['acct_type'] . '/' . $acct['name'],
		'val' => $acct['acct_id']);
}

$fields = array(
	'from_acct' => array(
		'name' => 'from_acct',
		'type' => 'select',
		'options' => $from_options
	),
	'stmt_start_bal' => array(
		'name' => 'stmt_start_bal',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'stmt_end_bal' => array(
		'name' => 'stmt_end_bal',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'stmt_close_date' => array(
		'name' => 'stmt_close_date',
		'type' => 'date'
	),
	'fee' => array(
		'name' => 'fee',
		'type' => 'text',
		'size' => 10,
		'maxlength' => 10
	),
	'payee_id' => array(
		'name' => 'payee_id',
		'type' => 'select',
		'options' => $payee_options
	),
	'to_acct' => array(
		'name' => 'to_acct',
		'type' => 'select',
		'options' => $to_options
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Continue'
	)
);

$form->set($fields);

view('Reconcile: Enter Preliminary Data', [], 'reconcile2.php', 'prerecon');

