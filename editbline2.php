<?php

include 'init.php';

$id = $_POST['id'] ?? NULL;
if (is_null($id)) {
    redirect('editbline.php');
}

$bg = model('budget', $db);
$acct = $bg->get_account_extended($id);

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
    'id' => array(
        'name' => 'id',
        'type' => 'hidden',
        'value' => $acct['id']
    ),
    'acctname' => array(
        'name' => 'acctname',
        'type' => 'text',
        'size' => 20,
        'maxlength' => 20,
        'required' => 1,
        'label' => 'Account Name',
        'value' => $acct['acctname']
    ),
    'typdue' => array(
        'name' => 'typdue',
        'type' => 'text',
        'size' => 10,
        'maxlength' => 10,
        'label' => 'Typical Due',
        'value' => int2dec($acct['typdue'])
    ),
    'period' => array(
        'name' => 'period',
        'type' => 'select',
        'options' => $period_options,
        'required' => 1,
        'label' => 'Period',
        'value' => $acct['period']
    ),
    'from_acct' => array(
        'name' => 'from_acct',
        'type' => 'select',
        'options' => $from_acct_options,
        'label' => 'From Account',
        'value' => $acct['from_acct']
    ),
    'payee_id' => array(
        'name' => 'payee_id',
        'type' => 'select',
        'options' => $payee_options,
        'label' => 'Payee',
        'value' => $acct['payee_id']
    ),
    'to_acct' => array(
        'name' => 'to_acct',
        'type' => 'select',
        'options' => $to_acct_options,
        'label' => 'To Account/Category',
        'value' => $acct['to_acct']
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Save Changes'
    )
);

$form->set($fields);
$focus_field = 'acctname';
$page_title = 'Edit Budget Account';
$return = 'editbline3.php';

include VIEWDIR . 'editbline2.view.php';

