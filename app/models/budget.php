<?php

/**
 * @copyright 2014, Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 */

/**
 * Budgeting (as I use it) is a matter of, for every expense, having an
 * amount set aside for that thing (like 3 weeks' worth of rent money),
 * adding a week's worth of set aside money for it, and then subtracting
 * whatever you paid that week. This gives you the amount you currently
 * owe.
 *
 * Example: Rent, date 08/08/20
 * Set aside as of last week:   $600.00
 * Added set aside this week: + $200.00
 * Paid this week:            - $800.00
 * Total now set aside:            0.00
 *
 * But there is a problem with doing week-to-week budgets like this. The
 * current set aside amount must become the prior set aside when you do
 * next week's budget. So if you have columns like this:
 *
 * | Prior S/A | Add'l S/A | Paid | New S/A |
 *
 * The "New S/A" column must be copied to the "Prior S/A" column when
 * you start your new budget. Spreadsheets can't really do this easily.
 * Hence this program.
 *
 * This model uses three tables:
 *
 * blines-- metadata about the individual accounts
 * history-- all budgets going back forever
 * cells-- the current completed budget
 * staging-- the budget you're working on
 *
 * When you start a new budget, the system checks to see if you already
 * have a budget in progress (the staging table). If so, it pulls the
 * figures from there. If there's nothing in the staging table, it pulls
 * last week's figures from the cells table. Data entry and
 * recalculations all occur in memory. When you hit "Save", the budget
 * is saved to the staging table. That way, if you have to go off and do
 * something else, it is saved. The next time you resume your budget
 * work, it will pull the figures from the staging table. When you're
 * done, three things happen:
 *
 * 1. The figures are pulled from the staging table.
 * 2. The staging table is blanked.
 * 3. The staging figures are copied to the cells table.
 * 4. The staging figures are copied to the history table.
 *
 * At any moment, history and cells contain the current completed
 * budget. History also contains all prior budgets. Staging will contain
 * your in-progress figures, if you're working on a budget.
 */

class budget
{
    public $db;

    function __construct($database_object)
    {
	$this->db = $database_object;
    }

    /**
     * Generate figures for totals row.
     *
     * @param array The cells array
     * @return array The totals array
     */

    function get_totals($recs)
    {
	$totals = [
	    'wklysa' => 0,
	    'priorsa' => 0,
	    'addlsa' => 0,
	    'paid' => 0,
	    'newsa' => 0
	];

	foreach ($recs as $rec) {
	    $totals['wklysa'] += $rec['wklysa'];
	    $totals['priorsa'] += $rec['priorsa'];
	    $totals['addlsa'] += $rec['addlsa'];
	    $totals['paid'] += $rec['paid'];
	    $totals['newsa'] += $rec['newsa'];
	}

	return $totals;
    }

	/**
     * Determine whether a combination of period, typdue and newsa is
     * over-budget.
     *
     * Examine period, typdue and newsa to see if they're over budget. If
     * so, return TRUE. Else FALSE.
     * 
     * @param char period
     * @param int typdue
     * @param int newsa
     * @return boolean TRUE if over, FALSE if not
	 */

    function red($period, $typdue, $newsa)
    {
	switch ($period) {
	    case 'M':
	    case 'Y':
	    case 'S':
		$over_budget = ($typdue != 0 && $newsa >= $typdue) ? TRUE : FALSE;
		break;
	    default:
		$over_budget = FALSE;
		break;
	}
	return $over_budget;
    }

    function get_staging()
    {
	// avoid fetching the id field
	// $sql = "SELECT acctname, acctnum, accttype, wedate, typdue, period, wklysa, priorsa, addlsa, paid, newsa FROM staging ORDER BY acctname";
	$sql = "SELECT b.acctname, b.from_acct, b.payee_id, b.to_acct,
	b.period, b.typdue, c.acctnum, c.wedate, c.wklysa, c.priorsa, c.addlsa,
	c.paid, c.newsa FROM staging AS c JOIN blines AS b ON b.id = c.acctnum";
	$results = $this->db->query($sql)->fetch_all();
	if ($results !== FALSE) {
	    // set up "red" field
	    $nresults = count($results);
	    for ($i = 0; $i < $nresults; $i++) {
		$results[$i]['red'] = $this->red($results[$i]['period'], $results[$i]['typdue'], $results[$i]['newsa']);
	    }
	}
	return $results;
    }

