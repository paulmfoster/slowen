<?php

include 'init.php';

$payee = model('payee', $db);
$payees = $payee->get_payees();
if ($payees == FALSE) {
    emsg('F', 'No payees on file.');
    redirect('index.php');
}

$payee_options = array();
if ($payees !== FALSE) {
    foreach ($payees as $p) {
        $payee_options[] = array('lbl' => $p['name'], 'val' => $p['id']);
    }
}

$fields = array(
    'payee' => array(
        'name' => 'payee',
        'type' => 'select',
        'options' => $payee_options
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Search'
    )
);
$form->set($fields);
$page_title = 'Search Payees';

$focus_field = 'payee';
$return = 'srchpayee2.php';
include VIEWDIR . 'paysrch.view.php';

