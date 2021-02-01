<?php

class report_controller extends master_controller
{
	function __construct()
	{
		parent::__construct();

		$this->nav->init('links.php');
		require_once $this->cfg['modeldir'] . 'report.mdl.php';
		$this->rpt = new report($this->db);
	}

	function search()
	{
		$payees = $this->rpt->get_payees();
		$categories = $this->rpt->get_accounts();

		$vendor_options = array();
		foreach ($payees as $payee) {
			$vendor_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
		}

		$cat_options = array();
		foreach ($categories as $cat) {
			$cat_options[] = array('lbl' => $cat['name'] . ' (' . $this->acct_types[$cat['acct_type']] . ')',
				'val' => $cat['acct_id']);
		}

		$fields = array(
			'vendor' => array(
				'name' => 'vendor',
				'type' => 'select',
				'options' => $vendor_options
			),
			'category' => array(
				'name' => 'category',
				'type' => 'select',
				'options' => $cat_options
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Search By Vendor'
			),
			's2' => array(
				'name' => 's2',
				'type' => 'submit',
				'value' => 'Search By Category'
			)
		);

		$this->form->set($fields);
		$this->output('Search', 'search');
		$form = new form($fields);
	}

	function results()
	{
		$byvendor = $_POST['s1'] ?? NULL;
		$bycategory = $_POST['s2'] ?? NULL;

		if (!is_null($byvendor)) {
			$transactions = $this->rpt->get_transactions($_POST['vendor'], 'P');
		}
		elseif (!is_null($bycategory)) {
			$transactions = $this->rpt->get_transactions($_POST['category'], 'C');
		}
		else {
			relocate('index.php');
		}

		$this->output('Search Results', 'results', ['transactions' => $transactions]);

	}

	function balances()
	{
		$fields = [
			'last_dt' => [
				'name' => 'last_dt',
				'type' => 'date'
			],
			's1' => [
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Compute'
			]
		];

		$this->form->set($fields);
		$this->output('Pick Date For Balances', 'balances', ['stage' => 'pick_date']);
	}

	function balances2()
	{
		if (!empty($_POST['last_dt'])) {
			$bals = $this->rpt->get_balances($_POST['last_dt']);
			$today = $_POST['last_dt'];
		}
		else {
			$bals = $this->rpt->get_balances();
			$today = pdate::now2iso();
		}

		if ($bals === FALSE) {
			emsg('F', 'Date is too early to show balances');
			relocate('index.php?c=report&m=search');
		}

		$d = [
			'stage' => 'show_bals',
			'today' => $today,
			'bals' => $bals
		];

		$this->output('Balances Results', 'balances', $d);
	}

	function expenses()
	{
		$temp_date = pdate::now();

		$oto_date = pdate::endwk($temp_date);
		// $ato_date = pdate::get($oto_date, 'm/d/y');
		$ito_date = pdate::get($oto_date, 'Y-m-d');

		$ofrom_date = pdate::adddays($oto_date, -6);
		// $afrom_date = pdate::get($ofrom_date, 'm/d/y');
		$ifrom_date = pdate::get($ofrom_date, 'Y-m-d');

		$fields = array(
			'from_date' => array(
				'name' => 'from_date',
				'type' => 'date',
				'value' => $ifrom_date
			),
			'to_date' => array(
				'name' => 'to_date',
				'type' => 'date',
				'value' => $ito_date
			),
			's1' => array(
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Calculate'
			)
		);
		$this->form->set($fields);
		$this->output('Dates For Expense Report', 'expenses', ['stage' => 1]);
	}

	function expenses2()
	{
		$expenses = $this->rpt->get_expenses($_POST['from_date'], $_POST['to_date']);
		$this->output('Expense Report', 'expenses', ['stage' => 2, 'expenses' => $expenses]);
	}
}
