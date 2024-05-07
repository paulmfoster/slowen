<?php

include 'init.php';

$reconcile = model('reconcile', $db);

$accts = $reconcile->get_recon_accts();
$payees = $reconcile->get_payees();

$payee_options = [];
if ($payees !== FALSE) {
    foreach ($payees as $payee) {
        $payee_options[] = ['lbl' => $payee['name'], 'val' => $payee['id']];
    }
}

$to_accts = $reconcile->get_to_accounts();
$to_options = [];
if ($to_accts !== FALSE) {
    foreach ($to_accts as $to_acct)
    {
        $to_options[] = ['lbl' => $to_acct['name'], 'val' => $to_acct['id']];
    }
}

$from_options = array();
if ($accts !== FALSE) {
    foreach ($accts as $acct) {
        $from_options[] = array('lbl' => $acct['acct_type'] . '/' . $acct['name'],
            'val' => $acct['id']);
    }
}

$fields = array(
    'from_acct' => array(
        'name' => 'from_acct',
        'type' => 'select',
        'required' => 1,
        'options' => $from_options
    ),
    'stmt_start_bal' => array(
        'name' => 'stmt_start_bal',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12
    ),
    'stmt_end_bal' => array(
        'name' => 'stmt_end_bal',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12
    ),
    'stmt_close_date' => array(
        'name' => 'stmt_close_date',
        'type' => 'date'
    ),
    'fee' => array(
        'name' => 'fee',
        'type' => 'text',
        'size' => 10,
        'maxlength' => 10
    ),
    'payee_id' => array(
        'name' => 'payee_id',
        'type' => 'select',
        'options' => $payee_options
    ),
    'to_acct' => array(
        'name' => 'to_acct',
        'type' => 'select',
        'options' => $to_options
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Continue'
    )
);

$form->set($fields);
$page_title = 'Reconcile: Enter Preliminary Data';
$return = 'clearrecon.php';
include VIEWDIR . 'prerecon.view.php';