    function get_cells()
    {
	// avoid fetching the id field
	// $sql = "SELECT acctname, acctnum, accttype, wedate, typdue, period, wklysa, priorsa, addlsa, paid, newsa  FROM cells ORDER BY acctname";
	$sql = "SELECT b.acctname, b.from_acct, b.payee_id, b.to_acct,
	b.period, b.typdue, c.acctnum, c.wedate, c.wklysa, c.priorsa, c.addlsa,
	c.paid, c.newsa FROM cells AS c JOIN blines AS b ON b.id = c.acctnum ORDER BY b.acctname";
	$results = $this->db->query($sql)->fetch_all();

	if ($results != FALSE) {
	    // set up "red" field
	    $nresults = count($results);
	    for ($i = 0; $i < $nresults; $i++) {
		$results[$i]['red'] = $this->red($results[$i]['period'], $results[$i]['typdue'], $results[$i]['newsa']);
	    }
	}

	return $results;
    }

    function put_staging($recs)
    {
	$this->db->delete('staging');
	foreach ($recs as $rec) {
	    // this is done to remove the "red" field, etc.
	    $trec = $this->db->prepare('staging', $rec);
	    $this->db->insert('staging', $trec);
	}
    }

    /**
     * Recalculate the wklysa, newsa columns, and set "red" attribute as
     * needed.
     *
     * @param array The cells array
     * @return array The changed cells array
     */

    function recalculate($cells)
    {
	$periods = [
	    'W' => 1,
	    'M' => 4,
	    'Q' => 13,
	    'S' => 26,
	    'Y' => 52
	];

	$max = count($cells);
	for ($i = 0; $i < $max; $i++) {
	    $cells[$i]['wklysa'] = floor($cells[$i]['typdue'] / $periods[$cells[$i]['period']]);
	    $cells[$i]['newsa'] = $cells[$i]['priorsa'] + $cells[$i]['addlsa'] - $cells[$i]['paid'];
	    $cells[$i]['red'] = $this->red($cells[$i]['period'], $cells[$i]['typdue'], $cells[$i]['newsa']);
	}

	return $cells;
    }

    private function swap($cells)
    {
	$max = count($cells);
	for ($i = 0; $i < $max; $i++) {
	    $cells[$i]['priorsa'] = $cells[$i]['newsa'];
	}

	return $cells;
    }

    private function zero_payments($cells)
    {
        $max = count($cells);
        for ($i = 0; $i < $max; $i++) {
            $cells[$i]['paid'] = 0;
        }
        return $cells;
    }

    function get_expenses($cells)
    {
        global $cfg;

        $max = count($cells);

        $to_date = $cells[0]['wedate'];
        $from = new xdate;
        $from->from_iso($to_date);
        $from->add_days(-6);
        $from_date = $from->to_iso();

        $sql = "SELECT journal.* FROM journal JOIN accounts ON accounts.id
            = journal.to_acct WHERE accounts.acct_type = 'E' AND
            journal.txn_dt >= '$from_date' AND journal.txn_dt <= '$to_date'
            AND journal.status != 'V'";

        $expenses = $this->db->query($sql)->fetch_all();

        if ($expenses) {
            foreach ($expenses as $exp) {
                for ($i = 0; $i < $max; $i++) {

                    // NOTE: "from" account matches are done first. These
                    // are typically credit cards. If we get a match on a
                    // credit card (the credit card paid something else), we
                    // must add addlsa. After "from" testing, "to" and "payee"
                    // matching is done. For matches, we add to the "paid" for
                    // that account.

                    if ($cells[$i]['from_acct'] == $exp['from_acct']) {
                        if ($cells[$i]['payee_id'] == 0) {
                            $cells[$i]['addlsa'] -= $exp['amount'];
                        }
                        elseif ($cells[$i]['payee_id'] == $exp['payee_id']) {
                            $cells[$i]['addlsa'] -= $exp['amount'];
                        }
                    }

                    if ($cells[$i]['to_acct'] == 0) {
                        if ($cells[$i]['payee_id'] == $exp['payee_id']) {
                            $cells[$i]['paid'] -= $exp['amount'];
                        }
                    }
                    elseif ($cells[$i]['to_acct'] == $exp['to_acct']) {
                        if ($cells[$i]['payee_id'] == 0) {
                            $cells[$i]['paid'] -= $exp['amount'];
                        }
                        elseif ($cells[$i]['payee_id'] == $exp['payee_id']) {
                            $cells[$i]['paid'] -= $exp['amount'];
                        }
                    }
                }
            }
        }

        return $cells;
    }

