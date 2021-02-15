<?php

include 'init.php';

$entities = array();
foreach ($cfg['entity'] as $index => $value) {
	$entities[] = array('entity_num' => $index, 'entity_name' => $value);
}

$page_title = 'Select Entity';
$view_file = view_file('entity');
include 'view.php';

