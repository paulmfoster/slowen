<?php

include 'init.php';

$account = model('account', $db);

global $atnames;

$accts = $account->get_accounts();
$acct_options = [];
foreach ($accts as $acct) {
    $acct_options[] = [
        'lbl' => $acct['name'] . ' ' . $atnames[$acct['acct_type']], 
        'val' => $acct['id']
    ];
}

$fields = [
    'id' => [
        'name' => 'id',
        'type' => 'select',
        'options' => $acct_options
    ],
    'show' => [
        'name' => 'show',
        'type' => 'submit',
        'value' => 'Show'
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
$page_title = 'Accounts List';
$focus_field = 'id';
$return = 'listacct2.php';

include VIEWDIR . 'acctlst.view.php';


