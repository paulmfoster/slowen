<?php

class recon
{
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Get all details on accounts like bank, checking and credit card accounts
	 *
	 * @return array Indexed array of 0 = number of accounts, 1 = array of account records
	 */
	
	function get_recon_accts()
	{
		$sql = "SELECT * from accounts WHERE acct_type IN ('R', 'C', 'S') AND parent != 0 ORDER BY name";
		$accts = $this->db->query($sql)->fetch_all();

		return $accts;
	}

	// fixme is this needed?
	function get_bank_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'S') AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}

	/**
	 * Get transactions for display
	 * P parameter gets transactions by payee
	 * C parameter gets transactions by category
	 * (blank) parameter gets transactions by from_acct
	 *
	 * @param int payee_id, to_acct, or nothing
	 * @type char 'P' for payees, 'C' for categories, ' ' for neither
	 *
	 * @return array Full data needed for the register screen display
	 */

	function get_uncleared_transactions($param)
	{
		$sql = "SELECT journal.*, payees.name AS payee_name, a3.name AS from_acct_name, a4.name AS to_acct_name FROM journal LEFT JOIN payees ON payees.payee_id = journal.payee_id LEFT JOIN accounts AS a3 ON a3.acct_id = journal.from_acct LEFT JOIN accounts AS a4 ON a4.acct_id = journal.to_acct WHERE journal.from_acct = $param AND journal.status = ' ' ORDER BY txn_dt, checkno, txnid";

		$txns = $this->db->query($sql)->fetch_all();	

		if ($txns === FALSE)
			return FALSE;

		$max_txns = count($txns);

		// massage dates, amounts
		for ($i = 0; $i < $max_txns; $i++) {
			
			$txns[$i]['x_txn_dt'] = pdate::reformat('Y-m-d', $txns[$i]['txn_dt'], 'm/d/y');

			if ($txns[$i]['amount'] < 0) {
				$txns[$i]['debit'] = int2dec(abs($txns[$i]['amount']));
				$txns[$i]['credit'] = '';
			}
			else {
				$txns[$i]['credit'] = int2dec($txns[$i]['amount']);
				$txns[$i]['debit'] = '';
			}

		}

		return $txns;
		
	}

	function get_account($acct_id)
	{
		global $acct_types;

		$sql = "SELECT a1.*, a2.name as x_parent FROM accounts as a1 left join accounts as a2 on a2.acct_id = a1.parent WHERE a1.acct_id = $acct_id ORDER BY lower(a1.name)";
		$acct = $this->db->query($sql)->fetch();

		if ($acct === FALSE) {
			return FALSE;
		}

		return $acct;
	}

	/**
	 * Check reconciliation: everything checks?
	 *
	 * @param integer $from_acct The from account
	 * @param float $stmt_start_bal Beginning balance from statement
	 * @param float $stmt_end_bal Ending balance from statement
	 * @param array $ids IDs for marked transactions
	 * 
	 * @return array Success: TRUE; Failure: all info necessary to explain the failure
	 */

	function check_reconciliation($from_acct, $stmt_start_bal, $stmt_end_bal, $ids)
	{
		$acct = $this->get_account($from_acct);

		$comp_start_bal = $acct['open_bal'];
		$sql = "select sum(amount) as total from journal where from_acct = $from_acct";
		$all_txns = $this->db->query($sql)->fetch();
		$comp_all_txns = $all_txns['total'];
		$comp_end_bal = $comp_start_bal + $comp_all_txns;

		$sql = "SELECT sum(amount) AS total FROM journal WHERE from_acct = $from_acct AND status != 'R' AND NOT id IN ($ids)";
		$uncleared = $this->db->query($sql)->fetch();
		$comp_uncleared_txns = $uncleared['total'];

		$x_stmt_end_bal = $stmt_end_bal;
		$check_bal = $x_stmt_end_bal + $comp_uncleared_txns;
	
		$difference = $comp_end_bal - $check_bal;

		if ($difference == 0) {
			// success
			return TRUE;
		}

		// something went wrong; return all relevant data
		$data = array(
			'from_acct_name' => $acct['name'],
			'comp_start_bal' => $comp_start_bal,
			'comp_all_txns' => $comp_all_txns,
			'comp_end_bal' => $comp_end_bal,
			'stmt_start_bal' => $stmt_start_bal,
			'stmt_end_bal' => $stmt_end_bal,
			'comp_uncleared_txns' => $comp_uncleared_txns,
			'check_bal' => $check_bal,
			'difference' => $comp_end_bal - $check_bal
		);
		return $data;
	}

	/**
	 * Finish up a reconciliation
	 *
	 * @param int $from_acct Which account?
	 * @param int $stmt_end_bal Statement ending balance
	 * @param string $recon_dt Closing date from statement
	 * @param string $ids List of transactions ("id, id, id, ...")
	 *
	 */

	function finish_reconciliation($from_acct, $stmt_end_bal, $recon_dt, $ids)
	{
		global $date_template;

		// $recon_dt = pdate::reformat($date_template, $recon_dt, 'Y-m-d');
		$balance = dec2int($stmt_end_bal);

		$this->db->begin();

		$sql = "update accounts set rec_bal = $balance, recon_dt = '$recon_dt' where acct_id = $from_acct";
		$this->db->query($sql);

		// sql for the actual update
		$sql = "update journal set status = 'R', recon_dt = '$recon_dt' where id in (" . $ids . ")"; 
		$this->db->query($sql);

		$this->db->commit();
	}
}
