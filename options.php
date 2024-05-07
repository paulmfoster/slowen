<?php

// NOTE: Options code from model atxn::options(). This is used by various
// add transaction controllers.

$trans = model('addtxn', $db);

$payees = $trans->get_payees();
$accounts = $trans->get_all_accounts();
$bank_accts = $accounts['bank'];
$ccard_accts = $accounts['ccard'];
$from_accts = $accounts['from'];
$to_accts = $accounts['to'];

if ($payees == FALSE || $from_accts == FALSE || $to_accts == FALSE) {
    emsg('F', 'Payees and/or accounts missing.');
    redirect('index.php');
}

$bank_options = array();
foreach($bank_accts as $bank_acct) {
    $bank_options[] = array('lbl' => 
        $bank_acct['name'] . ' ' . $atnames[$bank_acct['acct_type']], 
        'val' => $bank_acct['id']);
}

$from_options = array();
foreach($from_accts as $from_acct) {
    $from_options[] = array('lbl' => 
        $from_acct['name'] . ' ' . $atnames[$from_acct['acct_type']], 
        'val' => $from_acct['id']);
}

$ccard_options = array();
foreach($ccard_accts as $ccard_acct) {
    $ccard_options[] = array('lbl' => 
        $ccard_acct['name'] . ' ' . $atnames[$ccard_acct['acct_type']], 
        'val' => $ccard_acct['id']);
}

$payee_options = array();
$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
foreach($payees as $payee) {
    $payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
}

$to_options = array();
$to_options[] = ['lbl' => 'NONE', 'val' => 0];
foreach($to_accts as $to_acct) {
    $to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
        'val' => $to_acct['id']);
}

$status_options = array();
foreach($statuses as $key => $value) {
    $status_options[] = array('lbl' => $value, 'val' => $key);
}

