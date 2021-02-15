<?php

include 'init.php';
$accts = load_model('account');

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

$page_title = 'Show Account';
$view_file = view_file('acctshow');
include 'view.php';

