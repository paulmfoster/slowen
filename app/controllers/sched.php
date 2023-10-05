<?php

class sched extends controller
{
    public $cfg, $form, $nav, $db, $sched, $trans, $accounts;
    public $page_title, $return, $focus_field;
    public $from_options, $to_options, $payee_options;

    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
        $this->sched = model('scheduled', $this->db);
    }

    function add()
    {
        global $atnames;

        $this->trans = model('addtxn', $this->db);

        $payees = $this->trans->get_payees();
        $this->accounts = $this->trans->get_all_accounts();
        $from_accts = $this->accounts['from'];
        $to_accts = $this->accounts['to'];
        
        if ($payees == FALSE || $from_accts == FALSE || $to_accts == FALSE) {
            emsg('F', 'Payees and/or accounts missing.');
            redirect('index.php');
        }

        $this->from_options = array();
        foreach($from_accts as $from_acct) {
            $this->from_options[] = array('lbl' => 
                $from_acct['name'] . ' ' . $atnames[$from_acct['acct_type']], 
                'val' => $from_acct['id']);
        }

        $this->payee_options = array();
        $this->payee_options[] = array('lbl' => 'NONE', 'val' => 0);
        foreach($payees as $payee) {
            $this->payee_options[] = array('lbl' => $payee['name'], 'val' => $payee['id']);
        }

        $this->to_options = array();
        foreach($to_accts as $to_acct) {
            $this->to_options[] = array('lbl' => $to_acct['name'] . ' ' . $atnames[$to_acct['acct_type']], 
                'val' => $to_acct['id']);
        }

        $period_options = [];
        $period_options[] = ['lbl' => 'Day', 'val' => 'D'];
        $period_options[] = ['lbl' => 'Week', 'val' => 'W'];
        $period_options[] = ['lbl' => 'Month', 'val' => 'M'];
        $period_options[] = ['lbl' => 'Quarter', 'val' => 'Q'];
        $period_options[] = ['lbl' => 'Year', 'val' => 'Y'];

        $dom_options = [];
        for ($i = 1; $i < 31; $i++) {
            $dom_options[] = ['lbl' => $i, 'val' => $i];
        }

        $dt = new xdate();

        $fields = array(
            'from_acct' => array(
                'name' => 'from_acct',
                'type' => 'select',
                'options' => $this->from_options
            ),
            'freq' => array(
                'name' => 'freq',
                'type' => 'text',
                'size' => 3,
                'maxlength' => 3
            ),
            'period' => array(
                'name' => 'period',
                'type' => 'select',
                'options' => $period_options
            ),
            'last' => array(
                'name' => 'last',
                'type' => 'date',
                'value' => $dt->to_iso()
            ), 
            'xfer' => array(
                'name' => 'xfer',
                'type' => 'checkbox',
                'value' => 1
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
        $this->focus_field = 'from_acct';
        $this->page_title = 'Add Scheduled Transaction';
        $this->return = 'index.php?c=sched&m=save';
        $this->view('schadd.view.php');
    }

    function save()
    {
        $s1 = fork('s1', 'P', 'index.php?c=sched&m=add');
        $status = $this->sched->add_scheduled($_POST);
        if ($status) {
            emsg('S', "Scheduled transaction added.");
        }
        redirect('index.php?c=sched&m=add');
    }

    function list()
    {
        $list = $this->sched->fetch_scheduled();

        $this->page_title = 'Scheduled Transactions List';
        $this->view('schlist.view.php', ['list' => $list]);
    }

    function delete()
    {
        $r = $this->sched->fetch_scheduled();
        $this->page_title = 'Delete Scheduled Transactions';
        $this->return = 'index.php?c=sched&m=dconfirm';
        $this->view('schdel.view.php', ['r' => $r]);
    }

    function dconfirm()
    {
        fork('s1', 'P', 'index.php?c=sched&m=delete');

        $status = $this->sched->delete_scheduled($_POST);
        if ($status) {
            emsg('S', 'Scheduled transactions deleted.');
        }

        redirect('index.php?c=sched&m=list');
    }

    function activate()
    {
        $r = $this->sched->fetch_scheduled();
        $this->page_title = 'Activate Scheduled Transactions';
        $this->return = 'index.php?c=sched&m=aconfirm';
        $this->view('schact.view.php', ['r' => $r]);
    }

    function aconfirm()
    {
        fork('s1', 'P', 'index.php?c=sched&m=activate');

        $howmany = $this->sched->activate_scheduled($_POST);
        if ($howmany) {
            emsg('S', 'Scheduled transactions activated.');
        }
        else {
            emsg('F', 'No transactions were scheduled for activation.');
        }

        redirect('index.php?c=sched&m=list');
    }

}
