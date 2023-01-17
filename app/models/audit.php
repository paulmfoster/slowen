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
				if ($txns[$j]['from_acct'] == $accts[$i]['id']) {
					$bal += $txns[$j]['total'];
					break;
				}
			}

			$bals[] = array('name' => $accts[$i]['name'], 'balance' => $bal, 'id' => $accts[$i]['id'], 'acct_type' => $accts[$i]['acct_type']);
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

		$sql = "select distinct to_acct as cat_no, accounts.name as cat_name, sum(amount) as amount from journal, accounts where journal.to_acct = accounts.id and accounts.acct_type = '$inc_exp' and txn_dt >= '$iso_from_date' and txn_dt <= '$iso_to_date' group by to_acct order by cat_name";
		$r = $this->db->query($sql)->fetch_all();
		if ($r === FALSE) {
			// if we get to here, there is no income/expense
			$rmax = 0;
		}
		else {
			$rmax = count($r);
		}

		// get totals from splits for each category

		$sql = "select splits.to_acct as cat_no, sum(splits.amount) as amount, accounts.name as cat_name from splits, journal, accounts where journal.id = splits.jnlid and accounts.id = splits.to_acct and accounts.acct_type = '$inc_exp' and journal.txn_dt >= '$iso_from_date' and journal.txn_dt <= '$iso_to_date' group by splits.to_acct order by splits.to_acct";
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

	function do_audit($from_date, $end_dt)
	{
		/*
		 * NOTE: One thing to remember about balances: the balance returned from
		 * these routines is the balance at the end of THAT DAY. So if you want
		 * the balance as of the 1st of the month, you don't ask for that, because
		 * that would be the balance at the END of the 1st day. You ask for the
		 * balance at the end of the DAY BEFORE. VERY IMPORTANT.
		 */

		$from_dt = pdate::fromiso($from_date);
		// adjust to the day before
		$from_dt = pdate::adddays($from_dt, -1);
		$start_dt = pdate::get($from_dt, 'Y-m-d');

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
		$equity = 0; //

		$nbals = count($from_bals); // $to_bals should have same count
		for ($i = 0; $i < $nbals; $i++) {
			$diff = $to_bals[$i]['balance'] - $from_bals[$i]['balance'];
			$balances[] = array(
				'id' => $from_bals[$i]['id'],
				'acct_type' => $from_bals[$i]['acct_type'],
				'acct_name' => $from_bals[$i]['name'],
				'from_bal' => $from_bals[$i]['balance'],
				'to_bal' => $to_bals[$i]['balance'],
				'diff_bal' => $diff);

			switch ($from_bals[$i]['acct_type']) {
			case 'C':
			case 'S': 
				// checking and savings
				$cash += $diff;
				break;
			case 'R': 
				// credit cards
				$stdebt += $diff;
				break;
			case 'L': 
				// liability (loans)
				$ltdebt += $diff;
				break;
			case 'Q': //
				// equity
				$equity += $diff;
				break;
			}
		}

		// Now we have the beginning and ending balances for the month
		// from_bals[n] => [name, balance, date]
		// to_bals[n] => [name, balance, date]
		// diff_bals[n] => <number>
		// Now move the start date to January 1
		
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

		$final = $cash - $inc_exp + $ltdebt + $stdebt + $equity;

		$analysis = array(
			array('name' => 'Cash', 'total' => $cash),
			array('name' => 'Total Income', 'total' => $total_inc),
			array('name' => 'Total Expense', 'total' => $total_exp),
			array('name' => 'Inc/Exp', 'total' => $inc_exp),
			array('name' => 'S/T Debt', 'total' => $stdebt),
			array('name' => 'L/T Debt', 'total' => $ltdebt),
			array('name' => "Owner's Equity", 'total' => $equity),
			array('name' => 'Difference', 'total' => $final)
		);

		// Now we polish up the return array
		$data['balances'] = $balances;
		$data['incomes'] = $incomes;
		$data['expenses'] = $expenses;
		$data['equity'] =  $equity;
		$data['analysis'] = $analysis;

		return $data;	
	}

	/**
	 * Perform a yearly audit.
	 *
	 * @param int $year The year of the audit
	 * @return array The resulting audit
	 */

	function yearly_audit($year)
	{
		$from_str = $year . '-01-01';
		$to_str = $year . '-12-31';

		$data = $this->do_audit($from_str, $to_str);
		$data['time_frame'] = $year;
		$data['filename'] = 'audit-' . $year . '-' . 'all' . '.pdf';

		return $data;
	}

	/**
	 * Perform a monthly audit.
	 *
	 * @param integer $year The year of the audit
	 * @param integer $month The month of the audit
	 *
	 * @return array All the data needed for the audit screen
	 *
	 */

	function monthly_audit($year, $month)
	{
		$month_names = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$dims = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$leap = pdate::is_leap_year($year);
		if ($leap) {
			$dims[1] = 29;
		}

		$from_dt = pdate::fromints($year, $month, 1);
		$start_dt = pdate::toiso($from_dt);
		$to_dt = pdate::fromints($year, $month, $dims[$month - 1]);
		$end_dt = pdate::toiso($to_dt);

		$data = $this->do_audit($start_dt, $end_dt);
		$data['time_frame'] = $month_names[$month - 1] . ' ' . $year;
		$data['filename'] = 'audit-' . $year . '-' . sprintf('%02d', $month) . '.pdf';

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

	function print_audit($data, $filename)
	{
		global $cfg;

        $pdf = load('pdf_report');
		// LIBDIR . 'pdf_report.lib.php';
		// $pdf = new pdf_report;

		$pdf->add_page();
		$pdf->set_margins(6, 6, 5);

		$str = '                           Audit, Period ' . $data['time_frame'];
		$pdf->print_line($str);
		$pdf->skip_line();

		$bal_hdr1 = '            Balances                 Start Bal     End Bal       Diff';
		$pdf->print_line($bal_hdr1);

		$bal_hdr2 = '----------------------------------- ------------ ------------ ------------';
		$pdf->print_line($bal_hdr2);
		
		$nbals = count($data['balances']);
		for ($i = 0; $i < $nbals; $i++) {
			$line = sprintf('%35s %12.2f %12.2f %12.2f', $data['balances'][$i]['acct_name'],
				int2dec($data['balances'][$i]['from_bal']),
				int2dec($data['balances'][$i]['to_bal']),
				int2dec($data['balances'][$i]['diff_bal']));
			$pdf->print_line($line);
		}

		$pdf->skip_line(2);

		$inc_hdr1 = '         Income Category               Amount';
		$pdf->print_line($inc_hdr1);
		$inc_hdr2 = '----------------------------------- ------------';
		$pdf->print_line($inc_hdr2);

		$nincs = count($data['incomes']);
		for ($j = 0; $j < $nincs; $j++) {
			$line = sprintf('%35s %12.2f', $data['incomes'][$j]['cat_name'],
				int2dec($data['incomes'][$j]['amount']));
			$pdf->print_line($line);
		}

		$pdf->skip_line(2);

		$exp_hdr1 = '         Expense Category              Amount';
		$pdf->print_line($exp_hdr1);
		$exp_hdr2 = '----------------------------------- ------------';
		$pdf->print_line($exp_hdr2);

		$nincs = count($data['expenses']);
		for ($k = 0; $k < $nincs; $k++) {
			$line = sprintf('%35s %12.2f', $data['expenses'][$k]['cat_name'],
				int2dec($data['expenses'][$k]['amount']));
			$pdf->print_line($line);
		}

		$pdf->skip_line(2);

		$anl_hdr1 = '          Analysis Category            Amount';
		$pdf->print_line($anl_hdr1);
		$anl_hdr2 = '----------------------------------- ------------';
		$pdf->print_line($anl_hdr2);

		$nitems = count($data['analysis']);
		for ($m = 0; $m < $nitems; $m++) {
			$line = sprintf('%35s %12.2f', $data['analysis'][$m]['name'],
				int2dec($data['analysis'][$m]['total']));
			$pdf->print_line($line);
		}

		$pdf->output($filename);

    }
}
