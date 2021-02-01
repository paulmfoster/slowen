<?php

class entity_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();
		$this->nav->init('links.php');
	}

	function index()
	{
		$entities = array();
		foreach ($this->cfg['entity'] as $index => $value) {
			$entities[] = array('entity_num' => $index, 'entity_name' => $value);
		}

		$this->output('Select Entity', 'entity', ['entities' => $entities]);
	}

}
