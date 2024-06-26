<?php

// First, select an account to edit.

include 'init.php';

$bg = model('budget', $db);
$accts = $bg->get_accounts();

foreach ($accts as $acct) {
    $accts_options[] = [
        'lbl' => $acct['acctname'], 
        'val' => $acct['id']
    ];
}

$fields = [
    'id' => [
        'name' => 'id',
        'type' => 'select',
        'options' => $accts_options,
        'label' => 'Account'
    ],
    's1' => [
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Edit'
    ]
];

$form->set($fields);
$focus_field = 'id';
$page_title = 'Edit Budget Account: Account Selection';
$return = 'editbline2.php';

include VIEWDIR . 'editbline.view.php';

