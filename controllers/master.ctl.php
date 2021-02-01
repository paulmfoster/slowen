<?php

class master_controller
{
	function __construct()
	{
		global $cfg;

		require_once $cfg['libdir'] . 'navigation.lib.php';
		$this->nav = new navigation();

		if (!isset($_SESSION['entity_num'])) {
			$this->entity();
			exit();
		}
		$cfg['dbdata'] = 'slowen' . $_SESSION['entity_num'] . '.sq3';

		$this->cfg = $cfg;

		require_once $cfg['libdir'] . 'database.lib.php';
		$this->db = new database($this->cfg);
		require_once $cfg['libdir'] . 'form.lib.php';
		$this->form = new form();

		define('DECIMALS', 2);
		define('DECIMAL_SYMBOL', '.');

		$this->acct_types = array(
			'I' => 'Income',
			'E' => 'Expense',
			'L' => 'Liability',
			'A' => 'Asset',
			'Q' => 'Equity',
			'R' => 'Credit Card',
			'C' => 'Checking',
			'S' => 'Savings'
		);
		$this->max_acct_types = count($this->acct_types);

		$this->statuses = array(
			'C' => 'Cleared',
			'R' => 'Reconciled',
			'V' => 'Void',
			' ' => 'Uncleared'
		);
		$this->max_statuses = count($this->statuses);
	}

	function output($title, $view, $data = [], $debug = FALSE)
	{
		if ($debug) {
			instrument('data', $data);
			die('---');
		}
		$page_title = $title;
		$view_file = $view . '.view.php';

		if (!empty($data)) {
			extract($data);
		}

		$form = $this->form;

		include $this->cfg['viewdir'] . 'head.view.php';
		include $this->cfg['viewdir'] . $view_file;
		include $this->cfg['viewdir'] . 'footer.view.php';
	}

	function index()
	{
		// default view, default controller
		$this->nav->init('links.php');
		$this->output("Home", 'index');
	}

	function entity()
	{
		$this->nav->init('links.php');
		$entities = array();
		foreach ($this->cfg['entity'] as $index => $value) {
			$entities[] = array('entity_num' => $index, 'entity_name' => $value);
		}

		$this->output('Select Entity', 'entity', ['entities' => $entities]);
	}

	function entity2()
	{
		if (!empty($_POST)) {
			foreach ($this->cfg['entity'] as $index => $value) {
				if ($_POST['entity_num'] == $index) {
					$_SESSION['entity_num'] = $index;
					$_SESSION['entity_name'] = $value;
					emsg('S', "Entity has been set to {$_SESSION['entity_name']}.");
					break;
				}
			}
		}
		relocate('index.php?c=master');

	}

	function history()
	{
		$this->nav->init('links.php');
		$this->output('History', 'history');
	}
}

