<?php

include 'init.php';

$id = $_GET['id'] ?? NULL;
if (is_null($id))
    redirect('listpayee.php');

$p = model('payee', $db);
$payee = $p->get_payee($id);

$fields = array(
    'id' => array(
        'name' => 'id',
        'type' => 'hidden',
        'value' => $id
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Delete'
    )
);

$form->set($fields);
$page_title = 'Delete Payee';
$focus_field = 'name';
$return = 'delpayee2.php';

include VIEWDIR . 'paydel.view.php';
