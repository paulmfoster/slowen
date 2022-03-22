<?php

class txn extends controller
{
    function __construct()
    {
		global $init;
        list($this->cfg, $this->form, $this->nav, $this->db) = $init;
        $this->trans = model('transaction', $this->db);
    }


    function show($txnid = NULL)
    {
        if (is_null($txnid)) {
            redirect('index.php');
        }

        $txns = $this->trans->get_transaction($txnid);
        if ($txns[0]['split'] == 1) {
            $splits = $this->trans->get_splits($txns[0]['txnid']);
        }
        else {
            $splits = NULL;
        }

        $this->page_title = 'Show Transaction';
        $this->view('txnshow.view.php', ['txns' => $txns, 'splits' => $splits]);

    }

    function edit($txnid = NULL)
    {
        global $atnames, $statuses;

        $txnid = $txnid ?? NULL;
        if (is_null($txnid)) {
            redirect('index.php');
        }

        // $trans = model('transaction', $this->db);
        $txns = $this->trans->get_transaction($txnid);

        $max_txns = count($txns);

        $payees = $this->trans->get_payees();
        $payee_options[] = ['lbl' => 'NONE', 'val' => 0];
        foreach($payees as $payee) {
            $payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['payee_id']);
        }

        $status_options = array();
        foreach ($statuses as $key => $value) {
            $status_options[] = array('lbl' => $value, 'val' => $key);
        }

        $to_accts = $this->trans->get_to_accounts();
        $to_options = array();
        foreach ($to_accts as $to_acct) {
            $to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
                'val' => $to_acct['acct_id']);
        }

        $splits = $this->trans->get_splits($txns[0]['txnid']);
        if ($splits !== FALSE) {
            $max_splits = count($splits);
        }
        else {
            $max_splits = 0;
        }

        $split_to_options = array();
        foreach ($to_accts as $to_acct) {
            $split_to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
                'val' => $to_acct['acct_id']);
        }

        if ($max_txns == 1) {

            // single transaction

            $fields = array(
                'acct_id' => array(
                    'name' => 'acct_id',
                    'type' => 'hidden'
                ),
                'txntype' => array(
                    'name' => 'txntype',
                    'type' => 'hidden'
                ),
                'txnid' => array(
                    'name' => 'txnid',
                    'type' => 'hidden'
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
                'payee_id' => array(
                    'name' => 'payee_id',
                    'type' => 'select',
                    'options' => $payee_options
                ),
                'memo' => array(
                    'name' => 'memo',
                    'type' => 'text',
                    'size' => 35,
                    'maxlength' => 35
                ),
                'status' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'options' => $status_options
                ),
                'recon_dt' => array(
                    'name' => 'recon_dt',
                    'type' => 'text',
                    'size' => 10,
                    'maxlength' => 10
                ),
                'to_acct' => array(
                    'name' => 'to_acct',
                    'type' => 'select',
                    'options' => $to_options
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
                'amount' => array(
                    'name' => 'amount',
                    'type' => 'text',
                    'size' => 12,
                    'maxlength' => 12
                ),
                's1' => array(
                    'name' => 's1',
                    'type' => 'submit',
                    'value' => 'Save Edits'
                ),
                // only used for splits
                'split_id' => array(
                    'name' => 'split_id[]',
                    'type' => 'hidden'
                ),
                'split_payee_id' => array(
                    'name' => 'split_payee_id[]',
                    'type' => 'select',
                    'options' => $payee_options
                ),
                'split_to_acct' => array(
                    'name' => 'split_to_acct[]',
                    'type' => 'select',
                    'options' => $split_to_options
                ),
                'split_memo' => array(
                    'name' => 'split_memo[]',
                    'type' => 'text',
                    'size' => 35,
                    'maxlength' => 35
                ),
                'split_amount' => array(
                    'name' => 'split_amount[]',
                    'type' => 'text',
                    'size' => 12,
                    'maxlength' => 12
                )
            );

            $this->form->set($fields);

            $data = ['txns' => $txns, 'statuses' => $statuses, 'max_txns' => $max_txns, 'max_splits' => $max_splits, 'splits' => $splits];
            $this->page_title = 'Edit Transaction';
            $this->return = 'index.php?url=txn/update';
            $this->view('txnedt.view.php', $data);

        }
        else {

            // inter-account transfer

            $fields = array(
                'txntype' => array(
                    'name' => 'txntype',
                    'type' => 'hidden',
                    'value' => 'xfer'
                ),
                'txnid' => array(
                    'name' => 'txnid',
                    'type' => 'hidden',
                    'value' => $txns[0]['txnid']
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
                'payee_id' => array(
                    'name' => 'payee_id',
                    'type' => 'select',
                    'options' => $payee_options
                ),
                'memo' => array(
                    'name' => 'memo',
                    'type' => 'text',
                    'size' => 35,
                    'maxlength' => 35
                ),
                's1' => array(
                    'name' => 's1',
                    'type' => 'submit',
                    'value' => 'Save Edits'
                )
            );

            $this->form->set($fields);

            $data = ['txns' => $txns, 'statuses' => $statuses];
            $this->page_title = 'Edit Inter-Account Transfer';
            $this->return = 'index.php?url=txn/update';

            $this->view('xferedt.view.php', $data);
        }

    }

    function update()
    {
        $txnid = $_POST['txnid'] ?? NULL;
        if (is_null($txnid)) {
            redirect('index.php');
        }
        // $trans = model('transaction', $this->db);
        $this->trans->update_transaction($_POST);
        $this->show($_POST['txnid']);
    }

    function void($txnid)
    {
        $txnid = $txnid ?? NULL;
        if (is_null($txnid)) {
            redirect('index.php');
        }

        $txns = $this->trans->get_transaction($txnid);
        if ($txns[0]['split'] == 1) {
            $splits = $this->trans->get_splits($txns[0]['txnid']);
        }
        else {
            $splits = [];
        }

        $fields = array(
            'txnid' => array(
                'name' => 'txnid',
                'type' => 'hidden',
                'value' => $txnid
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Confirm'
            )
        );
        $this->form->set($fields);

        $this->page_title = 'Void Transaction';
        $this->return = 'index.php?url=txn/vconfirm';
        $data = ['txns' => $txns, 'splits' => $splits];
        $this->view('txnvoid.view.php', $data);

    }

    function vconfirm()
    {
        $txnid = $_POST['txnid'] ?? NULL;
        if (is_null($txnid)) {
            redirect('index.php');
        }

        $this->trans->void_transaction($txnid);
        $this->show($txnid);
        // redirect("index.php?url=txn/show/$txnid");

    }

}

