<?php

include 'init.php';

$sched = model('scheduled', $db);

$txns = $sched->fetch_scheduled();

$sched_options = [];

foreach ($txns as $t) {
    $sched_options[] = ['lbl' => $t['from_acct_name'] . ':' . $t['payee_name'] . ':' . $t['memo'] . ':' . $t['to_acct_name'], 'val' => $t['id']];
}

$fields = [
    'id' => [
        'name' => 'id',
        'type' => 'select',
        'options' => $sched_options
    ],
    'edit' => [
        'name' => 'edit',
        'type' => 'submit',
        'value' => 'Edit'
    ]
];

$form->set($fields);

$page_title = 'Edit Scheduled Transaction';
$return = 'editsched2.php';

include VIEWDIR . 'schedit.view.php';
