<?php

include 'init.php';

$entities = array();
foreach ($cfg['entity'] as $index => $value) {
	$entities[] = array('entity_num' => $index, 'entity_name' => $value);
}

view('Select Entity', ['entities' => $entities], 'index.php', 'entity');

