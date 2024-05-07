<?php

// NOTE: Show results from search of accounts. "Results" are in terms of
// transactions.

include 'init.php';

$txns = model('transaction', $db);

$acct = $_POST['category'] ?? NULL;

if (!is_null($acct)) {
    $transactions = $txns->get_transactions($acct, 'C');
}
else {
    redirect('listacct.php');
}

$page_title = 'Search Results';
include VIEWDIR . 'results.view.php';

