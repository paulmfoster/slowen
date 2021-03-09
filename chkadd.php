<?php

include 'init.php';

memory::clear();
$trans = model('addtxn');

$accounts = $trans->get_accounts();
$payees = $trans->get_payees();
$from_accts = $trans->get_bank_accounts();
$to_accts = $trans->get_to_accounts();

$from_options = array();
foreach($from_accts as $from_acct) {
	$from_options[] = array('lbl' => 
		$from_acct['name'] . ' ' . $atnames[$from_acct['acct_type']], 
		'val' => $from_acct['acct_id']);
}

$payee_options = array();
$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
foreach($payees as $payee) {
	$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$to_options = array();
foreach($to_accts as $to_acct) {
	$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
		'val' => $to_acct['acct_id']);
}

$fields = array(
	'from_acct' => array(
		'name' => 'from_acct',
		'type' => 'select',
		'options' => $from_options
	),
	'txn_dt' => array(
		'name' => 'txn_dt',
		'type' => 'date'
	),
	'checkno' => array(
		'name' => 'checkno',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'payee_id' => array(
		'name' => 'payee_id',
		'type' => 'select',
		'options' => $payee_options
	),
	'memo' => array(
		'name' => 'memo',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	'to_acct' => array(
		'name' => 'to_acct',
		'type' => 'select',
		'options' => $to_options
	),
	'dr_amount' => array(
		'name' => 'dr_amount',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	's1' => array(
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Save'
	)
);

$form->set($fields);

view('Enter Check', [], 'chkvrfy.php', 'chkadd');

