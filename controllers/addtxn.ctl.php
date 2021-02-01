<?php

class addtxn_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();

		$this->nav->init('links.php');
		require_once $this->cfg['modeldir'] . 'addtxn.mdl.php';
		$this->addtxn = new addtxn($this->db);
		require_once $this->cfg['libdir'] . 'memory.lib.php';
		$this->atnames = array(
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
	}

	function add()
	{
		memory::clear();
		$this->output('Add Transaction', 'txnadd');
	}

	function verify()
	{
		$s1 = $_POST['s1'] ?? NULL;
		if (is_null($s1)) {
			relocate('index.php?c=addtxn&m=add');
		}

		// when coming back from the split screen, POST only has the
		// splits data, nothing else
		$from_split = $_POST['from_split'] ?? 0;
		if ($from_split == 1) {
			for ($i = 0; $i < $_POST['max_splits']; $i++) {
				$snames = $this->addtxn->get_split_names($_POST['split_payee_id'][$i], $_POST['split_to_acct'][$i]);
				$_POST['split_payee_name'][$i] = $snames['split_payee_name'];
				$_POST['split_to_name'][$i] = $snames['split_to_name'];
			}
			$_POST = array_merge($_POST, memory::get_all());
		}

		$names = $this->addtxn->get_names($_POST['from_acct'], $_POST['payee_id'], $_POST['to_acct']);

		// adjust amount depending on transaction type
		switch ($_POST['txntype']) {
		case 'check':
		case 'ccard':
		case 'xfer':
			$_POST['amount'] = - $_POST['amount'];
			break;
		case 'other':
			if (!empty($_POST['cr_amount'])) {
				$_POST['amount'] = $_POST['cr_amount'];
			}
			elseif (!empty($_POST['dr_amount'])) {
				$_POST['amount'] = - $_POST['dr_amount'];
			}
			else {
				$_POST['amount'] = 0;
			}
			break;
		// case deposit omitted; amount needs no manipulation
		}

		memory::merge($_POST);
		memory::set('from_acct_name', $names['from_acct_name']);
		memory::set('to_acct_name', $names['to_acct_name']);
		memory::set('payee_name', $names['payee_name']);
		memory::set('status_descrip', $this->statuses[$_POST['status']]);

		$fields = array(
			's3' => array(
				'name' => 's3',
				'type' => 'submit',
				'value' => 'Confirm'
			)
		);

		$this->form->set($fields);

		$this->output('Verify Transaction', 'txnvrfy', ['data' => memory::get_all()]);
	}

	function save()
	{
		$saved = $this->addtxn->add_transaction(memory::get_all());
		memory::clear();
		relocate('index.php?c=addtxn&m=add');
	}

	function check()
	{
		$accounts = $this->addtxn->get_accounts();
		$payees = $this->addtxn->get_payees();
		$from_accts = $this->addtxn->get_bank_accounts();
		$to_accts = $this->addtxn->get_to_accounts();

		$from_options = array();
		foreach($from_accts as $from_acct) {
			$from_options[] = array('lbl' => 
				$from_acct['name'] . ' ' . $this->atnames[$from_acct['acct_type']], 
				'val' => $from_acct['acct_id']);
		}

		$payee_options = array();
		$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
		foreach($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$to_options = array();
		foreach($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $this->atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$fields = array(
			'status' => array(
				'name' => 'status',
				'type' => 'hidden',
				'value' => ' '
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'hidden',
				'value' => ''
			),
			'txntype' => array(
				'name' => 'txntype',
				'type' => 'hidden',
				'value' => 'check'
			),
			'from_acct' => array(
				'name' => 'from_acct',
				'type' => 'select',
				'options' => $from_options
			),
			'txn_dt' => array(
				'name' => 'txn_dt',
				'type' => 'date'
			),
			'checkno' => array(
				'name' => 'checkno',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'select',
				'options' => $payee_options
			),
			'memo' => array(
				'name' => 'memo',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'to_acct' => array(
				'name' => 'to_acct',
				'type' => 'select',
				'options' => $to_options
			),
			'amount' => array(
				'name' => 'amount',
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

		$this->output('Enter Check', 'chkadd');

	}

	function deposit()
	{
		$accounts = $this->addtxn->get_accounts();
		$payees = $this->addtxn->get_payees();
		$from_accts = $this->addtxn->get_from_accounts();
		$to_accts = $this->addtxn->get_to_accounts();

		$from_options = array();
		foreach($from_accts as $from_acct) {
			$from_options[] = array('lbl' => 
				$from_acct['name'] . ' ' . $this->atnames[$from_acct['acct_type']], 
				'val' => $from_acct['acct_id']);
		}

		$payee_options = array();
		$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
		foreach($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$to_options = array();
		foreach($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $this->atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$fields = array(
			'status' => array(
				'name' => 'status',
				'type' => 'hidden',
				'value' => ' '
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'hidden',
				'value' => ''
			),
			'txntype' => array(
				'name' => 'txntype',
				'type' => 'hidden',
				'value' => 'deposit'
			),
			'from_acct' => array(
				'name' => 'from_acct',
				'type' => 'select',
				'options' => $from_options
			),
			'txn_dt' => array(
				'name' => 'txn_dt',
				'type' => 'date'
			),
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'select',
				'options' => $payee_options
			),
			'memo' => array(
				'name' => 'memo',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'to_acct' => array(
				'name' => 'to_acct',
				'type' => 'select',
				'options' => $to_options
			),
			'amount' => array(
				'name' => 'amount',
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

		$this->output('Enter Deposit', 'depadd');
	}

	function ccard()
	{
		$accounts = $this->addtxn->get_accounts();
		$payees = $this->addtxn->get_payees();
		$from_accts = $this->addtxn->get_from_accounts();
		$to_accts = $this->addtxn->get_to_accounts();

		$from_options = array();
		foreach($from_accts as $from_acct) {
			$from_options[] = array('lbl' => 
				$from_acct['name'] . ' ' . $this->atnames[$from_acct['acct_type']], 
				'val' => $from_acct['acct_id']);
		}

		$payee_options = array();
		$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
		foreach($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$to_options = array();
		foreach($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $this->atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$fields = array(
			'status' => array(
				'name' => 'status',
				'type' => 'hidden',
				'value' => ' '
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'hidden',
				'value' => ''
			),
			'txntype' => array(
				'name' => 'txntype',
				'type' => 'hidden',
				'value' => 'ccard'
			),
			'from_acct' => array(
				'name' => 'from_acct',
				'type' => 'select',
				'options' => $from_options
			),
			'txn_dt' => array(
				'name' => 'txn_dt',
				'type' => 'date'
			),
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'select',
				'options' => $payee_options
			),
			'memo' => array(
				'name' => 'memo',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'to_acct' => array(
				'name' => 'to_acct',
				'type' => 'select',
				'options' => $to_options
			),
			'status' => array(
				'name' => 'status',
				'type' => 'hidden',
				'value' => ' '
			),
			'amount' => array(
				'name' => 'amount',
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

		$this->output('Enter Credit Card Transaction', 'ccardadd');
	}

	function xfer()
	{
		$accounts = $this->addtxn->get_accounts();
		$payees = $this->addtxn->get_payees();
		$from_accts = $this->addtxn->get_from_accounts();
		$to_accts = $this->addtxn->get_to_accounts();

		$from_options = array();
		foreach($from_accts as $from_acct) {
			$from_options[] = array('lbl' => 
				$from_acct['name'] . ' ' . $this->atnames[$from_acct['acct_type']], 
				'val' => $from_acct['acct_id']);
		}

		$payee_options = array();
		$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
		foreach($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$to_options = array();
		foreach($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $this->atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$fields = array(
			'status' => array(
				'name' => 'status',
				'type' => 'hidden',
				'value' => ' '
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'hidden',
				'value' => ''
			),
			'txntype' => array(
				'name' => 'txntype',
				'type' => 'hidden',
				'value' => 'xfer'
			),
			'from_acct' => array(
				'name' => 'from_acct',
				'type' => 'select',
				'options' => $from_options
			),
			'txn_dt' => array(
				'name' => 'txn_dt',
				'type' => 'date'
			),
			'xfer' => array(
				'name' => 'xfer',
				'type' => 'hidden',
				'value' => 1
			),
			'checkno' => array(
				'name' => 'checkno',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'select',
				'options' => $payee_options
			),
			'memo' => array(
				'name' => 'memo',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'to_acct' => array(
				'name' => 'to_acct',
				'type' => 'select',
				'options' => $to_options
			),
			'amount' => array(
				'name' => 'amount',
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

		$this->output('Inter-Account Transfer', 'xferadd');
	}

	function other()
	{
		$accounts = $this->addtxn->get_accounts();
		$payees = $this->addtxn->get_payees();
		$from_accts = $this->addtxn->get_from_accounts();
		$to_accts = $this->addtxn->get_to_accounts();

		$from_options = array();
		foreach($from_accts as $from_acct) {
			$from_options[] = array('lbl' => 
				$from_acct['name'] . ' ' . $this->atnames[$from_acct['acct_type']], 
				'val' => $from_acct['acct_id']);
		}

		$payee_options = array();
		$payee_options[] = array('lbl' => 'NONE', 'val' => 0);
		foreach($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$to_options = array();
		foreach($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $this->atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$status_options = [];
		foreach($this->statuses as $key => $value) {
			$status_options[] = ['lbl' => $value, 'val' => $key];
		}

		$fields = array(
			'txntype' => array(
				'name' => 'txntype',
				'type' => 'hidden',
				'value' => 'other'
			),
			'from_acct' => array(
				'name' => 'from_acct',
				'type' => 'select',
				'options' => $from_options
			),
			'txn_dt' => array(
				'name' => 'txn_dt',
				'type' => 'date'
			),
			'xfer' => array(
				'name' => 'xfer',
				'type' => 'checkbox',
				'value' => 1
			),
			'split' => array(
				'name' => 'split',
				'type' => 'checkbox',
				'value' => 1
			),
			'checkno' => array(
				'name' => 'checkno',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'payee_id' => array(
				'name' => 'payee_id',
				'type' => 'select',
				'options' => $payee_options
			),
			'memo' => array(
				'name' => 'memo',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'to_acct' => array(
				'name' => 'to_acct',
				'type' => 'select',
				'options' => $to_options
			),
			'line_no' => array(
				'name' => 'line_no',
				'type' => 'hidden'
			),
			'status' => array(
				'name' => 'status',
				'type' => 'select',
				'options' => $status_options
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'date'
			),
			'dr_amount' => array(
				'name' => 'dr_amount',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'cr_amount' => array(
				'name' => 'cr_amount',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'max_splits' => array(
				'name' => 'max_splits',
				'type' => 'text',
				'size' => 2,
				'maxlength' => 2
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save'
			)
		);

		$this->form->set($fields);
		$this->output('Enter Transaction', 'othadd');
	}

	function other2()
	{
		$s1 = $_POST['s1'] ?? NULL;
		if (is_null($s1)) {
			relocate('index.php?c=addtxn&m=other');
		}

		if ($_POST['split'] != 1) {
			$this->verify();
		}
		else {
			memory::merge($_POST);
			$this->split();
		}

	}

	private function split()
	{
		$payees = $this->addtxn->get_payees();
		$to_accts = $this->addtxn->get_split_to_accounts();
		$payee_options = array();
		foreach ($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$to_options = array();
		foreach ($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $this->atnames[$to_acct['acct_type']],
				'val' => $to_acct['acct_id']);
		}

		$fields = array(
			'max_splits' => array(
				'name' => 'max_splits',
				'type' => 'hidden',
				'value' => $_POST['max_splits']
			),
			'from_split' => array(
				'name' => 'from_split',
				'type' => 'hidden',
				'value' => 1
			),
			'split_payee_id' => array(
				'name' => 'split_payee_id[]',
				'type' => 'select',
				'options' => $payee_options
			),
			'split_to_acct' => array(
				'name' => 'split_to_acct[]',
				'type' => 'select',
				'options' => $to_options
			),
			'split_memo' => array(
				'name' => 'split_memo[]',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'split_cr_amount' => array(
				'name' => 'split_cr_amount[]',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'split_dr_amount' => array(
				'name' => 'split_dr_amount[]',
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
		$this->output('Enter Splits', 'txnsplt', ['txn' => memory::get_all()]);
	}

}
