<?php

include 'init.php';

$account = model('account', $db);

$id = $_GET['id'] ?? NULL;
if (is_null($id))
    redirect('listacct.php');

global $acct_types;

$acct = $account->get_account($id);
$acct['x_acct_type'] = $acct_types[$acct['acct_type']];
$fields = [
    'id' => [
        'name' => 'id',
        'type' => 'hidden',
        'value' => $acct['id']
    ],
    'edit' => [
        'name' => 'edit',
        'type' => 'submit',
        'value' => 'Edit',
    ],
    'delete' => [
        'name' => 'delete',
        'type' => 'submit',
        'value' => 'Delete'
    ]
];
$form->set($fields);
$return = 'showacct2.php';

$page_title = 'Show Account';
include VIEWDIR . 'acctshow.view.php';

