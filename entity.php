<?php

include 'init.php';

// entities should be specified in the config file, parsed in
// init.php. 

$entities = array();
foreach ($cfg['entity'] as $index => $value) {
	$entities[] = array('entity_num' => $index, 'entity_name' => $value);
}

// process user input
	
if (!empty($_POST)) {
	$num = count($entities);
	for ($i = 0; $i < $num; $i++) {
		if ($_POST['entity_num'] == $entities[$i]['entity_num']) {
			$_SESSION['entity_num'] = $_POST['entity_num'];
			$_SESSION['entity_name'] = $entities[$i]['entity_name'];
			emsg('S', "Entity has been set to {$_SESSION['entity_name']}.");
			break;
		}
	}
}

// set up for page display

$page_title = 'Select Entity';
$view_file = 'views/entity.view.php';
include 'view.php';


