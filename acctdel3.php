<?php

include 'init.php';
$accts = load_model('account');

if (isset($_POST['s1'])) {
	$accts->delete_account($_POST['acct_id']); 
}

relocate('acctdel.php');

