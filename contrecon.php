<?php

// NOTE: This script is used when a reconciliation is continued later.

include 'init.php';

$from_acct = $_GET['from_acct'] ?? NULL;
if (is_null($from_acct))
    redirect('prerecon.php');

$reconcile = model('reconcile', $db);
$acct = $reconcile->get_account($from_acct);
$name = $acct['name']; 

$fields = [
    'from_acct' => [
        'name' => 'from_acct',
        'type' => 'hidden',
        'value' => $from_acct
    ],
    'continue' => [
        'name' => 'continue',
        'type' => 'checkbox',
        'value' => 1
    ],
    's1' => [
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Continue'
    ]
];
$form->set($fields);
$page_title = 'Continue Reconciliation';
$return = 'reclearrecon.php';
include VIEWDIR . 'reconcont.view.php';

