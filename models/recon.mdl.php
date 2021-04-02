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
	 *
	 * @param int from_acct
	 * @return array Full data needed for the register screen display
	 */

	function get_uncleared_transactions($param)
	{
		$sql = "SELECT journal.*, payees.name AS payee_name, a3.name AS from_acct_name, a4.name AS to_acct_name FROM journal LEFT JOIN payees ON payees.payee_id = journal.payee_id LEFT JOIN accounts AS a3 ON a3.acct_id = journal.from_acct LEFT JOIN accounts AS a4 ON a4.acct_id = journal.to_acct WHERE journal.from_acct = $param AND journal.status NOT IN ('R', 'V') ORDER BY txn_dt, checkno, txnid";

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

		// get the opening balance for this account
		$comp_start_bal = $acct['open_bal'];
		// add up all the transactions for this account
		$sql = "select sum(amount) as total from journal where from_acct = $from_acct";
		$all_txns = $this->db->query($sql)->fetch();
		$comp_all_txns = $all_txns['total'];
		// get the ending balance for this account
		$comp_end_bal = $comp_start_bal + $comp_all_txns;

		// get uncleared items not marked by user
		$sql = "SELECT sum(amount) AS total FROM journal WHERE from_acct = $from_acct AND status != 'R' AND NOT id IN ($ids)";
		$uncleared = $this->db->query($sql)->fetch();
		$comp_uncleared_txns = $uncleared['total'];

		// take statement balance + uncleared items
		$check_bal = dec2int($stmt_end_bal) + $comp_uncleared_txns;

		// statement balance + uncleared items should equal computer
		// ending balance
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
	 * Mark transactions as cleared even though reconcilation failed.
	 *
	 * This is made complicated because the user may revisit a
	 * reconciliation multiple times, and undo work they've done before. If
	 * a user marks a transaction as cleared, then goes back and unmarks
	 * it, the second time through, it doesn't show that they unmarked it.
	 * So we unmark everything first, and just mark all they *have* marked.
	 *
	 * @param string Comma separated list of record IDs to clear
	 * @param integer From account
	 */

	function save_work($ids, $from_acct)
	{
		$this->unclear_all($from_acct);
		$this->db->update('journal', ['status' => 'C'], "id in ($ids)");
	}

	/**
	 * Get the next serialization number for transactions.
	 * This is NOT the same as the ID.
	 *
	 * @return int next transaction ID
	 */

	function get_next_txnid()
	{
		$sql = "SELECT max(txnid) as txnid FROM journal";
		$nt = $this->db->query($sql)->fetch();
		$ret = $nt['txnid'] + 1;

		return $ret;
	}

	function get_payees()
	{
		$sql = "SELECT * FROM payees ORDER BY name";
		$payees = $this->db->query($sql)->fetch_all();
		return $payees;
	}

	function get_to_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE parent != 0 ORDER BY lower(name)";
		$to_accts = $this->db->query($sql)->fetch_all();
		return $to_accts;
	}

	function add_statement_fee($from_acct, $payee_id, $to_acct, $fee, $stmt_dt)
	{
		$rec = [
			'from_acct' => $from_acct,
			'txnid' => $this->get_next_txnid(),
			'txn_dt' => $stmt_dt,
			'checkno' => '',
			'split' => 0,
			'payee_id' => $payee_id,
			'to_acct' => $to_acct,
			'memo' => '',
			'status' => ' ',
			'recon_dt' => '',
			'amount' => dec2int($fee)
		];

		$this->db->insert('journal', $rec);

	}

	/**
	 * "Unclear" all transactions for an account.
	 *
	 * This is used where a user starts a reconciliation, leaves it, and
	 * then on the next go, unclears transactions previously cleared. In
	 * other words, they uncleared all transactions.
	 *
	 * @param integer From account
	 */

	function unclear_all($from_acct)
	{
		$this->db->update('journal', ['status' => ' '], "status = 'C'");
	}

	/**
	 * Get IDs of uncleared transactions.
	 * 
	 * Used for saving in-progress cleared transactions. This includes
	 * transactions which have been marked as cleared, but where a full
	 * reconciliation hasn't been completed yet.
	 *
	 * @param integer From account for transactions
	 * @return array IDs of cleared transactions
	 */

	function get_uncleareds($from_acct)
	{
		$sql = "SELECT id FROM journal WHERE from_acct = $from_acct AND status IN (' ', 'C')";
		$recs = $this->db->query($sql)->fetch_all();
		$uc = [];
		foreach ($recs as $rec) {
			$uc[] = $rec['id'];
		}
		return $uc;
	}

	/**
	 * Finish up a reconciliation
	 *
	 * @param int $from_acct Which account?
	 * @param float $stmt_end_bal Statement ending balance
	 * @param string $recon_dt Closing date from statement
	 * @param string $ids List of transactions ("id, id, id, ...")
	 *
	 */

	function finish_reconciliation($from_acct, $stmt_end_bal, $recon_dt, $ids)
	{
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
