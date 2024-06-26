<?php

include 'init.php';

$bg = model('budget', $db);

$from_accts = $bg->get_from_accounts();
foreach ($from_accts as $fa) {
    $from_acct_options[] = [
        'lbl' => $fa['name'] . ' (' . $fa['acct_type'] . ')',
        'val' => $fa['id']
    ];
}

$to_accts = $bg->get_to_accounts();
foreach ($to_accts as $ta) {
    $to_acct_options[] = [
        'lbl' => $ta['name'] . ' (' . $ta['acct_type'] . ')',
        'val' => $ta['id']
    ];
}

$payees = $bg->get_payees();
foreach ($payees as $p) {
    $payee_options[] = [
        'lbl' => $p['name'],
        'val' => $p['id']
    ];
}

$period_options = array(
    array('lbl' => 'Year', 'val' => 'Y'),
    array('lbl' => 'Semi-Annual', 'val' => 'S'),
    array('lbl' => 'Quarter', 'val' => 'Q'),
    array('lbl' => 'Month', 'val' => 'M'),
    array('lbl' => 'Week', 'val' => 'W')
);

$fields = array(
    'acctname' => array(
        'name' => 'acctname',
        'type' => 'text',
        'size' => 20,
        'maxlength' => 20,
        'required' => 1,
        'label' => 'Account Name'
    ),
    'typdue' => array(
        'name' => 'typdue',
        'type' => 'text',
        'size' => 10,
        'maxlength' => 10,
        'label' => 'Typical Due'
    ),
    'period' => array(
        'name' => 'period',
        'type' => 'select',
        'options' => $period_options,
        'required' => 1,
        'label' => 'Period'
    ),
    'from_acct' => array(
        'name' => 'from_acct',
        'type' => 'select',
        'options' => $from_acct_options,
        'label' => 'From Account'
    ),
    'payee_id' => array(
        'name' => 'payee_id',
        'type' => 'select',
        'options' => $payee_options,
        'label' => 'Payee'
    ),
    'to_acct' => array(
        'name' => 'to_acct',
        'type' => 'select',
        'options' => $to_acct_options,
        'label' => 'To Account/Category'
    ),
    'priorsa' => array(
        'name' => 'priorsa',
        'type' => 'text',
        'size' => 10,
        'maxlength' => 10,
        'label' => 'Currently Owed'
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Add'
    )
);

$form->set($fields);
$focus_field = 'acctname';
$page_title = 'Add Budget Account';
$return = 'addbline2.php';

include VIEWDIR . 'addbline.view.php';

