<?php

include 'init.php';

$report = model('report', $db);

$dt = new xdate();
$dt->endwk();
$to_date = $dt->to_iso();

$dt->add_days(-6);
$from_date = $dt->to_iso();

$fields = array(
    'from_date' => array(
        'name' => 'from_date',
        'type' => 'date',
        'value' => $from_date
    ),
    'to_date' => array(
        'name' => 'to_date',
        'type' => 'date',
        'value' => $to_date
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Calculate'
    )
);

$form->set($fields);
$page_title = 'Weekly Expenses';
$focus_field = 'from_date';
$return = 'showexp2.php';

include VIEWDIR . 'expenses.view.php';


