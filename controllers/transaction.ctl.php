<?php

class transaction_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();

		$this->nav->init('links.php');
		require_once $this->cfg['modeldir'] . 'transaction.mdl.php';
		$this->txn = new transaction($this->db);
		require_once $this->cfg['libdir'] . 'memory.lib.php';

		$this->statuses = array(
			'C' => 'Cleared',
			'R' => 'Reconciled',
			'V' => 'Void',
			' ' => 'Uncleared'
		);
	}

	function register($get = [])
	{
		$acct_id = $get['acct_id'] ?? NULL;
		if (is_null($acct_id)) {
			relocate('index.php');
			exit();
		}

		$acct = $this->txn->get_account($_GET['acct_id']);
		$r = $this->txn->get_transactions($_GET['acct_id'], 'F');

		$d = [
			'acct' => $acct,
			'r' => $r
		];

		$this->output('Register', 'register', $d);
	}

	function show($get = [])
	{
		$txnid = $get['txnid'] ?? NULL;
		if (is_null($txnid)) {
			relocate('index.php');
		}

		$txns = $this->txn->get_transaction($_GET['txnid']);
		if ($txns[0]['split'] == 1) {
			$splits = $this->txn->get_splits($txns[0]['txnid']);
		}
		else {
			$splits = NULL;
		}

		$d = [
			'txns' => $txns,
			'splits' => $splits
		];

		$this->output('Show Transaction', 'txnshow', $d);
	}

	function edit($get = [])
	{
		$txnid = $get['txnid'] ?? NULL;
		if (is_null($txnid)) {
			relocate('index.php');
		}

		$txns = $this->txn->get_transaction($_GET['txnid']);
		$max_txns = count($txns);
	
		if ($max_txns > 1) {
			// iaxfer
			$this->edit_iaxfer($txns);
		}
		else {
			if ($txns[0]['split'] == 1) {
				// split
				$this->edit_split($txns[0]);
			}
			else {
				// single transaction
				$this->edit_single($txns[0]);
			}
		}

	}

	private function edit_single($txn)
	{
		$payees = $this->txn->get_payees();
		$payee_options = array();
		foreach($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$atnames = array(
			' ' => '(none)',
			'I' => '(inc)',
			'E' => '(exp)',
			'L' => '(liab)',
			'A' => '(asset)',
			'Q' => '(eqty)',
			'R' => '(ccard)',
			'C' => '(chkg)',
			'S' => '(svgs)'
		);

		$status_options = array();
		foreach ($this->statuses as $key => $value) {
			$status_options[] = array('lbl' => $value, 'val' => $key);
		}

		$to_accts = $this->txn->get_to_accounts();
		$to_options = array();
		foreach ($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$fields = array(
			'txnid' => array(
				'name' => 'txnid',
				'type' => 'hidden',
				'value' => $txn['txnid']
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
			'status' => array(
				'name' => 'status',
				'type' => 'select',
				'options' => $status_options
			),
			'to_acct' => array(
				'name' => 'to_acct',
				'type' => 'select',
				'options' => $to_options
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
			'amount' => array(
				'name' => 'amount',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save Edits'
			)
		);

		$this->form->set($fields);

		$d = ['txn' => $txn];
		$this->output('Edit Transaction', 'txnedt', $d);

	}

	function edit1()
	{
		$this->txn->update_single($_POST);
		emsg('S', 'Transaction edits saved');
		relocate('index.php?c=transaction&m=show&txnid=' . $_POST['txnid']);
	}

	private function edit_split($txn)
	{
		// single transaction, with splits

		$atnames = array(
			' ' => '(none)',
			'I' => '(inc)',
			'E' => '(exp)',
			'L' => '(liab)',
			'A' => '(asset)',
			'Q' => '(eqty)',
			'R' => '(ccard)',
			'C' => '(chkg)',
			'S' => '(svgs)'
		);

		$status_options = array();
		foreach ($this->statuses as $key => $value) {
			$status_options[] = array('lbl' => $value, 'val' => $key);
		}

		$payees = $this->txn->get_payees();
		array_unshift($payees, array('payee_id' => '0', 'name' => 'NONE'));
		$payee_options = array();
		foreach($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$to_accts = $this->txn->get_to_accounts();
		$to_options = array();
		foreach ($to_accts as $to_acct) {
			$to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$splits = $this->txn->get_splits($txn['txnid']);
		$max_splits = count($splits);

		$split_to_options = array();
		foreach ($to_accts as $to_acct) {
			$split_to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
				'val' => $to_acct['acct_id']);
		}

		$fields = array(
			'acct_id' => array(
				'name' => 'acct_id',
				'type' => 'hidden'
			),
			'txntype' => array(
				'name' => 'txntype',
				'type' => 'hidden'
			),
			'txnid' => array(
				'name' => 'txnid',
				'type' => 'hidden'
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
			'status' => array(
				'name' => 'status',
				'type' => 'select',
				'options' => $status_options
			),
			'recon_dt' => array(
				'name' => 'recon_dt',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10
			),
			'to_acct' => array(
				'name' => 'to_acct',
				'type' => 'select',
				'options' => $to_options
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
			'amount' => array(
				'name' => 'amount',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save Edits'
			),
			'split_id' => array(
				'name' => 'split_id[]',
				'type' => 'hidden'
			),
			'split_payee_id' => array(
				'name' => 'split_payee_id[]',
				'type' => 'select',
				'options' => $payee_options
			),
			'split_to_acct' => array(
				'name' => 'split_to_acct[]',
				'type' => 'select',
				'options' => $split_to_options
			),
			'split_memo' => array(
				'name' => 'split_memo[]',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'split_amount' => array(
				'name' => 'split_amount[]',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			)
		);

		$d = ['txn' => $txn, 'splits' => $splits];
		$this->form->set($fields);
		$this->output('Edit Split Transaction', 'splitedt', $d);

	}

	function edits()
	{
		$okay = $this->txn->update_splits($_POST);
		if ($okay) {
			emsg('S', 'Transaction edits saved');
		}
		else {
			emsg('F', "Transaction not saved. Split amounts don't equal transaction amount");
		}
		relocate('index.php?c=transaction&m=show&txnid=' . $_POST['txnid']);
	}

	private function edit_iaxfer($txns)
	{
		$payees = $this->txn->get_payees();
		$payee_options = array();
		foreach($payees as $payee) {
			$payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$fields = array(
			'txnid' => array(
				'name' => 'txnid',
				'type' => 'hidden',
				'value' => $txns[0]['txnid']
			),
			'id1' => array(
				'name' => 'id1',
				'type' => 'hidden',
				'value' => $txns[0]['id']
			),
			'id2' => array(
				'name' => 'id2',
				'type' => 'hidden',
				'value' => $txns[1]['id']
			),
			'txn_dt' => array(
				'name' => 'txn_dt',
				'type' => 'date',
				'value' => $txns[0]['txn_dt']
			),
			'checkno' => array(
				'name' => 'checkno',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'payee_id1' => array(
				'name' => 'payee_id1',
				'type' => 'select',
				'options' => $payee_options
			),
			'payee_id2' => array(
				'name' => 'payee_id2',
				'type' => 'select',
				'options' => $payee_options
			),
			'memo1' => array(
				'name' => 'memo1',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			'memo2' => array(
				'name' => 'memo2',
				'type' => 'text',
				'size' => 35,
				'maxlength' => 35
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Save Edits'
			)
		);

		$this->form->set($fields);
		$this->output('Edit Inter-Account Transfer', 'iaxedt', ['txns' => $txns]);
	}

	function editx()
	{
		$this->txn->update_xfer($_POST);
		relocate('index.php?c=transaction&m=show&txnid=' . $_POST['txnid']);
	}

	function add()
	{
		$this->output('Add Transaction', 'txnadd');
	}


}
