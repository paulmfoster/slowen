<?php

include 'init.php';

$payee = model('payee', $db);

$fields = array(
    'name' => array(
        'name' => 'name',
        'type' => 'text',
        'size' => 35,
        'maxlength' => 35
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Save'
    )
);

$form->set($fields);
$page_title = 'Add Payee';
$focus_field = 'name';
$return = 'addpayee2.php';
include VIEWDIR . 'payadd.view.php';

