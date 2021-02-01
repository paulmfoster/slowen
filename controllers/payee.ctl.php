<?php

class payee_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();

		$this->nav->init('links.php');
		require_once $this->cfg['modeldir'] . 'payee.mdl.php';
		$this->payee = new payee($this->db);
	}

	function index()
	{
		$payees = $this->payee->get_payees();

		$id_options = array();
		foreach ($payees as $payee) {
			$id_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$fields = array(
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'select',
				'options' => $id_options
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Add Payee'
			),
			's2' => array(
				'name' => 's2',
				'type' => 'submit',
				'value' => 'Edit Payee'
			),
			's3' => array(
				'name' => 's3',
				'type' => 'submit',
				'value' => 'Delete Payee'
			)
		);

		$this->form->set($fields);
		$this->output('Payees', 'payees');

	}

	function index2()
	{
		if (!empty($_POST['s1'])) {
			relocate('index.php?c=payee&m=add');
		}
		elseif (!empty($_POST['s2'])) {
			relocate('index.php?c=payee&m=edit&payee_id=' . $_POST['payee_id']);
		}
		elseif (!empty($_POST['s3'])) {
			relocate('index.php?c=payee&m=delete&payee_id=' . $_POST['payee_id']);
		}
	}

	function add()
	{
		$fields = array(
			'name' => array(
				'name' => 'name',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save'
			)
		);

		$this->form->set($fields);
		$this->output('Add Payee', 'payadd');

	}

	function add2()
	{
		if (isset($_POST['s1'])) {
			$this->payee->add_payee($_POST['name']);
		}
	}

	function edit($get = [])
	{
		$payee_id = $_GET['payee_id'] ?? NULL;
		if (is_null($payee_id)) {
			$this->index();
		}

		$payee = $this->payee->get_payee($_GET['payee_id']);

		$fields = array(
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'hidden',
				'value' => $payee_id
			),
			'name' => array(
				'name' => 'name',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35,
				'value' => $payee['name']
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Update'
			)
		);

		$this->form->set($fields);

		$this->output('Edit Payee', 'payedt');
	}

	function edit2()
	{
		if (isset($_POST['s1'])) {
			$this->payee->update_payee($_POST['payee_id'], $_POST['name']);
		}
		relocate('index.php?c=payee');
	}

	function delete()
	{
		$payee = $this->payee->get_payee($_GET['payee_id']);
		$fields = array(
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'hidden',
				'value' => $payee['payee_id']
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Delete'
			)
		);

		$this->form->set($fields);
		$this->output('Delete Payee', 'paydel', ['payee' => $payee]);
	}

	function delete2()
	{
		if (!empty($_POST['s1'])) {
			$this->payee->delete_payee($_POST['payee_id']);
		}
		relocate('index.php?c=payee');
	}

}
