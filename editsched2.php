<?php

include 'init.php';


$id = $_POST['id'] ?? NULL;
if (is_null($id))
    redirect('editsched.php');

$scheduled = model('scheduled', $db);
$txn = $scheduled->fetch_single_scheduled($id);

global $atnames;
$trans = model('addtxn', $db);

$payees = $trans->get_payees();
$accounts = $trans->get_all_accounts();
$from_accts = $accounts['from'];
$to_accts = $accounts['to'];

if ($payees == FALSE || $from_accts == FALSE || $to_accts == FALSE) {
    emsg('F', 'Payees and/or accounts missing.');
    redirect('index.php');
}

$from_options = array();
foreach($from_accts as $from_acct) {
    $from_options[] = array('lbl' => 
        $from_acct['name'] . ' ' . $atnames[$from_acct['acct_type']], 
        'val' => $from_acct['id']);
}

$payee_options = array();
$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
foreach($payees as $payee) {
    $payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
}

$to_options = array();
foreach($to_accts as $to_acct) {
    $to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
        'val' => $to_acct['id']);
}

$period_options = [];
$period_options[] = ['lbl' => 'Day', 'val' => 'D'];
$period_options[] = ['lbl' => 'Week', 'val' => 'W'];
$period_options[] = ['lbl' => 'Month', 'val' => 'M'];
$period_options[] = ['lbl' => 'Quarter', 'val' => 'Q'];
$period_options[] = ['lbl' => 'Year', 'val' => 'Y'];

$dom_options = [];
for ($i = 1; $i < 31; $i++) {
    $dom_options[] = ['lbl' => $i, 'val' => $i];
}

$fields = array(
    'id' => [
        'name' => 'id',
        'type' => 'hidden',
        'value' => $id
    ],
    'from_acct' => array(
        'name' => 'from_acct',
        'type' => 'select',
        'options' => $from_options,
        'value' => $txn['from_acct']
    ),
    'freq' => array(
        'name' => 'freq',
        'type' => 'text',
        'size' => 3,
        'maxlength' => 3,
        'value' => $txn['freq']
    ),
    'period' => array(
        'name' => 'period',
        'type' => 'select',
        'options' => $period_options,
        'value' => $txn['period']
    ),
    'last' => array(
        'name' => 'last',
        'type' => 'date',
        'value' => $txn['last']
    ), 
    'payee_id' => array(
        'name' => 'payee_id',
        'type' => 'select',
        'options' => $payee_options,
        'value' => $txn['payee_id']
    ),
    'memo' => array(
        'name' => 'memo',
        'type' => 'text',
        'size' => 35,
        'maxlength' => 35,
        'value' => $txn['memo']
    ),
    'to_acct' => array(
        'name' => 'to_acct',
        'type' => 'select',
        'options' => $to_options,
        'value' => $txn['to_acct']
    ),
    'dr_amount' => array(
        'name' => 'dr_amount',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12,
        'value' => ($txn['amount'] < 0) ? int2dec(abs($txn['amount'])) : ''
    ),
    'cr_amount' => array(
        'name' => 'cr_amount',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12,
        'value' => ($txn['amount'] > 0) ? int2dec($txn['amount']) : ''
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Save'
    )
);
$form->set($fields);
$focus_field = 'from_acct';
$page_title = 'Edit Scheduled Transaction';
$return = 'editsched3.php';

include VIEWDIR . 'schedit2.view.php';


