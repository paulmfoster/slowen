<?php

include 'init.php';

$condition = 'new';

if (isset($_POST['s1'])) {
	
	// user entered preliminary data

	$acct = $sm->get_account($_POST['from_acct']);
	$condition = 'prelim_entered';
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
		$condition = 'new';
	}

}
elseif (isset($_POST['s3'])) {

	// user has marked transactions to clear
	
	$cleared_list = implode(', ', $_POST['status']);
	$data = $sm->check_reconciliation($_POST['from_acct'], $_POST['stmt_start_bal'], 
		$_POST['stmt_end_bal'], $cleared_list);

	if ($data === TRUE) {
		// everything balances
		$sm->finish_reconciliation($_POST['from_acct'], $_POST['stmt_end_bal'], $_POST['stmt_close_date'], $cleared_list);
		emsg('S', "Reconciliation passes checks. Congratulations.");
		$condition = 'new';
	}
	else {
		// reconciliation failed
		emsg('F', "Statement and computer final balances don't match.");
		$condition = 'failed';
	}

}

if ($condition == 'new') {

	// the first entry screen

	$accts = $sm->get_bank_accts();
	
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
		's1' => array(
			'name' => 's1',
			'type' => 'submit',
			'value' => 'Continue'
		)
	);

	$form = new form($fields);

	$view_file = 'views/prerecon.view.php';
}
elseif ($condition == 'prelim_entered') {

	// user has entered preliminary data, and it's okay
	// so show transactions
	
	$fields = array(
		'from_acct' => array(
			'name' => 'from_acct',
			'type' => 'hidden'
		),
		'stmt_start_bal' => array(
			'name' => 'stmt_start_bal',
			'type' => 'hidden'
		),
		'stmt_end_bal' => array(
			'name' => 'stmt_end_bal',
			'type' => 'hidden'
		),
		'stmt_close_date' => array(
			'name' => 'stmt_close_date',
			'type' => 'hidden'
		),
		'from_acct_name' => array(
			'name' => 'from_acct_name',
			'type' => 'hidden'
		),
		's3' => array(
			'name' => 's3',
			'type' => 'submit',
			'value' => 'Continue'
		)
	);

	$form = new form($fields);

	$acct = $sm->get_account($_POST['from_acct']);
	$from_acct = $acct['acct_id'];
	$from_acct_name = $acct['name'];
	$x_open_bal = $acct['x_open_bal'];
	$stmt_start_bal = int2dec(dec2int($_POST['stmt_start_bal']));
	$stmt_end_bal = int2dec(dec2int($_POST['stmt_end_bal']));
	$stmt_close_date = $_POST['stmt_close_date'];
	$txns = $sm->get_uncleared_transactions($_POST['from_acct']);

	$view_file = 'views/reconlist.view.php';

}
elseif ($condition == 'failed') {
	$view_file = 'views/reconfailed.view.php';
}

$page_title = 'Account Reconciliation';
include 'view.php';

