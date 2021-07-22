<?php

// user chose to continue reconciliation

include 'init.php';
fork('s1', 'P', 'reconcile.php');

$continue = $_POST['continue'] ?? 0;

$rcn = model('recon');

if ($continue == 0) {
	$rcn->clear_saved_work($_POST['from_acct']);
	redirect('reconcile.php');
}

$acct = $rcn->get_account($_POST['from_acct']);

$saved = $rcn->get_saved_work($_POST['from_acct']);
// clear saved work; we're now in the middle of reconciliation again
$rcn->clear_saved_work($_POST['from_acct']);

$saved['stmt_start_bal'] = int2dec($saved['stmt_start_bal']);
$saved['stmt_end_bal'] = int2dec($saved['stmt_end_bal']);

$from_acct = $acct['acct_id'];
$from_acct_name = $acct['name'];

$stmt_start_bal = $saved['stmt_start_bal'];
$stmt_end_bal = $saved['stmt_end_bal'];
$stmt_close_date = $saved['stmt_close_date'];

$txns = $rcn->get_uncleared_transactions($_POST['from_acct']);

// hidden fields...
$fields = array(
	'from_acct' => array(
		'name' => 'from_acct',
		'type' => 'hidden',
		'value' => $from_acct
	),
	'stmt_start_bal' => array(
		'name' => 'stmt_start_bal',
		'type' => 'hidden',
		'value' => $stmt_start_bal
	),
	'stmt_end_bal' => array(
		'name' => 'stmt_end_bal',
		'type' => 'hidden',
		'value' => $stmt_end_bal
	),
	'stmt_close_date' => array(
		'name' => 'stmt_close_date',
		'type' => 'hidden',
		'value' => $stmt_close_date
	),
	'from_acct_name' => array(
		'name' => 'from_acct_name',
		'type' => 'hidden',
		'value' => $from_acct_name
	),
	's3' => array(
		'name' => 's3',
		'type' => 'submit',
		'value' => 'Continue'
	)
);

$form->set($fields);

view('Reconcile: Clear Transactions', ['txns' => $txns, 'from_acct_name' => $from_acct_name], 'reconcile3.php', 'reconlist');

