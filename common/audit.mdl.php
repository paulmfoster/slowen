<?php

class audit
{
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * audit_bals()
	 *
	 * Obtain the balance for each "bank" account
	 *
	 * @param date $iso_dt The date ending the period
	 *
	 * @return array Array of balance records, ordered by acct
	 *
	 */

	function audit_bals($iso_dt)
	{
		$accts = $this->get_accounts();
		$naccts = count($accts);

		$sql = "SELECT DISTINCT from_acct, sum(amount) as total FROM journal WHERE txn_dt <= '$iso_dt' GROUP BY from_acct";
		$txns = $this->db->query($sql)->fetch_all();
		$ntxns = count($txns);

		$bal = 0;
		for ($i = 0; $i < $naccts; $i++) {
			$bal = $accts[$i]['open_bal'];
			for ($j = 0; $j < $ntxns; $j++) {
				if ($txns[$j]['from_acct'] == $accts[$i]['acct_id']) {
					$bal += $txns[$j]['total'];
					break;
				}
			}

			$bals[] = array('name' => $accts[$i]['name'], 'balance' => $bal, 'acct_id' => $accts[$i]['acct_id'], 'acct_type' => $accts[$i]['acct_type']);
			$bal = 0;
		}
		return $bals;
	}

	/**
	 * audit_cats()
	 *
	 * Meant to pick up income or expense totals for a given month
	 *
	 * @param date $iso_from_date Date to start
	 * @param date $iso_to_date Date to end
	 * @param char $inc_exp I for income, E for expense
	 *
	 * @return array Totals for each acct within time frame
	 *
	 */

	function audit_cats($iso_from_date, $iso_to_date, $inc_exp)
	{
		// get totals from journal for each category

		$sql = "select distinct to_acct as cat_no, accounts.name as cat_name, sum(amount) as amount from journal, accounts where journal.to_acct = accounts.acct_id and accounts.acct_type = '$inc_exp' and txn_dt >= '$iso_from_date' and txn_dt <= '$iso_to_date' group by to_acct order by cat_name";
		$r = $this->db->query($sql)->fetch_all();
		$rmax = count($r);

		// get totals from splits for each category

		$sql = "select splits.to_acct as cat_no, sum(splits.amount) as amount, accounts.name as cat_name from splits, journal, accounts where journal.txnid = splits.txnid and accounts.acct_id = splits.to_acct and accounts.acct_type = '$inc_exp' and journal.txn_dt >= '$iso_from_date' and journal.txn_dt <= '$iso_to_date' group by splits.to_acct order by splits.to_acct";
		$s = $this->db->query($sql)->fetch_all();
		if ($s === FALSE) {
			// if we get to here, there were no splits
			$smax = 0;
		}
		else {
			$smax = count($s);
		}

		// create an array of category numbers from the journal table

		$keys = array();
		for ($a = 0; $a < $rmax; $a++) {
			$keys[] = $r[$a]['cat_no'];
		}

		//  add category numbers from the splits table
	
		for ($b = 0; $b < $smax; $b++) {
			$keys[] = $s[$b]['cat_no'];
		}

		// sort keys

		sort($keys);

		// eliminate duplicates

		$cat_nos = array_unique($keys);

		// the last step creates "holes" in the index/key sequence
		// so we remap the array

		$cat_nums = array();
		foreach ($cat_nos as $cat_no) {
			$cat_nums[] = $cat_no;
		}

		// create the full array, using the prior array as a guide
		// use the main and splits arrays

		$cats = array();
		$max_cats = count($cat_nums);
		for ($d = 0; $d < $max_cats; $d++) {
			$cats[$d] = array(
				'cat_no' => $cat_nums[$d],
				'cat_name' => '',
				'amount' => 0
			);
			for ($e = 0; $e < $rmax; $e++) {
				if ($r[$e]['cat_no'] == $cats[$d]['cat_no']) {
					$cats[$d]['cat_name'] = $r[$e]['cat_name'];
					$cats[$d]['amount'] += $r[$e]['amount'];
					break;
				}
			}
			for ($f = 0; $f < $smax; $f++) {
				if ($s[$f]['cat_no'] == $cats[$d]['cat_no']) {
					if ($cats[$d]['cat_name'] == '') {
						$cats[$d]['cat_name'] = $s[$f]['cat_name'];
					}
					$cats[$d]['amount'] += $s[$f]['amount'];
					break;
				}
			}
		}

		return $cats;
	}

	/**
	 * audit
	 *
	 * @param integer $year The year of the audit
	 * @param integer $month The month of the audit
	 *
	 * @return array All the data needed for the audit screen
	 *
	 */

