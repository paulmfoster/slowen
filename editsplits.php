<?php

include 'init.php';

$txnid = $_GET['txnid'] ?? NULL;
if (is_null($txnid))
    redirect('index.php');

$trans = model('transaction', $db);
$txns = $trans->get_transaction($txnid);

global $statuses, $atnames;

$payees = $trans->get_payees();
$payee_options[] = ['lbl' => 'NONE', 'val' => 0];
foreach($payees as $payee) {
    $payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
}

$to_accts = $trans->get_to_accounts();
$to_options = array();
foreach ($to_accts as $to_acct) {
    $to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
        'val' => $to_acct['id']);
}

$splits = $trans->get_splits($txns[0]['id']);
if ($splits !== FALSE) {
    $max_splits = count($splits);
}
else {
    $max_splits = 0;
}

$split_to_options = array();
foreach ($to_accts as $to_acct) {
    $split_to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
        'val' => $to_acct['id']);
}

$fields = array(
    'txnid' => array(
        'name' => 'txnid',
        'type' => 'hidden',
        'value' => $txns[0]['txnid']
    ),
    'txntype' => array(
        'name' => 'txntype',
        'type' => 'hidden',
        'value' => 'split'
    ), 
    'txn_dt' => array(
        'name' => 'txn_dt',
        'type' => 'date',
        'value' => $txns[0]['txn_dt']
    ),
    'checkno' => array(
        'name' => 'checkno',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12,
        'value' => $txns[0]['checkno']
    ),
    'payee_id' => array(
        'name' => 'payee_id',
        'type' => 'select',
        'options' => $payee_options,
        'value' => $txns[0]['payee_id']
    ),
    'memo' => array(
        'name' => 'memo',
        'type' => 'text',
        'size' => 35,
        'maxlength' => 35,
        'value' => $txns[0]['memo']
    ),
    'to_acct' => array(
        'name' => 'to_acct',
        'type' => 'select',
        'options' => $to_options,
        'value' => $txns[0]['to_acct']
    ),
    'save' => array(
        'name' => 'save',
        'type' => 'submit',
        'value' => 'Save Edits'
    ),
    // only used for splits
    'split_id' => array(
        'name' => 'split_id[]',
        'type' => 'hidden'
    ),
    'split_payee_id' => array(
        'name' => 'split_payee_id[]',
        'type' => 'select',
        'options' => $payee_options
    ),
    'split_to_acct' => array(
        'name' => 'split_to_acct[]',
        'type' => 'select',
        'options' => $split_to_options
    ),
    'split_memo' => array(
        'name' => 'split_memo[]',
        'type' => 'text',
        'size' => 35,
        'maxlength' => 35
    ),
    'split_amount' => array(
        'name' => 'split_amount[]',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12
    )
);

$form->set($fields);

$page_title = 'Edit Split Transaction';
$return = 'updatetxn.php';
include VIEWDIR . 'splitsedt.view.php';

