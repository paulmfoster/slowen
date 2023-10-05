<?php

class atxn extends controller
{
    public $cfg, $form, $nav, $db, $trans, $accounts, $bank_options;
    public $to_options, $ccard_options, $from_options, $payee_options, $status_options;
    public $page_title, $return, $focus_field;

    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
        load('memory');
        $this->trans = model('addtxn', $this->db);
    }

    function options()
    {
        global $atnames, $statuses;

        $payees = $this->trans->get_payees();
        $this->accounts = $this->trans->get_all_accounts();
        $bank_accts = $this->accounts['bank'];
        $ccard_accts = $this->accounts['ccard'];
        $from_accts = $this->accounts['from'];
        $to_accts = $this->accounts['to'];
        
        if ($payees == FALSE || $from_accts == FALSE || $to_accts == FALSE) {
            emsg('F', 'Payees and/or accounts missing.');
            redirect('index.php');
        }

        $this->bank_options = array();
        foreach($bank_accts as $bank_acct) {
            $this->bank_options[] = array('lbl' => 
                $bank_acct['name'] . ' ' . $atnames[$bank_acct['acct_type']], 
                'val' => $bank_acct['id']);
        }

        $this->from_options = array();
        foreach($from_accts as $from_acct) {
            $this->from_options[] = array('lbl' => 
                $from_acct['name'] . ' ' . $atnames[$from_acct['acct_type']], 
                'val' => $from_acct['id']);
        }

        $this->ccard_options = array();
        foreach($ccard_accts as $ccard_acct) {
            $this->ccard_options[] = array('lbl' => 
                $ccard_acct['name'] . ' ' . $atnames[$ccard_acct['acct_type']], 
                'val' => $ccard_acct['id']);
        }

        $this->payee_options = array();
        $this->payee_options[] = array('lbl' => 'NONE', 'val' => 0);
        foreach($payees as $payee) {
            $this->payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
        }

        $this->to_options = array();
        $this->to_options[] = ['lbl' => 'NONE', 'val' => 0];
        foreach($to_accts as $to_acct) {
            $this->to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
                'val' => $to_acct['id']);
        }

        $this->status_options = array();
        foreach($statuses as $key => $value) {
            $this->status_options[] = array('lbl' => $value, 'val' => $key);
        }
    }

    /**
     * Determine what to do after entering the main part of a transaction.
     *
     */

    function settle()
    {
        memory::merge($_POST);

        if ($_POST['status'] == 'V') {
            memory::set('amount', 0);
        }
        elseif (!empty($_POST['cr_amount'])) {
            memory::set('amount', $_POST['cr_amount']);
        }
        elseif (!empty($_POST['dr_amount'])) {
            memory::set('amount', - $_POST['dr_amount']);
        }
        else {
            memory::set('amount', 0);
        }

        $split = $_POST['split'] ?? 0;
        if ($split == 1) {
            redirect('index.php?c=atxn&m=split&max_splits=' . $_POST['max_splits']);
        }
        else {
            $_POST['max_splits'] = 0;
            memory::merge($_POST);
            redirect('index.php?c=atxn&m=verify');
        }
    }

    function split()
    {
        $max_splits = $_GET['max_splits'] ?? NULL;
        if (is_null($max_splits)) {
            emsg('F', 'For split transaction no number of splits specified');
            redirect('atxn', 'add');
        }

        $this->options();

        $fields = array(
            'max_splits' => array(
                'name' => 'max_splits',
                'type' => 'hidden',
                'value' => $max_splits
            ),
            'split_payee_id' => array(
                'name' => 'split_payee_id[]',
                'type' => 'select',
                'options' => $this->payee_options
            ),
            'split_to_acct' => array(
                'name' => 'split_to_acct[]',
                'type' => 'select',
                'options' => $this->to_options
            ),
            'split_memo' => array(
                'name' => 'split_memo[]',
                'type' => 'text',
                'size' => 35,
                'maxlength' => 35
            ),
            'split_cr_amount' => array(
                'name' => 'split_cr_amount[]',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12
            ),
            'split_dr_amount' => array(
                'name' => 'split_dr_amount[]',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12
            ),
            'save' => array(
                'name' => 'save',
                'type' => 'submit',
                'value' => 'Save'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Splits Entry';
        $data = ['max_splits' => $max_splits];
        $this->return = 'index.php?c=atxn&m=verify';
        $this->view('txnsplt.view.php', $data);
    }

    function verify()
    {
        global $statuses;

        if ($this->cfg['confirm_transactions'] == 0) {
            // for splits
            memory::merge($_POST);
            $txnid = $this->trans->add_transaction(memory::get_all());
            memory::clear();
            redirect('index.php?c=atxn&m=add');
        }

        $fields = array(
            'txnid' => array(
                'name' => 'txnid',
                'type' => 'hidden',
                'value' => memory::get('txnid')
            ),
            'confirm' => array(
                'name' => 'confirm',
                'type' => 'submit',
                'value' => 'Confirm'
            )
        );

        $this->form->set($fields);

        $data = memory::get_all();
        $data['x_status'] = $statuses[$data['status']];

        $names = $this->trans->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
        $data['from_acct_name'] = $names['from_acct_name'];
        $data['to_acct_name'] = $names['to_acct_name'];
        $data['payee_name'] = $names['payee_name'];
        $data['status_descrip'] = $statuses[$data['status']];

        if ($data['max_splits'] > 0) {
            for ($e = 0; $e < $data['max_splits']; $e++) {
                $names = $this->trans->get_split_names($data['split_payee_id'][$e], $data['split_to_acct'][$e]);
                $data['split_to_name'][$e] = $names['split_to_name'];
                $data['split_payee_name'][$e] = $names['split_payee_name'];
            }
        }

        $this->page_title = 'Confirm Transaction';
        $this->return = 'index.php?c=atxn&m=save';
        $this->view('txnvrfy.view.php', $data);
    }

    // This is where we end up if $cfg['confirm_transaction'] is true.
    function save()
    {
        $confirm = $_POST['confirm'] ?? NULL;
        if (!is_null($confirm)) {
            $txnid = $this->trans->add_transaction(memory::get_all());
            memory::clear();
        }
        else {
            emsg('Transaction save aborted.');
        }

        redirect('index.php?c=atxn&m=add');
    }

    function add()
    {
        global $atnames, $statuses;

        memory::clear();
        $this->options();

        $fields = array(
            'from_acct' => array(
                'name' => 'from_acct',
                'type' => 'select',
                'options' => $this->from_options
            ),
            'txn_dt' => array(
                'name' => 'txn_dt',
                'type' => 'date'
            ),
            'split' => array(
                'name' => 'split',
                'type' => 'checkbox',
                'value' => 1
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
                'options' => $this->payee_options
            ),
            'memo' => array(
                'name' => 'memo',
                'type' => 'text',
                'size' => 35,
                'maxlength' => 35
            ),
            'to_acct' => array(
                'name' => 'to_acct',
                'type' => 'select',
                'options' => $this->to_options
            ),
            'line_no' => array(
                'name' => 'line_no',
                'type' => 'hidden'
            ),
            'status' => array(
                'name' => 'status',
                'type' => 'select',
                'options' => $this->status_options
            ),
            'recon_dt' => array(
                'name' => 'recon_dt',
                'type' => 'date'
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
            'max_splits' => array(
                'name' => 'max_splits',
                'type' => 'text',
                'size' => 2,
                'maxlength' => 2
            ),
            'save' => array(
                'name' => 'save',
                'type' => 'submit',
                'value' => 'Save'
            )
        );
        $this->form->set($fields);
        $this->page_title = 'Enter Transaction';
        $this->focus_field = 'from_acct';
        $this->return = 'index.php?c=atxn&m=settle';
        $this->view('addtxn.view.php');
    }

}

