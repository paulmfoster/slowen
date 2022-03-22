<?php

class acct extends controller
{
    function __construct()
    {
		global $init;
        list($this->cfg, $this->form, $this->nav, $this->db) = $init;

        $this->account = model('account', $this->db);
    }

    function add()
    {
        global $acct_types;

        $parents = $this->account->get_parents();
        $parent_options = array();
        foreach ($parents as $parent) {
            $parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['acct_id']);
        }

        $acct_type_options = array();
        foreach ($acct_types as $key => $value) {
            $acct_type_options[] = array('lbl' => $value, 'val' => $key);
        }

        $fields = array(
            'parent' => array(
                'name' => 'parent',
                'type' => 'select',
                'required' => 1,
                'options' => $parent_options
            ),
            'open_dt' => array(
                'name' => 'open_dt',
                'required' => 1,
                'type' => 'date',
                'value' => pdate::toiso(pdate::now()),
            ),
            'recon_dt' => array(
                'name' => 'recon_dt',
                'type' => 'date'
            ),
            'acct_type' => array(
                'name' => 'acct_type',
                'type' => 'select',
                'required' => 1,
                'options' => $acct_type_options
            ),
            'name' => array(
                'name' => 'name',
                'type' => 'text',
                'size' => 35,
                'required' => 1,
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
                'value' => 0,
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
        $this->page_title = 'Add Account';
        $this->return = 'index.php?url=acct/aconfirm';
        $this->view('acctadd.view.php');

    }

    function aconfirm()
    {
        if (isset($_POST['s1'])) {
            $this->account->add_account($_POST);
        }
        redirect('index.php');
    }

    function select($rtn)
    {
        global $atnames;

        $accounts = $this->account->get_accounts();

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
                'value' => 'Select'
            )
        );

        $this->form->set($fields);

        $this->page_title = 'Select Account';
        $this->return = 'index.php?url=acct/' . $rtn;

        $this->view('acctsel.view.php');
    }

    function edit()
    {
        global $acct_types;

        $acct_id = $_POST['acct_id'] ?? NULL;
        if (is_null($acct_id)) {
            redirect('index.php?url=acct/select');
        }

        $acct = $this->account->get_account($acct_id);

        $parents = $this->account->get_parents();
        $parent_options = array();
        foreach ($parents as $parent) {
            $parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['acct_id']);
        }

        $acct_type_options = array();
        foreach ($acct_types as $key => $value) {
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
        $this->page_title = 'Edit Account';
        $this->return = 'index.php?url=acct/econfirm';
        $data = ['acct' => $acct];
        $this->view('acctedt.view.php', $data);

    }

    function econfirm()
    {
        if (isset($_POST['s1'])) {
            if ($this->account->update_account($_POST)) {
                emsg('S', "Account edits SAVED");
            }
        }	

        $this->show($_POST['acct_id']);
    }

    function show($acct_id)
    {
        global $acct_types;

        $acct = $this->account->get_account($acct_id);
        $acct['x_acct_type'] = $acct_types[$acct['acct_type']];
        $this->page_title = 'Show Account';
        $this->view('acctshow.view.php', ['acct' => $acct]);
    }

    function delete()
    {
        global $acct_types;

        $acct_id = $_POST['acct_id'] ?? NULL;
        if (is_null($acct_id)) {
            redirect('index.php');
        }

        $acct = $this->account->get_account($acct_id);
        $acct['x_acct_type'] = $acct_types[$acct['acct_type']];

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
        $this->page_title = 'Delete Account';
        $this->return = 'index.php?url=acct/dconfirm';
        $this->view('acctdel.view.php', ['acct' => $acct]);
    }

    function dconfirm()
    {
        if (isset($_POST['s1'])) {
            $this->account->delete_account($_POST['acct_id']); 
        }
        redirect('index.php');
    }

    function search()
    {
        global $acct_types;

        $categories = $this->account->get_accounts();

        $cat_options = array();
        foreach ($categories as $cat) {
            $cat_options[] = array('lbl' => $cat['name'] . ' (' . $acct_types[$cat['acct_type']] . ')',
                'val' => $cat['acct_id']);
        }

        $fields = array(
            'category' => array(
                'name' => 'category',
                'type' => 'select',
                'options' => $cat_options
            ),
            's1' => array(
                'name' => 's2',
                'type' => 'submit',
                'value' => 'Search'
            )
        );
        $this->form->set($fields);

        $this->page_title = 'Search By Category/Account';
        $this->return = 'index.php?url=acct/results';
        $this->view('acctsrch.view.php');

    }

    function results()
    {
        $txns = model('transaction', $this->db);

        $acct = $_POST['category'] ?? NULL;

        if (!is_null($acct)) {
            $transactions = $txns->get_transactions($acct, 'C');
        }
        else {
            redirect('index.php');
        }

        $this->page_title = 'Search Results';
        $this->view('results.view.php', ['transactions' => $transactions]);
    }

}

