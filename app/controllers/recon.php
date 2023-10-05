<?php

class recon extends controller
{
    public $cfg, $form, $nav, $db, $reconcile;
    public $page_title, $return, $focus_field;

    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
        $this->reconcile = model('reconcile', $this->db);
    }

    /**
     * Enter preliminary reconciliation parameters.
     */

    function prelim()
    {
        $accts = $this->reconcile->get_recon_accts();
        $payees = $this->reconcile->get_payees();

        $payee_options = [];
        if ($payees !== FALSE) {
            foreach ($payees as $payee) {
                $payee_options[] = ['lbl' => $payee['name'], 'val' => $payee['id']];
            }
        }

        $to_accts = $this->reconcile->get_to_accounts();
        $to_options = [];
        if ($to_accts !== FALSE) {
            foreach ($to_accts as $to_acct)
            {
                $to_options[] = ['lbl' => $to_acct['name'], 'val' => $to_acct['id']];
            }
        }

        $from_options = array();
        if ($accts !== FALSE) {
            foreach ($accts as $acct) {
                $from_options[] = array('lbl' => $acct['acct_type'] . '/' . $acct['name'],
                    'val' => $acct['id']);
            }
        }

        $fields = array(
            'from_acct' => array(
                'name' => 'from_acct',
                'type' => 'select',
                'required' => 1,
                'options' => $from_options
            ),
            'stmt_start_bal' => array(
                'name' => 'stmt_start_bal',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12
            ),
            'stmt_end_bal' => array(
                'name' => 'stmt_end_bal',
                'type' => 'text',
                'size' => 12,
                'maxlength' => 12
            ),
            'stmt_close_date' => array(
                'name' => 'stmt_close_date',
                'type' => 'date'
            ),
            'fee' => array(
                'name' => 'fee',
                'type' => 'text',
                'size' => 10,
                'maxlength' => 10
            ),
            'payee_id' => array(
                'name' => 'payee_id',
                'type' => 'select',
                'options' => $payee_options
            ),
            'to_acct' => array(
                'name' => 'to_acct',
                'type' => 'select',
                'options' => $to_options
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Continue'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Reconcile: Enter Preliminary Data';
        $this->return = 'index.php?c=recon&m=clear';
        $this->view('prerecon.view.php');
    }

    /**
     * Allow the user to mark transactions as cleared.
     */

    function clear()
    {
        // check for a reconciliation in progress (recon table)
        $saved = $this->reconcile->get_saved_work($_POST['from_acct']);
        if ($saved !== FALSE) {
            redirect('index.php?c=recon&m=continue&from_acct=' . $_POST['from_acct']);
            exit();
        }

        $acct = $this->reconcile->get_account($_POST['from_acct']);
        $errors = 0;

        if (!filled_out($_POST, ['stmt_start_bal', 'stmt_end_bal'])) {
            // user failed to provide either of the stmt balances we asked for
            $errors++;
            emsg('F', 'Beginning and/or ending balance omitted');
        } 

        if (empty($_POST['stmt_close_date'])) {
            // user omitted a statement close date
            $errors++;
            emsg('F', 'No closing date provided');
        }

        if ($acct['rec_bal'] != dec2int($_POST['stmt_start_bal'])) {
            // starting balances don't match
            $errors++;
            emsg('F', "Statement and computer starting balances don't match.");
        }

        if ($errors) {
            $this->prelim();
            exit();
        }

        if (!empty($_POST['fee'])) {
            $this->reconcile->add_statement_fee($_POST['from_acct'], $_POST['payee_id'], $_POST['to_acct'], $_POST['fee'], $_POST['stmt_close_date']);
        }

        $acct = $this->reconcile->get_account($_POST['from_acct']);
        $from_acct = $acct['id'];
        $from_acct_name = $acct['name'];

        $stmt_start_bal = $_POST['stmt_start_bal'];
        $stmt_end_bal = $_POST['stmt_end_bal'];
        $stmt_close_date = $_POST['stmt_close_date'];
        $txns = $this->reconcile->get_uncleared_transactions($_POST['from_acct']);

        // hidden fields...
        $fields = array(
            'from_acct' => array(
                'name' => 'from_acct',
                'type' => 'hidden',
                'value' => $from_acct
            ),
            'stmt_start_bal' => array(
                'name' => 'stmt_start_bal',
                'type' => 'hidden',
                'value' => $stmt_start_bal
            ),
            'stmt_end_bal' => array(
                'name' => 'stmt_end_bal',
                'type' => 'hidden',
                'value' => $stmt_end_bal
            ),
            'stmt_close_date' => array(
                'name' => 'stmt_close_date',
                'type' => 'hidden',
                'value' => $stmt_close_date
            ),
            'from_acct_name' => array(
                'name' => 'from_acct_name',
                'type' => 'hidden',
                'value' => $from_acct_name
            ),
            's3' => array(
                'name' => 's3',
                'type' => 'submit',
                'value' => 'Continue'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Reconcile: Clear Transactions';
        $d = ['txns' => $txns, 'from_acct_name' => $from_acct_name];
        $this->return = 'index.php?c=recon&m=finish';
        $this->view('reconlist.view.php', $d);
    }

    /**
     * Ask the user to continue paused reconciliation.
     */

    function continue()
    {
        $from_acct = $_GET['from_acct'] ?? NULL;
        if (is_null($from_acct)) {
            $this->prelim();
            exit();
        }

        $acct = $this->reconcile->get_account($from_acct);

        $fields = [
            'from_acct' => [
                'name' => 'from_acct',
                'type' => 'hidden',
                'value' => $from_acct
            ],
            'continue' => [
                'name' => 'continue',
                'type' => 'checkbox',
                'value' => 1
            ],
            's1' => [
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Continue'
            ]
        ];
        $this->form->set($fields);
        $this->page_title = 'Continue Reconciliation';
        $this->return = 'index.php?c=recon&m=reclear';
        $this->view('reconcont.view.php', ['name' => $acct['name']]);

    }

    /**
     * Set up paused then continued reconciliation.
     */

    function reclear()
    {
        $continue = $_POST['continue'] ?? 0;

        if ($continue == 0) {
            $this->reconcile->clear_saved_work($_POST['from_acct']);
            $this->prelim();
            exit();
        }

        $acct = $this->reconcile->get_account($_POST['from_acct']);

        $saved = $this->reconcile->get_saved_work($_POST['from_acct']);
        // clear saved work; we're now in the middle of reconciliation again
        $this->reconcile->clear_saved_work($_POST['from_acct']);

        $saved['stmt_start_bal'] = int2dec($saved['stmt_start_bal']);
        $saved['stmt_end_bal'] = int2dec($saved['stmt_end_bal']);

        $from_acct = $acct['id'];
        $from_acct_name = $acct['name'];

        $stmt_start_bal = $saved['stmt_start_bal'];
        $stmt_end_bal = $saved['stmt_end_bal'];
        $stmt_close_date = $saved['stmt_close_date'];

        $txns = $this->reconcile->get_uncleared_transactions($_POST['from_acct']);

        // hidden fields...
        $fields = array(
            'from_acct' => array(
                'name' => 'from_acct',
                'type' => 'hidden',
                'value' => $from_acct
            ),
            'stmt_start_bal' => array(
                'name' => 'stmt_start_bal',
                'type' => 'hidden',
                'value' => $stmt_start_bal
            ),
            'stmt_end_bal' => array(
                'name' => 'stmt_end_bal',
                'type' => 'hidden',
                'value' => $stmt_end_bal
            ),
            'stmt_close_date' => array(
                'name' => 'stmt_close_date',
                'type' => 'hidden',
                'value' => $stmt_close_date
            ),
            'from_acct_name' => array(
                'name' => 'from_acct_name',
                'type' => 'hidden',
                'value' => $from_acct_name
            ),
            's3' => array(
                'name' => 's3',
                'type' => 'submit',
                'value' => 'Continue'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Continue Reconciliation';
        $this->return = 'index.php?c=recon&m=finish';
        $this->view('reconlist.view.php', ['txns' => $txns, 'from_acct_name' => $from_acct_name]);
    }

    /**
     * Update the database with cleared transactions.
     */

    function finish()
    {
        if (count($_POST) == 0)
            redirect('index.php?c=recon&m=prelim');

        if (!empty($_POST['status'])) {
            $cleared_list = implode(', ', $_POST['status']);
            $data = $this->reconcile->check_reconciliation($_POST['from_acct'], $_POST['stmt_start_bal'], 
                $_POST['stmt_end_bal'], $cleared_list);
        }
        else {
            // no transactions marked as cleared
            $data = FALSE;
        }

        if ($data === TRUE) {
            // everything balances
            $this->reconcile->finish_reconciliation($_POST['from_acct'], $_POST['stmt_end_bal'], $_POST['stmt_close_date'], $cleared_list);
            emsg('S', "Reconciliation passes checks. Congratulations.");
            $this->prelim();
            exit();
        }
        elseif ($data === FALSE) {
            // no transactions marked as cleared
            // however, this can happen when someone revisits the reconciliation
            // and "unclears" transactions already marked as cleared.
            emsg('F', 'No transactions marked for clearing. Aborted.');
            $this->reconcile->unclear_all($_POST['from_acct']);
            $this->prelim();
            exit();
        }
        else {
            // reconciliation failed
            $this->reconcile->save_work($cleared_list, $_POST['from_acct'], $_POST['stmt_start_bal'], $_POST['stmt_end_bal'], $_POST['stmt_close_date']);
            emsg('F', "Statement and computer final balances don't match.");
            $this->page_title = 'Reconciliation Failed';
            $this->view('reconfailed.view.php', ['data' => $data]);
        }
    }
}
