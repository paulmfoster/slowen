<?php

include 'init.php';
$acct_id = fork('acct_id', 'P', 'register.php');

$txns = model('transaction');

$acct = $txns->get_account($acct_id);
$r = $txns->get_transactions($acct_id, 'F');

view('Account Register', ['acct' => $acct, 'r' => $r], '', 'register');

