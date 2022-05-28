<?php

class rpt extends controller
{
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
        $fields = [
            'last_dt' => [
                'name' => 'last_dt',
                'type' => 'date',
                'value' => pdate::now2iso()
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
        $this->return = url('rpt', 'balshow');
        $this->view('balances.view.php');
    }

    function balshow()
    {
        if (!empty($_POST['last_dt'])) {
            $today = $_POST['last_dt'];
            $bals = $this->report->get_balances($_POST['last_dt']);
        }
        else {
            $today = pdate::now2iso();
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
        $this->return = url('rpt', 'bgtshow');
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
        $temp_date = pdate::now();

        $oto_date = pdate::endwk($temp_date);
        $ato_date = pdate::get($oto_date, 'm/d/y');
        $ito_date = pdate::get($oto_date, 'Y-m-d');

        $ofrom_date = pdate::adddays($oto_date, -6);
        $afrom_date = pdate::get($ofrom_date, 'm/d/y');
        $ifrom_date = pdate::get($ofrom_date, 'Y-m-d');

        $fields = array(
            'from_date' => array(
                'name' => 'from_date',
                'type' => 'date',
                'value' => $ifrom_date
            ),
            'to_date' => array(
                'name' => 'to_date',
                'type' => 'date',
                'value' => $ito_date
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
        $this->return = url('rpt', 'expshow');
        $this->view('expenses.view.php');
    }

    function expshow()
    {
        $expenses = $this->report->get_expenses($_POST['from_date'], $_POST['to_date']);
        $this->page_title = 'Weekly Expenses';
        $this->view('expshow.view.php', ['expenses' => $expenses]);
    }
}
