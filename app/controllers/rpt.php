<?php

class rpt extends controller
{
    public $cfg, $form, $nav, $db, $report;
    public $page_title, $return, $focus_field;

    function __construct()
    {
        global $cfg, $form, $nav, $db;
        $this->cfg = $cfg;
        $this->form = $form;
        $this->nav = $nav;
        $this->db = $db;
        $this->report = model('report', $this->db);
    }

    function balances()
    {
        $today = new xdate();

        $fields = [
            'last_dt' => [
                'name' => 'last_dt',
                'type' => 'date',
                'value' => $today->to_iso()
            ],
            's1' => [
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Compute'
            ]
        ];

        $this->form->set($fields);
        $this->page_title = 'List Balances';
        $this->focus_field = 'last_dt';
        $this->return = 'index.php?c=rpt&m=balshow';
        $this->view('balances.view.php');
    }

    function balshow()
    {
        $dt = new xdate();
        if (!empty($_POST['last_dt'])) {
            $dt->from_iso($_POST['last_dt']);
            $today = $dt->to_amer();
            $bals = $this->report->get_balances($_POST['last_dt']);
        }
        else {
            $today = $dt->to_amer();
            $bals = $this->report->get_balances();
        }

        if ($bals === FALSE) {
            emsg('F', 'Date is too early to show balances');
            $this->balances();
        }
        else {
            $nbals = count($bals);
        }

        $d = [
            'today' => $today,
            'nbals' => $nbals,
            'bals' => $bals
        ];

        $this->page_title = 'Balances';
        $this->view('balshow.view.php', $d);
    }

    function budget()
    {
        $accts = $this->report->get_budget_accounts();
        $cat_options = [];
        foreach ($accts as $acct) {
            if ($acct['acct_type'] == 'I') {
                $type = ' (income)';
            }
            elseif ($acct['acct_type'] == 'E') {
                $type = ' (expense)';
            }
            $cat_options[] = ['lbl' => $acct['name'] . $type, 'val' => $acct['id']];
        }	

        $fields = [
            'from' => [
                'name' => 'from',
                'type' => 'date',
                'label' => 'Start Date',
                'required' => 1
            ],
            'to' => [
                'name' => 'to',
                'type' => 'date',
                'label' => 'End Date',
                'required' => 1
            ],
            'category' => [
                'name' => 'category',
                'type' => 'select',
                'label' => 'Category',
                'required' => 1,
                'options' => $cat_options
            ],
            's1' => [
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Report'
            ]
        ];

        $this->form->set($fields);
        $this->page_title = 'Budget Query';
        $this->return = 'index.php?c=rpt&m=bgtshow';
        $this->view('budget.view.php');
    }

    function bgtshow()
    {
        fork('s1', 'P', 'index.php');
        $t = $this->report->budget($_POST['from'], $_POST['to'], $_POST['category']);
        $this->page_title = 'Budget Query Results';
        $this->view('bgtshow.view.php', ['txns' => $t[0], 'bal' => $t[1]]);
    }

    function expenses()
    {
        $dt = new xdate();
        $dt->endwk();
        $to_date = $dt->to_iso();

        $dt->add_days(-6);
        $from_date = $dt->to_iso();

        $fields = array(
            'from_date' => array(
                'name' => 'from_date',
                'type' => 'date',
                'value' => $from_date
            ),
            'to_date' => array(
                'name' => 'to_date',
                'type' => 'date',
                'value' => $to_date
            ),
            's1' => array(
                'name' => 's1',
                'type' => 'submit',
                'value' => 'Calculate'
            )
        );

        $this->form->set($fields);
        $this->page_title = 'Weekly Expenses';
        $this->focus_field = 'from_date';
        $this->return = 'index.php?c=rpt&m=expshow';
        $this->view('expenses.view.php');
    }

    function expshow()
    {
        $expenses = $this->report->get_expenses($_POST['from_date'], $_POST['to_date']);
        $this->page_title = 'Weekly Expenses';
        $this->view('expshow.view.php', ['expenses' => $expenses]);
    }
}
