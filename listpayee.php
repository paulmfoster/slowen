<?php

include 'init.php';

$payee = model('payee', $db);

$payees = $payee->get_payees();
$payee_options = [];
foreach ($payees as $payee) {
    $payee_options[] = ['lbl' => $payee['name'], 'val' => $payee['id']];
}

$fields = [
    'id' => [
        'name' => 'id',
        'type' => 'select',
        'options' => $payee_options
    ],
    'edit' => [
        'name' => 'edit',
        'type' => 'submit',
        'value' => 'Edit'
    ],
    'delete' => [
        'name' => 'delete',
        'type' => 'submit',
        'value' => 'Delete'
    ]
];
$form->set($fields);

$page_title = 'List Payees';
$focus_field = 'id';
$return = 'listpayee2.php';
include VIEWDIR . 'paylst.view.php';

