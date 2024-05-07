<?php

include 'init.php';

$id = $_GET['id'] ?? NULL;
if (is_null($id))
    redirect('listpayee.php');

$payee = model('payee', $db);

$payee = $payee->get_payee($id);

$fields = array(
    'id' => array(
        'name' => 'id',
        'type' => 'hidden',
        'value' => $id
    ),
    'name' => array(
        'name' => 'name',
        'type' => 'text',
        'size' => 35,
        'maxlength' => 35,
        'value' => $payee['name']
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Update'
    )
);

$form->set($fields);
$page_title = 'Edit Payee';
$focus_field = 'name';
$return = 'editpayee2.php';
include VIEWDIR . 'payedt.view.php';

