<?php

include 'init.php';

load('memory');
global $atnames, $statuses;
memory::clear();

include 'options.php';

$fields = array(
    'from_acct' => array(
        'name' => 'from_acct',
        'type' => 'select',
        'options' => $from_options
    ),
    'txn_dt' => array(
        'name' => 'txn_dt',
        'type' => 'date'
    ),
    'split' => array(
        'name' => 'split',
        'type' => 'checkbox',
        'value' => 1
    ),
    'checkno' => array(
        'name' => 'checkno',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12
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
    'line_no' => array(
        'name' => 'line_no',
        'type' => 'hidden'
    ),
    'status' => array(
        'name' => 'status',
        'type' => 'select',
        'options' => $status_options
    ),
    'recon_dt' => array(
        'name' => 'recon_dt',
        'type' => 'date'
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
    'max_splits' => array(
        'name' => 'max_splits',
        'type' => 'text',
        'size' => 2,
        'maxlength' => 2
    ),
    'save' => array(
        'name' => 'save',
        'type' => 'submit',
        'value' => 'Save'
    )
);
$form->set($fields);
$page_title = 'Enter Transaction';
$focus_field = 'from_acct';
$return = 'settletxn.php';
include VIEWDIR . 'addtxn.view.php';

