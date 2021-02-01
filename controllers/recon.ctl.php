<?php

class recon_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();

		$this->nav->init('links.php');
		require_once $this->cfg['modeldir'] . 'recon.mdl.php';
		$this->rcn = new recon($this->db);
	}

	function index()
	{
		// the first entry screen

		$accts = $this->rcn->get_recon_accts();

		$from_options = array();
		foreach ($accts as $acct) {
			$from_options[] = array('lbl' => $acct['acct_type'] . '/' . $acct['name'],
				'val' => $acct['acct_id']);
		}

		$fields = array(
			'from_acct' => array(
				'name' => 'from_acct',
				'type' => 'select',
				'options' => $from_options
			),
			'stmt_start_bal' => array(
				'name' => 'stmt_start_bal',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'stmt_end_bal' => array(
				'name' => 'stmt_end_bal',
				'type' => 'text',
				'size' => 12,
				'maxlength' => 12
			),
			'stmt_close_date' => array(
				'name' => 'stmt_close_date',
				'type' => 'date'
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Continue'
			)
		);
		$this->form->set($fields);
		$this->output('Reconciliation, Step 1', 'prerecon');

	}

	function list()
	{
		// user has entered preliminary data, and it's okay
		// so show transactions

		$acct = $this->rcn->get_account($_POST['from_acct']);
		$from_acct = $acct['acct_id'];
		$from_acct_name = $acct['name'];
		$open_bal = $acct['open_bal'];
		$stmt_start_bal = dec2int($_POST['stmt_start_bal']);
		$stmt_end_bal = dec2int($_POST['stmt_end_bal']);
		$stmt_close_date = $_POST['stmt_close_date'];
		$txns = $this->rcn->get_uncleared_transactions($_POST['from_acct']);

		$fields = array(
			'from_acct' => array(
				'name' => 'from_acct',
				'type' => 'hidden',
				'value' => $from_acct
			),
			'stmt_start_bal' => array(
				'name' => 'stmt_start_bal',
				'type' => 'hidden',
				'value' => $stmt_start_bal
			),
			'stmt_end_bal' => array(
				'name' => 'stmt_end_bal',
				'type' => 'hidden',
				'value' => $stmt_end_bal
			),
			'stmt_close_date' => array(
				'name' => 'stmt_close_date',
				'type' => 'hidden',
				'value' => $stmt_close_date
			),
			'from_acct_name' => array(
				'name' => 'from_acct_name',
				'type' => 'hidden',
				'value' => $from_acct_name
			),
			's3' => array(
				'name' => 's3',
				'type' => 'submit',
				'value' => 'Continue'
			)
		);

		$this->form->set($fields);

		// show transactions for clearing
		$this->output('Reconciliation, Step 2', 'reconlist', ['txns' => $txns, 'from_acct_name' => $from_acct_name]);
	}

	function complete()
	{
		// user has marked transactions to clear
	
		$cleared_list = implode(', ', $_POST['status']);
		$data = $this->rcn->check_reconciliation($_POST['from_acct'], $_POST['stmt_start_bal'], 
			$_POST['stmt_end_bal'], $cleared_list);

		if ($data === TRUE) {
			// everything balances
			$this->rcn->finish_reconciliation($_POST['from_acct'], $_POST['stmt_end_bal'], $_POST['stmt_close_date'], $cleared_list);
			emsg('S', "Reconciliation passes checks. Congratulations.");
			relocate('index.php?c=recon');
		}
		else {
			// reconciliation failed
			emsg('F', "Statement and computer final balances don't match.");
			$this->output('Reconciliation Failed', 'reconfailed', ['data' => $data]);
		}
	}

}
