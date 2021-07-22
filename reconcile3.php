<?php

// user has marked transactions to clear

include 'init.php';
$rcn = model('recon');
if (!empty($_POST['status'])) {
	$cleared_list = implode(', ', $_POST['status']);
	$data = $rcn->check_reconciliation($_POST['from_acct'], $_POST['stmt_start_bal'], 
		$_POST['stmt_end_bal'], $cleared_list);
}
else {
	// no transactions marked as cleared
	$data = FALSE;
}

if ($data === TRUE) {
	// everything balances
	$rcn->finish_reconciliation($_POST['from_acct'], $_POST['stmt_end_bal'], $_POST['stmt_close_date'], $cleared_list);
	emsg('S', "Reconciliation passes checks. Congratulations.");
	redirect('reconcile.php');
}
elseif ($data === FALSE) {
	// no transactions marked as cleared
	// however, this can happen when someone revisits the reconciliation
	// and "unclears" transactions already marked as cleared.
	emsg('F', 'No transactions marked for clearing. Aborted.');
	$rcn->unclear_all($_POST['from_acct']);
	redirect('reconcile.php');
}
else {
	// reconciliation failed
	$rcn->save_work($cleared_list, $_POST['from_acct'], $_POST['stmt_start_bal'], $_POST['stmt_end_bal'], $_POST['stmt_close_date']);
	emsg('F', "Statement and computer final balances don't match.");
	view('Reconciliation Failed', ['data' => $data], '', 'reconfailed');
}


