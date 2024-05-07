<?php

// NOTE: Show transactions for a given payee.

include 'init.php';

$txns = model('transaction', $db);

$payee = $_POST['payee'] ?? NULL;

if (!is_null($payee)) {
    $transactions = $txns->get_transactions($payee, 'P');
}
else {
    redirect('index.php');
}

$page_title = 'Search Results';
include VIEWDIR . 'results.view.php';


