<?php
include 'init.php';


$entity_options = [];
foreach ($cfg['entity_name'] as $entity_num => $entity_name) {
	$entity_options[] = ['lbl' => $entity_name, 'val' => $entity_num];
}

$fields = [
	'entity_num' => [
		'name' => 'entity_num',
		'type' => 'radio',
		'direction' => 'RV',
		'options' => $entity_options,
		'checked' => 1,
		'label' => 'Entity'
	],
	's1' => [
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Switch'
	]
];

$form->set($fields);

view('Select Entity', [], 'index.php', 'entity');

