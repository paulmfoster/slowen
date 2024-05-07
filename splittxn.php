<?php

include 'init.php';

$max_splits = $_GET['max_splits'] ?? NULL;
if (is_null($max_splits)) {
    emsg('F', 'For split transaction no number of splits specified');
    redirect('addtxn.php');
}

include 'options.php';

$fields = array(
    'max_splits' => array(
        'name' => 'max_splits',
        'type' => 'hidden',
        'value' => $max_splits
    ),
    'split_payee_id' => array(
        'name' => 'split_payee_id[]',
        'type' => 'select',
        'options' => $payee_options
    ),
    'split_to_acct' => array(
        'name' => 'split_to_acct[]',
        'type' => 'select',
        'options' => $to_options
    ),
    'split_memo' => array(
        'name' => 'split_memo[]',
        'type' => 'text',
        'size' => 35,
        'maxlength' => 35
    ),
    'split_cr_amount' => array(
        'name' => 'split_cr_amount[]',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12
    ),
    'split_dr_amount' => array(
        'name' => 'split_dr_amount[]',
        'type' => 'text',
        'size' => 12,
        'maxlength' => 12
    ),
    'save' => array(
        'name' => 'save',
        'type' => 'submit',
        'value' => 'Save'
    )
);

$form->set($fields);
$page_title = 'Splits Entry';
$return = 'vrfytxn.php';

include VIEWDIR . 'txnsplt.view.php';


