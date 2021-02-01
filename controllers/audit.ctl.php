<?php

class audit_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();

		$this->nav->init('links.php');
		require_once $this->cfg['modeldir'] . 'audit.mdl.php';
		$this->aud = new audit($this->db);
	}

	function index()
	{
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

		// $state == 0
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
				'value' => 'Calculate'
			)
		);
		$this->form->set($fields);
		$this->output('Enter Audit Criteria', 'audit', ['state' => 0, 'focus_field' => 'month']);
	}

	function show()
	{
		$data = $this->aud->audit($_POST['year'], $_POST['month']);
		$this->output('Audit Data', 'audit', ['state' => 1, 'data' => $data]);
	}

}
