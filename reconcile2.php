<?php

// user entered preliminary data

include 'init.php';
$rcn = model('recon');

$acct = $rcn->get_account($_POST['from_acct']);
$errors = 0;

if (!isset($_POST['stmt_start_bal']) || !isset($_POST['stmt_end_bal'])) {
	// user failed to provide either of the stmt balances we asked for
	$errors++;
	emsg('F', 'Beginning and/or ending balance omitted');
} 

if (empty($_POST['stmt_close_date'])) {
	// user omitted a statement close date
	$errors++;
	emsg('F', 'No closing date provided');
}

if ($acct['rec_bal'] != dec2int($_POST['stmt_start_bal'])) {
	// starting balances don't match
	$errors++;
	emsg('F', "Statement and computer starting balances don't match.");
}

if ($errors) {
	redirect('reconcile.php');
}

$acct = $rcn->get_account($_POST['from_acct']);
$from_acct = $acct['acct_id'];
$from_acct_name = $acct['name'];

$stmt_start_bal = $_POST['stmt_start_bal'];
$stmt_end_bal = $_POST['stmt_end_bal'];
$stmt_close_date = $_POST['stmt_close_date'];
$txns = $rcn->get_uncleared_transactions($_POST['from_acct']);

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