    /**
     * Fetch and apply payments to accounts.
     *
     * This principally applies to credit card accounts. These are
     * typically paid out of another account (usually checking) and the
     * transactions are in two halves. The first is a debit of the checking
     * or other account. This will show a from_acct of the checking
     * account. The second half is a credit (positive amount) where the
     * from account is the credit card account. Where the budget tracks
     * credit cards, they should be set up with a from_acct set to the
     * account number of the credit card account. The other side (debit on
     * a credt card) gets handled by the get_expenses() method. These will
     * be debits (negative amounts) with the from account as the credit
     * card account.
     *
     * @param array The $cells array
     * @param array The $cells array changed by payments
     */

    function get_payments($cells)
    {
        global $cfg;

        $max = count($cells);

        $to_date = $cells[0]['wedate'];
        $from = new xdate;
        $from->from_iso($to_date);
        $from->add_days(-6);
        $from_date = $from->to_iso();

        $sql = "select journal.* from journal, accounts 
            where txn_dt >= '$from_date' 
            and txn_dt <= '$to_date' 
            and amount > 0 
            and journal.from_acct = accounts.id 
            and accounts.acct_type = 'R'
            and status != 'V'";

        $payments = $this->db->query($sql)->fetch_all();

        if ($payments) {
            foreach ($payments as $pmt) {
                for ($i = 0; $i < $max; $i++) {
                    if ($cells[$i]['from_acct'] == $pmt['from_acct']) {
                        $cells[$i]['paid'] += $pmt['amount'];
                    }
                }
            }
        }

        return $cells;
    }

    function update_addlsa($cells)
    {
        $max = count($cells);
        for ($i = 0; $i < $max; $i++) {
            $cells[$i]['addlsa'] = $cells[$i]['wklysa'];
        }
        return $cells;
    }

    function start()
    {
	$cells = $this->get_staging();

	if ($cells !== FALSE) {

	    $totals = $this->get_totals($cells);
	    $wedate = $cells[0]['wedate'];
	    $to = $wedate;

	    $todate = new xdate;
	    $todate->from_iso($to);
	    $hr_wedate = $todate->to_amer();

	    emsg('S', 'Budget has been RESUMED');
	}
	else {

	    // grab data
	    $cells = $this->get_cells();

	    // get from date
	    $xfrom = new xdate;
	    $xfrom->from_iso($cells[0]['wedate']);
	    $xfrom->add_days(1);
	    $from = $xfrom->to_iso();

	    // advance the date
	    $xto = new xdate;
	    $xto = $xfrom;
	    $xto->add_days(6);
	    $to = $xto->to_iso();

	    $hr_wedate = $xto->to_amer();

	    // update wedates
	    $max = count($cells);
	    for ($i = 0; $i < $max; $i++) {
		$cells[$i]['wedate'] = $to;
	    }

	    $cells = $this->swap($cells);
	    $cells = $this->zero_payments($cells);
	    $cells = $this->update_addlsa($cells);

	    $cells = $this->get_expenses($cells);
	    $cells = $this->get_payments($cells);

	    $cells = $this->recalculate($cells);
	    $totals = $this->get_totals($cells);

	    $this->put_staging($cells);
	}

	return [$to, $hr_wedate, $cells, $totals];
    }

    function restart()
    {
	$this->db->delete('staging');
	return $this->start();
    }

    function to_staging($cells)
    {
	$this->db->begin();

	$this->db->delete('staging');
	foreach ($cells as $cell) {
	    $cell = $this->db->prepare('staging', $cell);
	    $this->db->insert('staging', $cell);
	}

	$this->db->commit();

    }

    function post2cells($post)
    {
	$cells = [];
	$max = count($post['acctname']);

	for ($i = 0; $i < $max; $i++) {
	    $cells[$i]['from_acct'] = (int) $post['from_acct'][$i];
	    $cells[$i]['payee_id'] = (int) $post['payee_id'][$i];
	    $cells[$i]['to_acct'] = (int) $post['to_acct'][$i];
	    $cells[$i]['period'] = $post['period'][$i];
	    $cells[$i]['typdue'] = $post['typdue'][$i];
	    $cells[$i]['acctname'] = $post['acctname'][$i];
	    $cells[$i]['acctnum'] = $post['acctnum'][$i];
	    $cells[$i]['wedate'] = $post['wedate'];
	    $cells[$i]['wklysa'] = dec2int($post['wklysa'][$i]);
	    $cells[$i]['priorsa'] = dec2int($post['priorsa'][$i]);
	    $cells[$i]['addlsa'] = dec2int($post['addlsa'][$i]);
	    $cells[$i]['paid'] = dec2int($post['paid'][$i]);
	    $cells[$i]['newsa'] = dec2int($post['newsa'][$i]);
	}

	return $cells;
    }

