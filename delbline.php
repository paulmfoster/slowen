<?php

// First, select an account to delete.

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
        'value' => 'Delete'
    ]
];

$form->set($fields);
$focus_field = 'id';
$page_title = 'Delete Budget Account: Account Selection';
$return = 'delbline2.php';

include VIEWDIR . 'delbline.view.php';

