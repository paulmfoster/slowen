<?php

include 'init.php';

$report = model('report', $db);

$today = new xdate();

$fields = [
    'last_dt' => [
        'name' => 'last_dt',
        'type' => 'date',
        'value' => $today->to_iso()
    ],
    's1' => [
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Compute'
    ]
];

$form->set($fields);
$page_title = 'List Balances';
$focus_field = 'last_dt';
$return = 'showbals.php';

include VIEWDIR . 'balances.view.php';
