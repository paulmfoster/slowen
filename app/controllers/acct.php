<?php

class acct extends controller
{
    public $cfg, $form, $nav, $db, $account;
    public $page_title, $return, $focus_field;

    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;

        $this->account = model('account', $this->db);
    }

    function list()
    {
        global $atnames;

        $accts = $this->account->get_accounts();
        $acct_options = [];
        foreach ($accts as $acct) {
            $acct_options[] = [
                'lbl' => $acct['name'] . ' ' . $atnames[$acct['acct_type']], 
                'val' => $acct['id']
            ];
        }

        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'select',
                'options' => $acct_options
            ],
            'show' => [
                'name' => 'show',
                'type' => 'submit',
                'value' => 'Show'
            ],
            'edit' => [
                'name' => 'edit',
                'type' => 'submit',
                'value' => 'Edit',
            ],
            'delete' => [
                'name' => 'delete',
                'type' => 'submit',
                'value' => 'Delete'
            ]
        ];
        $this->form->set($fields);
        $this->page_title = 'Accounts List';
        $this->focus_field = 'id';
        $this->return = url('acct', 'resolve');
        $this->view('acctlst.view.php');
    }

    function resolve()
    {
        $edit = $_POST['edit'] ?? NULL;
        $delete = $_POST['delete'] ?? NULL;
        $show = $_POST['show'] ?? NULL;
        if (!is_null($show)) {
            $this->show($_POST['id']);
        }
        elseif (!is_null($edit)) {
            $this->edit($_POST['id']);
        }
        elseif (!is_null($delete)) {
            $this->delete($_POST['id']);
        }
        else {
            $this->list();
        }
    }

    function add()
    {
        global $acct_types;

        $parents = $this->account->get_parents();
        $parent_options = array();
        foreach ($parents as $parent) {
            $parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['id']);
        }

        $acct_type_options = array();
        foreach ($acct_types as $key => $value) {
            $acct_type_options[] = array('lbl' => $value, 'val' => $key);
        }

        $today = new xdate();

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
                'value' => $today->to_iso(),
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
        $this->return = url('acct', 'aconfirm');
        $this->view('acctadd.view.php');

    }

    function aconfirm()
    {
        if (isset($_POST['s1'])) {
            $this->account->add_account($_POST);
        }
        redirect(url('acct', 'list'));
    }

    function edit($id)
    {
        global $acct_types;

        if (is_null($id)) {
            $this->list();
        }

        $acct = $this->account->get_account($id);

        $parents = $this->account->get_parents();
        $parent_options = array();
        foreach ($parents as $parent) {
            $parent_options[] = array('lbl' => $parent['name'], 'val' => $parent['id']);
        }

        $acct_type_options = array();
        foreach ($acct_types as $key => $value) {
            $acct_type_options[] = array('lbl' => $value, 'val' => $key);
        }

        $fields = array(
            'id' => array(
                'name' => 'id',
                'type' => 'hidden',
                'value' => $id
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
        $this->return = url('acct', 'econfirm');
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

        redirect(url('acct', 'show', $_POST['id']));
    }

    function show($id)
    {
        global $acct_types;

        $acct = $this->account->get_account($id);
        $acct['x_acct_type'] = $acct_types[$acct['acct_type']];
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'hidden',
                'value' => $acct['id']
            ],
            'edit' => [
                'name' => 'edit',
                'type' => 'submit',
                'value' => 'Edit',
            ],
            'delete' => [
                'name' => 'delete',
                'type' => 'submit',
                'value' => 'Delete'
            ]
        ];
        $this->form->set($fields);
        $this->return = url('acct', 'resolve');

        $this->page_title = 'Show Account';
        $this->view('acctshow.view.php', ['acct' => $acct]);
    }

    function delete($id)
    {
        global $acct_types;

        if (is_null($id)) {
            $this->list();
        }

        $acct = $this->account->get_account($id);
        $acct['x_acct_type'] = $acct_types[$acct['acct_type']];

        $fields = array(
            'id' => array(
                'name' => 'id',
                'type' => 'hidden',
                'value' => $id
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Delete'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Delete Account';
        $this->return = url('acct', 'dconfirm');
        $this->view('acctdel.view.php', ['acct' => $acct]);
    }

    function dconfirm()
    {
        if (isset($_POST['s1'])) {
            $this->account->delete_account($_POST['id']); 
        }
        redirect(url('acct', 'list'));
    }

    function search()
    {
        global $acct_types;

        $categories = $this->account->get_accounts();

        $cat_options = array();
        foreach ($categories as $cat) {
            $cat_options[] = array('lbl' => $cat['name'] . ' (' . $acct_types[$cat['acct_type']] . ')',
                'val' => $cat['id']);
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

        $this->focus_field = 'category';
        $this->page_title = 'Search By Category/Account';
        $this->return = url('acct', 'results');
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
            $this->list();
        }

        $this->page_title = 'Search Results';
        $this->view('results.view.php', ['transactions' => $transactions]);
    }

}

