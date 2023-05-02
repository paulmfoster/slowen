<?php

class register extends controller
{
    public $cfg, $form, $nav, $db;
    public $page_title, $return, $focus_field;

	function __construct()
	{
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
	}

	function select()
	{
		$acct = $this->model('account', $this->db);
		$accounts = $acct->get_from_accounts();
		$acct_options = [];
		foreach ($accounts as $acct) {
			$acct_options[] = ['lbl' => $acct['name'], 'val' => $acct['id']];
		}

		$fields = [
			'id' => [
				'name' => 'id',
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

        $this->return = url('register', 'show');
        $this->page_title = 'Register: Select Account';

		$this->view('acctsel.view.php');
	}

    function show($id = NULL)
    {
        if (is_null($id)) {
            if (!isset($_POST['id'])) {
                $this->select();
            }
            else {
                $id = $_POST['id'];
            }
        }

        $txns = model('transaction', $this->db);

        $acct = $txns->get_account($id);
        $r = $txns->get_transactions($id, 'F');

        $this->page_title = 'Account Register';
        $this->view('register.view.php', ['acct' => $acct, 'r' => $r]);
    }

}

