<?php

include 'init.php';

$id = $_GET['id'] ?? NULL;
if (is_null($id))
    redirect('listacct.php');

$account = model('account', $db);
$acct = $account->get_account($id);

global $acct_types;

$acct = $account->get_account($id);
$acct['x_acct_type'] = $acct_types[$acct['acct_type']];

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
$page_title = 'Delete Account';
$return = 'delacct2.php';
include VIEWDIR . 'acctdel.view.php';