	function audit($year, $month)
	{
		$dims = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$leap = pdate::is_leap_year($year);
		if ($leap) {
			$dims[1] = 29;
		}

		$month_names = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

		$time_frame = $month_names[$month - 1] . ' ' . $year;

		/*
		 * NOTE: One thing to remember about balances: the balance returned from
		 * these routines is the balance at the end of THAT DAY. So if you want
		 * the balance as of the 1st of the month, you don't ask for that, because
		 * that would be the balance at the END of the 1st day. You ask for the
		 * balance at the end of the DAY BEFORE. VERY IMPORTANT.
		 */

		$from_dt = pdate::fromints($year, $month, 1);
		// adjust to the day before
		$from_dt = pdate::adddays($from_dt, -1);
		$to_dt = pdate::fromints($year, $month, $dims[$month - 1]);

		$start_dt = pdate::get($from_dt, 'Y-m-d');
		$end_dt = pdate::get($to_dt, 'Y-m-d');

		$from_bals = $this->audit_bals($start_dt);
		$nfrom_bals = count($from_bals);

		$to_bals = $this->audit_bals($end_dt);
		$nto_bals = count($to_bals);

		// now we figure the balance differences
		// while here, we figure the cash, l/t and s/t totals
		$balances = array();
		$cash = 0;
		$ltdebt = 0;
		$stdebt = 0;

		$nbals = count($from_bals); // $to_bals should have same count
		for ($i = 0; $i < $nbals; $i++) {
			$diff = $to_bals[$i]['balance'] - $from_bals[$i]['balance'];
			$balances[] = array(
				'acct_id' => $from_bals[$i]['acct_id'],
				'acct_type' => $from_bals[$i]['acct_type'],
				'acct_name' => $from_bals[$i]['name'],
				'from_bal' => $from_bals[$i]['balance'],
				'to_bal' => $to_bals[$i]['balance'],
				'diff_bal' => $diff);

			switch ($from_bals[$i]['acct_type']) {
			case 'C':
			case 'S': $cash += $diff;
				break;
			case 'R': $stdebt += $diff;
				break;
			case 'L': $ltdebt += $diff;
				break;
			}
		}

		// Now we have the beginning and ending balances for the month
		// from_bals[n] => [name, balance, date]
		// to_bals[n] => [name, balance, date]
		// diff_bals[n] => <number>
		
		$from_dt = pdate::adddays($from_dt, 1);
		$start_dt = pdate::get($from_dt, 'Y-m-d');

		$incomes = $this->audit_cats($start_dt, $end_dt, 'I');
		$expenses = $this->audit_cats($start_dt, $end_dt, 'E');

		// Now we have the expense and income totals for the period
		// incs[to_acct, acct_name, amount]
		// exps[to_acct, acct_name, amount]

		// Now we do the analysis
		$total_inc = 0;
		$nincs = count($incomes);
		for ($m = 0; $m < $nincs; $m++) {
			$total_inc += $incomes[$m]['amount'];
		}

		$total_exp = 0;
		$nexps = count($expenses);
		for ($n = 0; $n < $nexps; $n++) {
			$total_exp += $expenses[$n]['amount'];
		}

		$inc_exp = $total_inc + $total_exp;

		// now we have cash, ltdebt, stdebt, total_exp, total_inc, inc_exp
		// also arrays: $totals, $incs, $exps

		$final = $cash - $inc_exp + $ltdebt + $stdebt;

		$analysis = array(
			array('name' => 'Cash', 'total' => $cash),
			array('name' => 'Total Income', 'total' => $total_inc),
			array('name' => 'Total Expense', 'total' => $total_exp),
			array('name' => 'Inc/Exp', 'total' => $inc_exp),
			array('name' => 'S/T Debt', 'total' => $stdebt),
			array('name' => 'L/T Debt', 'total' => $ltdebt),
			array('name' => 'Difference', 'total' => $final),
		);

		// Now we polish up the return array
		$data['time_frame'] = $time_frame;
		$data['balances'] = $balances;
		$data['incomes'] = $incomes;
		$data['expenses'] = $expenses;
		$data['analysis'] = $analysis;

		return $data;	
	}

	/**
	 * Get all details on accounts like bank, checking and credit card accounts
	 *
	 * @return array Indexed array of 0 = number of accounts, 1 = array of account records
	 */
	
	function get_accounts()
	{
		$sql = "SELECT * from accounts WHERE acct_type IN ('R', 'C', 'S', 'L', 'Q') AND parent != 0 ORDER BY name";
		$accts = $this->db->query($sql)->fetch_all();

		return $accts;
	}

}
