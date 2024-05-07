<?php

// NOTE: Select which account to show in the register.

include 'init.php';

$acct = model('account', $db);
$accounts = $acct->get_from_accounts();
$acct_options = [];
foreach ($accounts as $acct) {
    $acct_options[] = ['lbl' => $acct['name'], 'val' => $acct['id']];
}

$fields = [
    'id' => [
        'name' => 'id',
        'type' => 'select',
        'options' => $acct_options
    ],
    's1' => [
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Report'
    ]
];

$form->set($fields);

$return = 'register.php';
$page_title = 'Register: Select Account';

include VIEWDIR . 'acctsel.view.php';

