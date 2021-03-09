<?php

include 'init.php';
memory::merge($_POST);

if (!empty($_POST['cr_amount'])) {
	memory::set('amount', $_POST['cr_amount']);
}
elseif (!empty($_POST['dr_amount'])) {
	memory::set('amount', $_POST['dr_amount']);
}
else {
	memory::set('amount', 0);
}

$trans = model('addtxn');

$split = $_POST['split'] ?? 0;
if ($split == 0) {
	redirect('othvrfy.php');
}

$max_splits = $_POST['max_splits'];

$payees = $trans->get_payees();
$to_accts = $trans->get_split_to_accounts();
$payee_options = array();
foreach ($payees as $payee) {
	$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$to_options = array();
foreach ($to_accts as $to_acct) {
	$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']],
		'val' => $to_acct['acct_id']);
}

$fields = array(
	'max_splits' => array(
		'name' => 'max_splits',
		'type' => 'hidden',
		'value' => $_POST['max_splits']
	),
	'split_payee_id' => array(
		'name' => 'split_payee_id[]',
		'type' => 'select',
		'options' => $payee_options
	),
	'split_to_acct' => array(
		'name' => 'split_to_acct[]',
		'type' => 'select',
		'options' => $to_options
	),
	'split_memo' => array(
		'name' => 'split_memo[]',
		'type' => 'text',
		'size' => 35,
		'maxlength' => 35
	),
	'split_cr_amount' => array(
		'name' => 'split_cr_amount[]',
		'type' => 'text',
		'size' => 12,
		'maxlength' => 12
	),
	'split_dr_amount' => array(
		'name' => 'split_dr_amount[]',
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

view('Splits Entry', ['max_splits' => $_POST['max_splits']], 'othvrfy.php', 'txnsplt');