    function print()
    {
        global $cfg;

        $cells = $this->get_cells();
        $totals = $this->get_totals($cells);
        $wedate = $cells[0]['wedate'];
        $xwedate = new xdate;
        $xwedate->from_iso($wedate);

        $p = load('pdf_report');
        $p->add_page();
        $p->set_margins(5, 0, 0);
        // $p->print_line($cfg['app_name'] . ' for week ending ' . pdate::iso2am($wedate));
        $top = 'Budget for week ending ' . $xwedate->to_amer();
        $p->center($top);
        $p->skip_line();

        $line = '      Acct Name     |  Typ   |P|  Wkly | Prior | Addl  | Paid  |  New';
        $p->print_line($line);
        $line = '-------------------- -------- - ------- ------- ------- ------- -------'; 
        $p->print_line($line);

        foreach ($cells as $cell) {
            $line = sprintf('%-20s %8.2f %s %7.2f %7.2f %7.2f %7.2f %7.2f', $cell['acctname'], int2dec($cell['typdue']), $cell['period'], int2dec($cell['wklysa']), int2dec($cell['priorsa']), int2dec($cell['addlsa']), int2dec($cell['paid']), int2dec($cell['newsa']));
            $p->print_line($line, TRUE);
        }

        $line = '-------------------- -------- - ------- ------- ------- ------- -------'; 
        $p->print_line($line, TRUE);

        $line = sprintf('TOTALS:                         %7.2f %7.2f %7.2f %7.2f %7.2f', int2dec($totals['wklysa']), int2dec($totals['priorsa']), int2dec($totals['addlsa']), int2dec($totals['paid']), int2dec($totals['newsa']));
        $p->print_line($line, TRUE);

	$p->print_line('', TRUE);
	$p->print_line('', TRUE);

	$total = 0;
	$bals = $this->get_balances($wedate);
	foreach ($bals as $bal) {
	    if ($bal['amount'] != 0) {
		$line = sprintf("%35s %8.2f", $bal['name'], int2dec($bal['amount']));
		$p->print_line($line, TRUE);
		$total += $bal['amount'];
	    }
	}

	$line = '                                   ---------';
	$p->print_line($line, TRUE);
	$line = '                                    ' . sprintf("%8.2f", int2dec($total));
	$p->print_line($line, TRUE);

	$line = '                          Setasides ' . sprintf("%8.2f", int2dec($totals['newsa']));
	$p->print_line($line, TRUE);
	$cashbills = $total - $totals['newsa'];
	$line = '                         CASH/BILLS ' . sprintf("%8.2f", int2dec($cashbills));
	$p->print_line($line, TRUE);

        $p->output(PRINTDIR . 'budget.pdf');
        emsg('S', 'Print budget PDF <a href="' . PRINTDIR . 'budget.pdf">HERE</a>');
    }

    /**
     * Save post to cells to staging.
     *
     * @param array POST array
     * @return array cells
     */

    function save($post)
    {
	$cells = $this->post2cells($post);
	$cells = $this->recalculate($cells);	
	$this->to_staging($cells);
	return $cells;
    }

    function complete($post)
    {
	$cells = $this->get_staging();
	$this->db->begin();

	$this->db->delete('staging');
	$this->db->delete('cells');
	foreach ($cells as $cell) {
	    // remove "red" field and others belonging to blines
	    $ccell = $this->db->prepare('cells', $cell);
	    $this->db->insert('cells', $ccell);
	    $hcell = $this->db->prepare('history', $cell);
	    $this->db->insert('history', $hcell);
	}

	$this->db->commit();

	$this->print();
    }

    function get_accounts()
    {
	$sql = "SELECT * FROM blines ORDER BY acctname";
	$result = $this->db->query($sql)->fetch_all();
	return $result;
    }

    function get_account($id)
    {
        $sql = "SELECT id, acctname, acctnum FROM blines WHERE id = $id";
        $acct = $this->db->query($sql)->fetch();
        return $acct;
    }

    function get_account_extended($id)
    {
        $sql = "SELECT * FROM blines WHERE id = $id";
        $acct = $this->db->query($sql)->fetch();
        return $acct;
    }
	
