<?php

include 'init.php';

$acct_id = $_POST['acct_id'] ?? NULL;

if (is_null($acct_id)) {
	relocate('register.php');
}

$txns = load_model('transaction');

$acct = $txns->get_account($acct_id);
$r = $txns->get_transactions($acct_id, 'F');

$page_title = 'Account Register';
$view_file = view_file('register');
include 'view.php';
