<?php

class txn extends controller
{
    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
        $this->trans = model('transaction', $this->db);
    }

    function show($txnid = NULL)
    {
        if (is_null($txnid)) {
            redirect('index.php');
        }

        $txns = $this->trans->get_transaction($txnid);
        if ($txns[0]['split'] == 1) {
            $splits = $this->trans->get_splits($txns[0]['id']);
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

        if (count($txns) > 1) {
            $this->editxfer($txns);
        }
        elseif ($txns[0]['split']) {
            $this->editsplits($txns);
        }
        else {
            $this->editsingle($txns);
        }
    }

    function editsingle($txns)
    {
        global $statuses, $atnames;

        $payees = $this->trans->get_payees();
        $payee_options[] = ['lbl' => 'NONE', 'val' => 0];
        foreach($payees as $payee) {
            $payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
        }

        $status_options = array();
        foreach ($statuses as $key => $value) {
            $status_options[] = array('lbl' => $value, 'val' => $key);
        }

        $to_accts = $this->trans->get_to_accounts();
        $to_options = array();
        foreach ($to_accts as $to_acct) {
            $to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
                'val' => $to_acct['id']);
        }

        $fields = array(
            'txnid' => array(
                'name' => 'txnid',
                'type' => 'hidden',
                'value' => $txns[0]['txnid']
            ),
            'txntype' => array(
                'name' => 'txntype',
                'type' => 'hidden',
                'value' => 'single'
            ), 
            'txn_dt' => array(
                'name' => 'txn_dt',
                'type' => 'date',
                'value' => $txns[0]['txn_dt']
            ),
            'checkno' => array(
                'name' => 'checkno',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12,
                'value' => $txns[0]['checkno']
            ),
            'payee_id' => array(
                'name' => 'payee_id',
                'type' => 'select',
                'options' => $payee_options,
                'value' => $txns[0]['payee_id']
            ),
            'memo' => array(
                'name' => 'memo',
                'type' => 'text',
                'size' => 35,
                'maxlength' => 35,
                'value' => $txns[0]['memo']
            ),
            'to_acct' => array(
                'name' => 'to_acct',
                'type' => 'select',
                'options' => $to_options,
                'value' => $txns[0]['to_acct']
            ),
            'save' => array(
                'name' => 'save',
                'type' => 'submit',
                'value' => 'Save Edits'
            ),
        );

        if ($txns[0]['status'] != 'R' && $txns[0]['status'] != 'V') {
            $fields['amount'] = [
                'name' => 'amount',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12,
                'value' => $txns[0]['amount']
            ];
        }

        $this->form->set($fields);

        $data = ['txns' => $txns, 'statuses' => $statuses];
        $this->page_title = 'Edit Single Transaction';
        $this->return = url('txn', 'update');
        $this->view('txnedt.view.php', $data);
    }

    function editxfer($txns)
    {
        global $statuses, $atnames;

        $max_txns = count($txns);

        $payees = $this->trans->get_payees();
        $payee_options[] = ['lbl' => 'NONE', 'val' => 0];
        foreach($payees as $payee) {
            $payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
        }

        $to_accts = $this->trans->get_to_accounts();
        $to_options = array();
        foreach ($to_accts as $to_acct) {
            $to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
                'val' => $to_acct['id']);
        }

        $fields = array(
            'txnid' => array(
                'name' => 'txnid',
                'type' => 'hidden',
                'value' => $txns[0]['txnid']
            ),
            'txntype' => array(
                'name' => 'txntype',
                'type' => 'hidden',
                'value' => 'xfer'
            ), 
            'txn_dt' => array(
                'name' => 'txn_dt',
                'type' => 'date',
                'value' => $txns[0]['txn_dt']
            ),
            'checkno' => array(
                'name' => 'checkno',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12,
                'value' => $txns[0]['checkno']
            ),
            'payee_id' => array(
                'name' => 'payee_id',
                'type' => 'select',
                'options' => $payee_options,
                'value' => $txns[0]['payee_id']
            ),
            'memo' => array(
                'name' => 'memo',
                'type' => 'text',
                'size' => 35,
                'maxlength' => 35,
                'value' => $txns[0]['memo']
            ),
            'save' => array(
                'name' => 'save',
                'type' => 'submit',
                'value' => 'Save Edits'
            )
        );

        $this->form->set($fields);

        $data = ['txns' => $txns, 'statuses' => $statuses];
        $this->page_title = 'Edit Inter-Account Transfer';
        $this->return = url('txn', 'update');

        $this->view('xferedt.view.php', $data);

    }

    function editsplits($txns)
    {
        global $statuses, $atnames;

        $payees = $this->trans->get_payees();
        $payee_options[] = ['lbl' => 'NONE', 'val' => 0];
        foreach($payees as $payee) {
            $payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
        }

        $to_accts = $this->trans->get_to_accounts();
        $to_options = array();
        foreach ($to_accts as $to_acct) {
            $to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
                'val' => $to_acct['id']);
        }

        $splits = $this->trans->get_splits($txns[0]['id']);
        if ($splits !== FALSE) {
            $max_splits = count($splits);
        }
        else {
            $max_splits = 0;
        }

        $split_to_options = array();
        foreach ($to_accts as $to_acct) {
            $split_to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
                'val' => $to_acct['id']);
        }

        $fields = array(
            'txnid' => array(
                'name' => 'txnid',
                'type' => 'hidden',
                'value' => $txns[0]['txnid']
            ),
            'txntype' => array(
                'name' => 'txntype',
                'type' => 'hidden',
                'value' => 'split'
            ), 
            'txn_dt' => array(
                'name' => 'txn_dt',
                'type' => 'date',
                'value' => $txns[0]['txn_dt']
            ),
            'checkno' => array(
                'name' => 'checkno',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12,
                'value' => $txns[0]['checkno']
            ),
            'payee_id' => array(
                'name' => 'payee_id',
                'type' => 'select',
                'options' => $payee_options,
                'value' => $txns[0]['payee_id']
            ),
            'memo' => array(
                'name' => 'memo',
                'type' => 'text',
                'size' => 35,
                'maxlength' => 35,
                'value' => $txns[0]['memo']
            ),
            'to_acct' => array(
                'name' => 'to_acct',
                'type' => 'select',
                'options' => $to_options,
                'value' => $txns[0]['to_acct']
            ),
            'save' => array(
                'name' => 'save',
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

        $data = ['txns' => $txns, 'statuses' => $statuses, 'max_splits' => $max_splits, 'splits' => $splits];
        $this->page_title = 'Edit Split Transaction';
        $this->return = url('txn', 'update');
        $this->view('splitsedt.view.php', $data);

    }

    function update()
    {
        $txnid = $_POST['txnid'] ?? NULL;
        if (is_null($txnid)) {
            redirect('index.php');
        }
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
        $this->return = url('txn', 'vconfirm');
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
    }

}