    /**
     * get_latest_wedate()
     *
     * Get latest weekending date for existing data (date object)
     *
     * @return date $wedate (date object)
     *
     */

    function get_latest_wedate()
    {
	$sql = "SELECT wedate FROM cells LIMIT 1";
	$e = $this->db->query($sql)->fetch();
	return $e['wedate'];
    }

    /**
	 * add_account()
	 *
	 * Adds an account to the latest records in the values table
	 *
	 * @param array Typically the POST array
	 *
	 * @return boolean FALSE if no acctname, accttype, or period are
	 * supplied. Otherwise TRUE
	 *
	 */

    function add_account($post)
    {
	$rec = [
	    'acctname' => $post['acctname'],
	    'period' => $post['period'],
	    'typdue' => (!isset($post['typdue']) || empty($post['typdue'])) ? 0 : dec2int($post['typdue']),
	    'from_acct' => $post['from_acct'] ?? 0,
	    'to_acct' => $post['to_acct'] ?? 0,
	    'payee_id' => $post['payee_id'] ?? 0,
	    'priorsa' => (!isset($post['priorsa']) || empty($post['priorsa'])) ? 0 : dec2int($post['priorsa'])
	];

	$bline = $this->db->prepare('blines', $rec);
	$this->db->insert('blines', $bline);
	$acctnum = $this->db->lastid('blines');

	$periods = [
	    'W' => 1,
	    'M' => 4,
	    'Q' => 13,
	    'S' => 26,
	    'Y' => 52
	];

	$rec['acctnum'] = $acctnum;
	$rec['wedate'] = $this->get_latest_wedate();
	// using floor() here may mean the user as to adjust addlsa
	// constantly
	$rec['wklysa'] = ceil($rec['typdue'] / $periods[$rec['period']]);
	$rec['addlsa'] = 0;
	$rec['paid'] = 0;
	$rec['newsa'] = $rec['priorsa'] + $rec['addlsa'] - $rec['paid'];

	$trec = $this->db->prepare('cells', $rec);
	$this->db->insert('cells', $trec);

	return TRUE;
    }

    function update_account($post)
    {
        $d = [
            'acctname' => $post['acctname'], 
            'period' => $post['period'],
            'typdue' => dec2int($post['typdue']),
            'from_acct' => $post['from_acct'] ?? 0,
            'to_acct' => $post['to_acct'] ?? 0,
            'payee_id' => $post['payee_id'] ?? 0
        ];

        $this->db->update('blines', $d, "id = {$post['id']}");
        return TRUE;
    }

    function delete_account($post)
    {
	$this->db->delete('cells', "acctnum = {$post['id']}");
	$this->db->delete('blines', "id = {$post['id']}");
	return TRUE;
    }

    function get_from_accounts()
    {
	$sql = "SELECT id, name, acct_type FROM accounts WHERE acct_type IN ('C', 'R', 'S', 'L', 'Q') AND parent != 0 ORDER BY lower(name)";
	$from_accts = $this->db->query($sql)->fetch_all();
	array_unshift($from_accts, ['id' => 0, 'name' => 'NONE', 'acct_type' => ' ']);
	return $from_accts;
    }

    function get_to_accounts()
    {
	$sql = "SELECT id, name, acct_type FROM accounts ORDER BY lower(name)";
	$to_accts = $this->db->query($sql)->fetch_all();
	array_unshift($to_accts, ['id' => 0, 'name' => 'NONE', 'acct_type' => ' ']);
	return $to_accts;
    }

    function get_payees()
    {
	$sql = "SELECT * FROM payees ORDER BY lower(name)";
	$payees = $this->db->query($sql)->fetch_all();
	array_unshift($payees, ['id' => 0, 'name' => 'NONE']);
	return $payees;
    }

    function version()
    {
	return 6.5;
    }

    function get_balances($isodt)
    {
	$sql = "SELECT id, name, rec_bal FROM accounts WHERE acct_type in ('C', 'S') ORDER BY name";
	$accts = $this->db->query($sql)->fetch_all();

	foreach ($accts as $acct) {
	    $sql = "SELECT sum(amount) AS amount FROM journal WHERE (status = ' ' OR status = 'C') AND txn_dt <= '$isodt' AND from_acct = {$acct['id']}";
	    $amount = $this->db->query($sql)->fetch();

	    $recs[] = [
		'id' => $acct['id'],
		'name' => $acct['name'],
		'amount' => $acct['rec_bal'] + $amount['amount']
	    ];
	}

	return $recs;
    }
};

