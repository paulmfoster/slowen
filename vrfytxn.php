<?php

// NOTE: This code checks to see if the user has asked to have transactions
// confirms in the config. If not, it simply adds the transaction and
// quits. Otherwise, it presents the transaction to the user and asks for
// confirmation.

include 'init.php';

$trans = model('addtxn', $db);

global $statuses;

// for splits
load('memory');
memory::merge($_POST);

if ($cfg['confirm_transactions'] == 0) {
    $txnid = $trans->add_transaction(memory::get_all());
    memory::clear();
    redirect('addtxn.php');
}

$fields = array(
    'txnid' => array(
        'name' => 'txnid',
        'type' => 'hidden',
        'value' => memory::get('txnid')
    ),
    'confirm' => array(
        'name' => 'confirm',
        'type' => 'submit',
        'value' => 'Confirm'
    )
);

$form->set($fields);

$data = memory::get_all();
$data['x_status'] = $statuses[$data['status']];

$names = $trans->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
$data['from_acct_name'] = $names['from_acct_name'];
$data['to_acct_name'] = $names['to_acct_name'];
$data['payee_name'] = $names['payee_name'];
$data['status_descrip'] = $statuses[$data['status']];

if ($data['max_splits'] > 0) {
    for ($e = 0; $e < $data['max_splits']; $e++) {
        $names = $this->trans->get_split_names($data['split_payee_id'][$e], $data['split_to_acct'][$e]);
        $data['split_to_name'][$e] = $names['split_to_name'];
        $data['split_payee_name'][$e] = $names['split_payee_name'];
    }
}

$page_title = 'Confirm Transaction';
$return = 'savetxn.php';

include VIEWDIR . 'txnvrfy.view.php';

