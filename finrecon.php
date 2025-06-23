<?php

include 'init.php';

$reconcile = model('reconcile', $db);

if (count($_POST) == 0)
    redirect('prerecon.php');

// FIXME: where do we get POST['status'] from? I can't find it
if (!empty($_POST['status'])) {
    $cleared_list = implode(', ', $_POST['status']);
    $data = $reconcile->check_reconciliation($_POST['from_acct'], $_POST['stmt_start_bal'], 
        $_POST['stmt_end_bal'], $cleared_list);
}
else {
    // no transactions marked as cleared
    $data = FALSE;
}

if ($data === TRUE) {
    // everything balances
    $reconcile->finish_reconciliation($_POST['from_acct'], $_POST['stmt_end_bal'], $_POST['stmt_close_date'], $cleared_list);
    emsg('S', "Reconciliation passes checks. Congratulations.");
    redirect('prerecon.php');
}
elseif ($data === FALSE) {
    // no transactions marked as cleared
    // however, this can happen when someone revisits the reconciliation
    // and "unclears" transactions already marked as cleared.
    emsg('F', 'No transactions marked for clearing. Aborted.');
    $reconcile->unclear_all($_POST['from_acct']);
    redirect('prerecon.php');
}
else {
    // reconciliation failed
    $reconcile->save_work($cleared_list, $_POST['from_acct'], $_POST['stmt_start_bal'], $_POST['stmt_end_bal'], $_POST['stmt_close_date']);
    emsg('F', "Statement and computer final balances don't match.");
    $page_title = 'Reconciliation Failed';
    include VIEWDIR . 'reconfailed.view.php';
}

