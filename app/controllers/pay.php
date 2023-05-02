<?php

class pay extends controller
{
    public $cfg, $form, $nav, $db, $payee;
    public $page_title, $return, $focus_field;

    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
        $this->payee = model('payee', $this->db);
    }

    function list()
    {
        $payees = $this->payee->get_payees();
        $payee_options = [];
        foreach ($payees as $payee) {
            $payee_options[] = ['lbl' => $payee['name'], 'val' => $payee['id']];
        }

        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'select',
                'options' => $payee_options
            ],
            'edit' => [
                'name' => 'edit',
                'type' => 'submit',
                'value' => 'Edit'
            ],
            'delete' => [
                'name' => 'delete',
                'type' => 'submit',
                'value' => 'Delete'
            ]
        ];
        $this->form->set($fields);

        $this->page_title = 'List Payees';
        $this->focus_field = 'id';
        $this->return = url('pay', 'resolve');
        $this->view('paylst.view.php');
    }

    function resolve()
    {
        $edit = $_POST['edit'] ?? NULL;
        $delete = $_POST['delete'] ?? NULL;

        if (!is_null($edit)) {
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
        $fields = array(
            'name' => array(
                'name' => 'name',
                'type' => 'text',
                'size' => 35,
                'maxlength' => 35
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Save'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Add Payee';
        $this->return = url('pay', 'aconfirm');
        $this->focus_field = 'name';
        $this->view('payadd.view.php');
    }

    function aconfirm()
    {
        if (isset($_POST['s1'])) {
            $this->payee->add_payee($_POST['name']);
        }

        redirect(url('pay', 'add'));
    }

    function select($rtn)
    {
        $payees = $this->payee->get_payees();
        if ($payees == FALSE) {
            emsg('F', 'No payees on file.');
            redirect('index.php');
        }

        $id_options = array();
        foreach ($payees as $payee) {
            $id_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
        }

        $fields = array(
            'id' => array(
                'name' => 'id',
                'type' => 'select',
                'options' => $id_options
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Edit Payee'
            )
        );
        $this->form->set($fields);
        $this->page_title = 'Select Payee';
        $this->return = url('pay', $rtn);
        $this->focus_field = 'id';
        $this->view('paysel.view.php');

    }

    function edit($id)
    {
        if (is_null($id)) {
            $this->list();
        }
        $payee = $this->payee->get_payee($id);

        $fields = array(
            'id' => array(
                'name' => 'id',
                'type' => 'hidden',
                'value' => $id
            ),
            'name' => array(
                'name' => 'name',
                'type' => 'text',
                'size' => 35,
                'maxlength' => 35,
                'value' => $payee['name']
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Update'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Edit Payee';
        $this->focus_field = 'name';
        $this->return = url('pay', 'econfirm');
        $this->view('payedt.view.php', ['payee' => $payee]);
    }

    function econfirm()
    {
        if (isset($_POST['s1'])) {
            $this->payee->update_payee($_POST['id'], $_POST['name']); 
        }	

        redirect(url('pay', 'show', $_POST['id']));
    }

    function show($id)
    {
        $p = $this->payee->get_payee($id);
        $this->page_title = 'Show Payee';
        $this->view('payshow.view.php', ['payee' => $p]);
    }

    function delete($id)
    {
        if (is_null($id)) {
            $this->list();
        }
        $p = $this->payee->get_payee($id);

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
        $this->page_title = 'Delete Payee';
        $this->focus_field = 'name';
        $this->return = url('pay', 'dconfirm');
        $this->view('paydel.view.php', ['payee' => $p]);

    }

    function dconfirm()
    {
        $id = $_POST['id'] ?? NULL;
        if (!is_null($id)) {
            $this->payee->delete_payee($_POST['id']);
        }

        redirect(url('pay', 'list'));
    }

    function search()
    {
        $payees = $this->payee->get_payees();
        if ($payees == FALSE) {
            emsg('F', 'No payees on file.');
            redirect('index.php');
        }

        $payee_options = array();
        if ($payees !== FALSE) {
            foreach ($payees as $p) {
                $payee_options[] = array('lbl' => $p['name'], 'val' => $p['id']);
            }
        }

        $fields = array(
            'payee' => array(
                'name' => 'payee',
                'type' => 'select',
                'options' => $payee_options
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Search'
            )
        );
        $this->form->set($fields);
        $this->page_title = 'Search Payees';

        $this->focus_field = 'payee';
        $this->return = url('pay', 'results');
        $this->view('paysrch.view.php');

    }

    function results()
    {
        $txns = model('transaction', $this->db);

        $payee = $_POST['payee'] ?? NULL;

        if (!is_null($payee)) {
            $transactions = $txns->get_transactions($payee, 'P');
        }
        else {
            redirect('index.php');
        }

        $this->page_title = 'Search Results';
        $this->view('results.view.php', ['transactions' => $transactions]);
    }


}
