<?php

include 'init.php';

$month_options = array(
    array('lbl' => 'January', 'val' => 1),
    array('lbl' => 'February', 'val' => 2),
    array('lbl' => 'March', 'val' => 3),
    array('lbl' => 'April', 'val' => 4),
    array('lbl' => 'May', 'val' => 5),
    array('lbl' => 'June', 'val' => 6),
    array('lbl' => 'July', 'val' => 7),
    array('lbl' => 'August', 'val' => 8),
    array('lbl' => 'September', 'val' => 9),
    array('lbl' => 'October', 'val' => 10),
    array('lbl' => 'November', 'val' => 11),
    array('lbl' => 'December', 'val' => 12)
);

for ($i = 2016; $i < 2050; $i++) {
    $year_options[] = array('lbl' => $i, 'val' => $i);
}

$fields = array(
    'month' => array(
        'name' => 'month',
        'type' => 'select',
        'options' => $month_options
    ),
    'year' => array(
        'name' => 'year',
        'type' => 'select',
        'options' => $year_options
    ),
    's1' => array(
        'name' => 's1',
        'type' => 'submit',
        'value' => 'Report'
    )
);

$form->set($fields);
$page_title = 'Monthly Audit';
$return = 'showaudit.php';
$focus_field = 'month';
include VIEWDIR . 'auditm.view.php';

