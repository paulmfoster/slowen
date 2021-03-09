<?php

include 'init.php';

$acct_id = $_POST['acct_id'] ?? NULL;

if (is_null($acct_id)) {
	redirect('register.php');
}

$txns = model('transaction');

$acct = $txns->get_account($acct_id);
$r = $txns->get_transactions($acct_id, 'F');

view('Account Register', ['acct' => $acct, 'r' => $r], '', 'register');

