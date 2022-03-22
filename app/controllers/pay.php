<?php

class pay extends controller
{
    function __construct()
    {
		global $init;
        list($this->cfg, $this->form, $this->nav, $this->db) = $init;
        $this->payee = model('payee', $this->db);
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
        $this->return = 'index.php?url=pay/aconfirm';
        $this->focus_field = 'name';
        $this->view('payadd.view.php');
    }

    function aconfirm()
    {
        if (isset($_POST['s1'])) {
            $this->payee->add_payee($_POST['name']);
        }
        $this->add();
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
            $id_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
        }

        $fields = array(
            'payee_id' => array(
                'name' => 'payee_id',
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
        $this->return = 'index.php?url=pay/' . $rtn;
        $this->focus_field = 'payee_id';
        $this->view('paysel.view.php');

    }

    function edit()
    {
        $payee_id = $_POST['payee_id'] ?? NULL;
        if (is_null($payee_id)) {
            redirect('index.php');
        }
        $payee = $this->payee->get_payee($payee_id);

        $fields = array(
            'payee_id' => array(
                'name' => 'payee_id',
                'type' => 'hidden',
                'value' => $payee_id
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
        $this->return = 'index.php?url=pay/econfirm';
        $this->view('payedt.view.php', ['payee' => $payee]);
    }

    function econfirm()
    {
        if (isset($_POST['s1'])) {
            $this->payee->update_payee($_POST['payee_id'], $_POST['name']); 
        }	

        $this->show($_POST['payee_id']);
    }

    function show($payee_id)
    {
        $p = $this->payee->get_payee($payee_id);
        $this->page_title = 'Show Payee';
        $this->view('payshow.view.php', ['payee' => $p]);
    }

    function delete()
    {
        $payee_id = $_POST['payee_id'] ?? NULL;
        if (is_null($payee_id)) {
            redirect('paydel.php');
        }
        $p = $this->payee->get_payee($payee_id);

        $fields = array(
            'payee_id' => array(
                'name' => 'payee_id',
                'type' => 'hidden',
                'value' => $payee_id
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
        $this->return = 'index.php?url=pay/dconfirm';
        $this->view('paydel.view.php', ['payee' => $p]);

    }

    function dconfirm()
    {
        $payee_id = $_POST['payee_id'] ?? NULL;
        if (!is_null($payee_id)) {
            $this->payee->delete_payee($_POST['payee_id']);
        }

        redirect('index.php');
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
                $payee_options[] = array('lbl' => $p['name'], 'val' => $p['payee_id']);
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

        $this->return = 'index.php?url=pay/results';
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
