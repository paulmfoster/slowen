<?php

include 'init.php';
$accts = model('account');

if (isset($_POST['s1'])) {
	$accts->delete_account($_POST['acct_id']); 
}

redirect('acctdel.php');

