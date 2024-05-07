<?php

include 'init.php';

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

$dt = new xdate();

$fields = array(
    'from_acct' => array(
        'name' => 'from_acct',
        'type' => 'select',
        'options' => $from_options
    ),
    'freq' => array(
        'name' => 'freq',
        'type' => 'text',
        'size' => 3,
        'maxlength' => 3
    ),
    'period' => array(
        'name' => 'period',
        'type' => 'select',
        'options' => $period_options
    ),
    'last' => array(
        'name' => 'last',
        'type' => 'date',
        'value' => $dt->to_iso()
    ), 
    'xfer' => array(
        'name' => 'xfer',
        'type' => 'checkbox',
        'value' => 1
    ),
    'payee_id' => array(
        'name' => 'payee_id',
        'type' => 'select',
        'options' => $payee_options
    ),
    'memo' => array(
        'name' => 'memo',
        'type' => 'text',
        'size' => 35,
        'maxlength' => 35
    ),
    'to_acct' => array(
        'name' => 'to_acct',
        'type' => 'select',
        'options' => $to_options
    ),
    'dr_amount' => array(
        'name' => 'dr_amount',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12
    ),
    'cr_amount' => array(
        'name' => 'cr_amount',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Save'
    )
);
$form->set($fields);
$focus_field = 'from_acct';
$page_title = 'Add Scheduled Transaction';
// $return = 'index.php?c=sched&m=save';
$return = 'savesched.php';

include VIEWDIR . 'schadd.view.php';


