<?php

class account_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();

		$this->nav->init('links.php');
		require_once $this->cfg['modeldir'] . 'account.mdl.php';
		$this->acct = new account($this->db);
	}

	function list()
	{
		$accounts = $this->acct->get_from_accounts();
		$this->output('Account List', 'acctlist', ['accounts' => $accounts]);
	}

	function index()
	{
		$accounts = $this->acct->get_accounts();

		$atnames = array(
			' ' => '',
			'I' => '(inc)',
			'E' => '(exp)',
			'L' => '(liab)',
			'A' => '(asset)',
			'Q' => '(eqty)',
			'R' => '(ccard)',
			'C' => '(chkg)',
			'S' => '(svgs)'
		);

		$acct_options = array();
		foreach ($accounts as $account) {
			$acct_options[] = array('lbl' => $account['name'] . ' ' . $atnames[$account['acct_type']], 'val' => $account['acct_id']);
		}

		$fields = array(
			'acct_id' => array(
				'name' => 'acct_id',
				'type' => 'select',
				'options' => $acct_options
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Add Account'
			),
			's2' => array(
				'name' => 's2',
				'type' => 'submit',
				'value' => 'Edit Account'
			),
			's3' => array(
				'name' => 's3',
				'type' => 'submit',
				'value' => 'Delete Account'
			)
		);
		$this->form->set($fields);
		$this->output('Accounts', 'accounts');
	}

	function index2()
	{
		if (isset($_POST['s1'])) {
			$this->add();
		}
		if (isset($_POST['s2'])) {
			$this->edit(['acct_id' => $_POST['acct_id']]);
		}
		if (isset($_POST['s3'])) {
			$this->delete(['acct_id' => $_POST['acct_id']]);
		}
	}

	function edit($get = [])
	{
		$acct_id = $get['acct_id'] ?? NULL;
		if (is_null($acct_id)) {
			$this->index();
			exit();
		}

		$acct = $this->acct->get_account($acct_id);

		$parents = $this->acct->get_parents();
		$parent_options = array();
		foreach ($parents as $parent) {
			$parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['acct_id']);
		}

		$acct_type_options = array();
		foreach ($this->acct_types as $key => $value) {
			$acct_type_options[] = array('lbl' => $value, 'val' => $key);
		}

		$fields = array(
			'acct_id' => array(
				'name' => 'acct_id',
				'type' => 'hidden',
				'value' => $acct_id
			),
			'parent' => array(
				'name' => 'parent',
				'type' => 'select',
				'options' => $parent_options
			),
			'open_dt' => array(
				'name' => 'open_dt',
				'type' => 'date'
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'date'
			),
			'acct_type' => array(
				'name' => 'acct_type',
				'type' => 'select',
				'options' => $acct_type_options
			),
			'name' => array(
				'name' => 'name',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'descrip' => array(
				'name' => 'descrip',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 255 
			),
			'open_bal' => array(
				'name' => 'open_bal',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12 
			),
			'rec_bal' => array(
				'name' => 'rec_bal',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12 
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save'
			)
		);

		$this->form->set($fields);
		$this->output('Edit Account', 'acctedt', ['acct' => $acct]);

	}

	function edit2()
	{
		if ($this->acct->update_account($_POST)) {
			emsg('S', "Account edits SAVED");
		}
		else {
			emsg('F', "Account update FAILED");
		}
		relocate('index.php?c=account');
	}

	function delete($get = [])
	{
		$acct_id = $get['acct_id'] ?? NULL;
		if (is_null($acct_id)) {
			$this->index();
			exit();
		}

		$acct = $this->acct->get_account($acct_id);
		foreach ($this->acct_types as $key => $val) {
			if ($acct['acct_type'] == $key) {
				$acct['x_acct_type'] = $val;
				break;
			}
		}

		$fields = array(
			'acct_id' => array(
				'name' => 'acct_id',
				'type' => 'hidden',
				'value' => $acct_id
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Delete'
			)
		);
		$this->form->set($fields);

		$this->output('Delete Account', 'acctdel', ['acct' => $acct]);
	}

	function delete2()
	{
		if (!$this->acct->delete_account($_POST['acct_id'])) {
			emsg('F', 'Account deletion FAILED.');
		}

		relocate('index.php?c=account');
	}

	function add()
	{
		$parents = $this->acct->get_parents();
		$parent_options = array();
		foreach ($parents as $parent) {
			$parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['acct_id']);
		}

		$acct_type_options = array();
		foreach ($this->acct_types as $key => $value) {
			$acct_type_options[] = array('lbl' => $value, 'val' => $key);
		}

		$fields = array(
			'parent' => array(
				'name' => 'parent',
				'type' => 'select',
				'options' => $parent_options
			),
			'open_dt' => array(
				'name' => 'open_dt',
				'type' => 'date'
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'date'
			),
			'acct_type' => array(
				'name' => 'acct_type',
				'type' => 'select',
				'options' => $acct_type_options
			),
			'name' => array(
				'name' => 'name',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'descrip' => array(
				'name' => 'descrip',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 255 
			),
			'open_bal' => array(
				'name' => 'open_bal',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12 
			),
			'rec_bal' => array(
				'name' => 'rec_bal',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12 
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save'
			)
		);

		$this->form->set($fields);
		$this->output('Add Account', 'acctadd');

	}

	function add2()
	{
		if (isset($_POST['s1'])) {
			$sm->add_account($_POST);
		}
		else {
			relocate('index.php?c=account&add');
		}
	}


}

