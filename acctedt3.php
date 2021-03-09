<?php

// user had done edits; now show the results

include 'init.php';
$accts = model('account');

if (isset($_POST['s1'])) {
	if ($accts->update_account($_POST)) {
		emsg('S', "Account edits SAVED");
	}
	else {
		emsg('F', "Account update FAILED");
	}
}	

$acct = $accts->get_account($_POST['acct_id']);
$acct['x_acct_type'] = $acct_types[$acct['acct_type']];

view('Show Account', ['acct' => $acct], '', 'acctshow');

