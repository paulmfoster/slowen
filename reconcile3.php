<?php

// user has marked transactions to clear

include 'init.php';
$rcn = model('recon');

$cleared_list = implode(', ', $_POST['status']);
$data = $rcn->check_reconciliation($_POST['from_acct'], $_POST['stmt_start_bal'], 
	$_POST['stmt_end_bal'], $cleared_list);

if ($data === TRUE) {
	// everything balances
	$rcn->finish_reconciliation($_POST['from_acct'], $_POST['stmt_end_bal'], $_POST['stmt_close_date'], $cleared_list);
	emsg('S', "Reconciliation passes checks. Congratulations.");
	redirect('reconcile.php');
}
else {
	// reconciliation failed
	emsg('F', "Statement and computer final balances don't match.");
	view('Reconciliation Failed', ['data' => $data], '', 'reconfailed');
}


