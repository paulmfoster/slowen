<?php

include 'init.php';

global $acct_types;

$account = model('account', $db);

$parents = $account->get_parents();
$parent_options = array();
foreach ($parents as $parent) {
    $parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['id']);
}

$acct_type_options = array();
foreach ($acct_types as $key => $value) {
    $acct_type_options[] = array('lbl' => $value, 'val' => $key);
}

$today = new xdate();

$fields = array(
    'parent' => array(
        'name' => 'parent',
        'type' => 'select',
        'required' => 1,
        'options' => $parent_options
    ),
    'open_dt' => array(
        'name' => 'open_dt',
        'required' => 1,
        'type' => 'date',
        'value' => $today->to_iso(),
    ),
    'recon_dt' => array(
        'name' => 'recon_dt',
        'type' => 'date'
    ),
    'acct_type' => array(
        'name' => 'acct_type',
        'type' => 'select',
        'required' => 1,
        'options' => $acct_type_options
    ),
    'name' => array(
        'name' => 'name',
        'type' => 'text',
        'size' => 35,
        'required' => 1,
        'maxlength' => 35
    ),
    'descrip' => array(
        'name' => 'descrip',
        'type' => 'text',
        'size' => 35,
        'maxlength' => 255 
    ),
    'open_bal' => array(
        'name' => 'open_bal',
        'type' => 'text',
        'size' => 12,
        'value' => 0,
        'maxlength' => 12 
    ),
    'rec_bal' => array(
        'name' => 'rec_bal',
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
$page_title = 'Add Account';
$return = 'addacct2.php';

include VIEWDIR . 'acctadd.view.php';


