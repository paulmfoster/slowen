<?php

class register extends controller
{
	function __construct()
	{
		global $init;
        list($this->cfg, $this->form, $this->nav, $this->db) = $init;
	}

	function select()
	{
		$acct = $this->model('account', $this->db);
		$accounts = $acct->get_from_accounts();
		$acct_options = [];
		foreach ($accounts as $acct) {
			$acct_options[] = ['lbl' => $acct['name'], 'val' => $acct['acct_id']];
		}

		$fields = [
			'acct_id' => [
				'name' => 'acct_id',
				'type' => 'select',
				'options' => $acct_options
			],
			's1' => [
				'name' => 's1',
				'type' => 'submit',
				'value' => 'Report'
			]
		];

		$this->form->set($fields);

        $this->return = 'index.php?url=register/show';
        $this->page_title = 'Register: Select Account';

		$this->view('acctsel.view.php');
	}

    function show()
    {
        if (!isset($_POST['acct_id'])) {
            $this->select();
        }

        $txns = model('transaction', $this->db);

        $acct = $txns->get_account($_POST['acct_id']);
        $r = $txns->get_transactions($_POST['acct_id'], 'F');

        $this->page_title = 'Account Register';

        $this->view('register.view.php', ['acct' => $acct, 'r' => $r]);

    }

}

