<?php

include 'init.php';
$txnid = $_GET['txnid'] ?? NULL;
if (is_null($txnid)) {
	redirect('index.php');
}

$trans = model('transaction');
$txns = $trans->get_transaction($txnid);

$max_txns = count($txns);

$payees = $trans->get_payees();
$payee_options[] = ['lbl' => 'NONE', 'val' => 0];
foreach($payees as $payee) {
	$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
}

$atnames = array(
	' ' => '(none)',
	'I' => '(inc)',
	'E' => '(exp)',
	'L' => '(liab)',
	'A' => '(asset)',
	'Q' => '(eqty)',
	'R' => '(ccard)',
	'C' => '(chkg)',
	'S' => '(svgs)'
);

$status_options = array();
foreach ($statuses as $key => $value) {
	$status_options[] = array('lbl' => $value, 'val' => $key);
}

$to_accts = $trans->get_to_accounts();
$to_options = array();
foreach ($to_accts as $to_acct) {
	$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
		'val' => $to_acct['acct_id']);
}

$splits = $trans->get_splits($txns[0]['txnid']);
if ($splits !== FALSE) {
	$max_splits = count($splits);
}
else {
	$max_splits = 0;
}

$split_to_options = array();
foreach ($to_accts as $to_acct) {
	$split_to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
		'val' => $to_acct['acct_id']);
}

if ($max_txns == 1) {

	// single transaction

	$fields = array(
		'acct_id' => array(
			'name' => 'acct_id',
			'type' => 'hidden'
		),
		'txntype' => array(
			'name' => 'txntype',
			'type' => 'hidden'
		),
		'txnid' => array(
			'name' => 'txnid',
			'type' => 'hidden'
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
		'status' => array(
			'name' => 'status',
			'type' => 'select',
			'options' => $status_options
		),
		'recon_dt' => array(
			'name' => 'recon_dt',
			'type' => 'text',
			'size' => 10,
			'maxlength' => 10
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
		'cr_amount' => array(
			'name' => 'cr_amount',
			'type' => 'text',
			'size' => 12,
			'maxlength' => 12
		),
		'amount' => array(
			'name' => 'amount',
			'type' => 'text',
			'size' => 12,
			'maxlength' => 12
		),
		's1' => array(
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Save Edits'
		),
		// only used for splits
		'split_id' => array(
			'name' => 'split_id[]',
			'type' => 'hidden'
		),
		'split_payee_id' => array(
			'name' => 'split_payee_id[]',
			'type' => 'select',
			'options' => $payee_options
		),
		'split_to_acct' => array(
			'name' => 'split_to_acct[]',
			'type' => 'select',
			'options' => $split_to_options
		),
		'split_memo' => array(
			'name' => 'split_memo[]',
			'type' => 'text',
			'size' => 35,
			'maxlength' => 35
		),
		'split_amount' => array(
			'name' => 'split_amount[]',
			'type' => 'text',
			'size' => 12,
			'maxlength' => 12
		)
	);

	$form->set($fields);

	view('Edit Transaction', ['txns' => $txns, 'statuses' => $statuses, 'max_txns' => $max_txns], 'txnupd.php', 'txnedt');

}
else {

	// inter-account transfer

	$fields = array(
		'txntype' => array(
			'name' => 'txntype',
			'type' => 'hidden',
			'value' => 'xfer'
		),
		'txnid' => array(
			'name' => 'txnid',
			'type' => 'hidden',
			'value' => $txns[0]['txnid']
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
		's1' => array(
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Save Edits'
		)
	);

	$form->set($fields);

	view('Edit Inter-Account Transfer', ['txns' => $txns, 'statuses' => $statuses], 'txnupd.php', 'xferedt');
}


