<?php

include 'init.php';

for ($i = 2016; $i < 2050; $i++) {
    $year_options[] = array('lbl' => $i, 'val' => $i);
}

$fields = array(
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
$page_title = 'Yearly Audit';
$focus_field = 'year';
$return = 'showaudit.php';

include VIEWDIR . 'audity.view.php';

