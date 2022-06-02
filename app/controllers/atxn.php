<?php

class atxn extends controller
{
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
            $this->split();
        }
        else {
            $this->verify();
        }
    }

    private function split()
    {
        $max_splits = $_POST['max_splits'];

        $this->options();

        $fields = array(
            'max_splits' => array(
                'name' => 'max_splits',
                'type' => 'hidden',
                'value' => $_POST['max_splits']
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
        $data = ['max_splits' => $_POST['max_splits']];
        $this->return = url('atxn', 'verify');
        $this->view('txnsplt.view.php', $data);
    }

    function verify()
    {
        global $statuses;

        if ($this->cfg['confirm_transactions'] == 0) {
            $txnid = $this->trans->add_transaction(memory::get_all());
            memory::clear();
            $this->add();
        }

        memory::merge($_POST);

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
        $this->return = url('atxn', 'save');
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

        $this->add();
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
        $this->return = url('atxn', 'settle');
        $this->view('addtxn.view.php');
    }

    /****************************************************************
     * ITEMS BELOW ARE DEPRECATED.
     ****************************************************************/

    function check()
    {
        memory::clear();
        $this->options();

        $fields = array(
            'from_acct' => array(
                'name' => 'from_acct',
                'type' => 'select',
                'options' => $this->bank_options
            ),
            'txn_dt' => array(
                'name' => 'txn_dt',
                'type' => 'date'
            ),
            'checkno' => array(
                'name' => 'checkno',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12
            ),
            'status' => array(
                'name' => 'status',
                'type' => 'checkbox',
                'value' => 'V'
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
            'dr_amount' => array(
                'name' => 'dr_amount',
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
        $this->page_title = 'Enter Check';
        $this->return = url('atxn', 'chkvrfy');
        $this->focus_field = 'from_acct';
        $this->view('chkadd.view.php');
    }

    function chkvrfy()
    { 
        memory::merge($_POST);
        if (strlen(trim($_POST['dr_amount'])) == 0) {
            $_POST['dr_amount'] = 0;
        }
        memory::set('amount', - $_POST['dr_amount']);

        if ($this->cfg['confirm_transactions'] == 0) {
            $txnid = $this->trans->add_transaction(memory::get_all());
            memory::clear();
            $this->check();
        }

        $fields = array(
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Confirm'
            )
        );

        $this->form->set($fields);

        $data = $_POST;

        $names = $this->trans->get_names($_POST['from_acct'], $_POST['payee_id'], $_POST['to_acct']);
        $data['from_acct_name'] = $names['from_acct_name'];
        $data['to_acct_name'] = $names['to_acct_name'];
        $data['payee_name'] = $names['payee_name'];

        $this->page_title = 'Confirm Check';
        $this->return = 'index.php?url=atxn/save/check';
        $this->view('chkvrfy.view.php', $data);
    }

    function deposit()
    {
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
            'cr_amount' => array(
                'name' => 'cr_amount',
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
        $this->page_title = 'Enter Deposit';
        $this->focus_field = 'from_acct';
        $this->return = 'index.php?url=atxn/depvrfy';
        $this->view('depadd.view.php');
    }

    function depvrfy()
    {
        memory::merge($_POST);
        memory::set('amount', $_POST['cr_amount']);

        if ($this->cfg['confirm_transactions'] == 0) {
            $txnid = $this->trans->add_transaction(memory::get_all());
            $this->deposit();
        }

        $fields = array(
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Confirm'
            )
        );

        $this->form->set($fields);

        $data = $_POST;
        $names = $this->trans->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
        $data['from_acct_name'] = $names['from_acct_name'];
        $data['to_acct_name'] = $names['to_acct_name'];
        $data['payee_name'] = $names['payee_name'];

        $this->page_title = 'Confirm Deposit';
        $this->return = 'index.php?url=atxn/save/deposit';
        $this->view('depvrfy.view.php', $data);

    }

    function ccard()
    {
        memory::clear();
        $this->options();

        $fields = array(
            'from_acct' => array(
                'name' => 'from_acct',
                'type' => 'select',
                'options' => $this->ccard_options
            ),
            'txn_dt' => array(
                'name' => 'txn_dt',
                'type' => 'date'
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
            'status' => array(
                'name' => 'status',
                'type' => 'hidden',
                'value' => ' '
            ),
            'dr_amount' => array(
                'name' => 'dr_amount',
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
        $this->page_title = 'Enter Payment By Credit Card';
        $this->return = 'index.php?url=atxn/ccardvrfy';
        $this->focus_field = 'from_acct';
        $this->view('ccardadd.view.php');
    }

    function ccardvrfy()
    {
        memory::merge($_POST);
        memory::set('amount', - $_POST['dr_amount']);

        if ($this->cfg['confirm_transactions'] == 0) {
            $txnid = $this->trans->add_transaction(memory::get_all());
            memory::clear();
            $this->ccard();
        }

        $fields = array(
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Confirm'
            )
        );
        $this->form->set($fields);

        $data = memory::get_all();
        $names = $this->trans->get_names($_POST['from_acct'], $_POST['payee_id'], $_POST['to_acct']);
        $data['from_acct_name'] = $names['from_acct_name'];
        $data['to_acct_name'] = $names['to_acct_name'];
        $data['payee_name'] = $names['payee_name'];

        $this->page_title = 'Confirm Credit Card Charge';
        $this->return = 'index.php?url=atxn/save/ccard';
        $this->view('ccardvrfy.view.php', $data);

    }

    function transfer()
    {
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
            'xfer' => array(
                'name' => 'xfer',
                'type' => 'hidden',
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
            'dr_amount' => array(
                'name' => 'dr_amount',
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
        $this->page_title = 'Enter Inter-Account Transfer';
        $this->focus_field = 'from_acct';
        $this->return = 'index.php?url=atxn/xfervrfy';
        $this->view('xferadd.view.php');
    }

    function xfervrfy()
    {
        memory::merge($_POST);
        memory::set('amount', - $_POST['dr_amount']);

        if ($this->cfg['confirm_transactions'] == 0) {
            $txnid = $this->trans->add_transaction(memory::get_all());
            memory::clear();
            $this->transfer();
        }

        $fields = array(
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Confirm'
            )
        );
        $this->form->set($fields);

        $data = $_POST;
        $names = $this->trans->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
        $data['from_acct_name'] = $names['from_acct_name'];
        $data['to_acct_name'] = $names['to_acct_name'];
        $data['payee_name'] = $names['payee_name'];

        $this->page_title = 'Confirm Inter-Account Transfer';
        $this->return = 'index.php?url=atxn/save/transfer';
        $this->view('xfervrfy.view.php', $data);
    }

    function other()
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
            'xfer' => array(
                'name' => 'xfer',
                'type' => 'checkbox',
                'value' => 1
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
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Save'
            )
        );
        $this->form->set($fields);
        $this->page_title = 'Enter Transaction';
        $this->focus_field = 'from_acct';
        $this->return = 'index.php?url=atxn/split';
        $this->view('othadd.view.php');
    }

    function othvrfy()
    {
        global $statuses;

        memory::merge($_POST);

        if ($this->cfg['confirm_transactions'] == 0) {
            $txnid = $this->trans->add_transaction(memory::get_all());
            memory::clear();
            $this->other();
        }

        $fields = array(
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Confirm'
            )
        );

        $this->form->set($fields);

        $data = memory::get_all();
        $names = $this->trans->get_names($data['from_acct'], $data['payee_id'], $data['to_acct']);
        $data['from_acct_name'] = $names['from_acct_name'];
        $data['to_acct_name'] = $names['to_acct_name'];
        $data['payee_name'] = $names['payee_name'];
        $data['status_descrip'] = $statuses[$data['status']];

        if (isset($data['split']) && $data['max_splits'] > 0) {
            for ($e = 0; $e < $data['max_splits']; $e++) {
                $names = $this->trans->get_split_names($data['split_payee_id'][$e], $data['split_to_acct'][$e]);
                $data['split_to_name'][$e] = $names['split_to_name'];
                $data['split_payee_name'][$e] = $names['split_payee_name'];
            }
        }

        $this->page_title = 'Confirm Transaction';
        $this->return = 'index.php?url=atxn/save/other';
        $this->view('othvrfy.view.php', $data);
    }

    // This is an additional verification step where there are splits.
    function old_split()
    {
        memory::merge($_POST);

        if (!empty($_POST['cr_amount'])) {
            memory::set('amount', $_POST['cr_amount']);
        }
        elseif (!empty($_POST['dr_amount'])) {
            memory::set('amount', - $_POST['dr_amount']);
        }
        else {
            memory::set('amount', 0);
        }

        $split = $_POST['split'] ?? 0;
        if ($split == 0) {
            $this->othvrfy();
        }

        $max_splits = $_POST['max_splits'];

        $this->options();

        $fields = array(
            'max_splits' => array(
                'name' => 'max_splits',
                'type' => 'hidden',
                'value' => $_POST['max_splits']
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
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Save'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Splits Entry';
        $data = ['max_splits' => $_POST['max_splits']];
        $this->return = 'index.php?url=atxn/othvrfy';
        $this->view('txnsplt.view.php', $data);
    }

}

