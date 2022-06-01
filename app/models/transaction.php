<?php

class transaction
{
	function __construct($db)
	{
        $this->db = $db;
	}

	/**
	 * Fetch data for the "add transaction" verify screen
	 *
	 * @param int $from_acct The from_acct account number
	 * @param int $payee_id The payee_id from the form
	 * @param int $to_acct The to_acct account number
	 *
	 * @return array Indexed array of the actual names from the three
	 * integer parameters passed in.
	 */

	function get_names($from_acct, $payee_id, $to_acct)
	{

		$sql1 = "select name from accounts where id = $from_acct";
		$d = $this->db->query($sql1)->fetch();
		$rtn['from_acct_name'] = $d['name'] ? $d['name'] : '';

		$sql2 = "select name from accounts where id = $to_acct";
		$e = $this->db->query($sql2)->fetch();
		$rtn['to_acct_name'] = $e['name'] ? $e['name'] : '';

		$sql3 = "select name from payees where id = $payee_id";
		$f = $this->db->query($sql3)->fetch();
		$rtn['payee_name'] = $f['name'] ? $f['name'] : '';

		return $rtn;

	}

	function get_bank_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'S') AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}

	/**
	 * Get all the data on accounts/categories for a HTML SELECT field
	 * Note: an illegal account of NONE is added to the top
	 *
	 * @return array Indexed array, 0 = max records returned, 1 = array of account records
	 */

	function get_accounts()
	{
		$sql = "SELECT * FROM accounts ORDER BY lower(name)";
		$accounts = $this->db->query($sql)->fetch_all();

		return $accounts;
	}

	function get_account($id)
	{
        global $acct_types;

		$sql = "SELECT a1.*, a2.name as x_parent FROM accounts as a1 left join accounts as a2 on a2.id = a1.parent WHERE a1.id = $id ORDER BY lower(a1.name)";
		$acct = $this->db->query($sql)->fetch();

		if ($acct === FALSE) {
			return FALSE;
		}

		$acct['x_acct_type'] = $acct_types[$acct['acct_type']];
		$acct['x_open_dt'] = pdate::reformat('Y-m-d', $acct['open_dt'], 'm/d/y');
		$acct['x_recon_dt'] = pdate::reformat('Y-m-d', $acct['recon_dt'], 'm/d/y');
		$acct['x_open_bal'] = int2dec($acct['open_bal']);
		$acct['x_rec_bal'] = int2dec($acct['rec_bal']);

		return $acct;
	}

	function get_open_bal($id)
	{
		$sql = "SELECT open_bal FROM accounts WHERE id = $id";
		$rec = $this->db->query($sql)->fetch();
		return $rec['open_bal'];
	}

	function transactions_sql($where_clause)
	{
		$sql = "SELECT journal.*, payees.name AS payee_name, a3.name AS from_acct_name, a4.name AS to_acct_name FROM journal LEFT JOIN payees ON payees.id = journal.payee_id LEFT JOIN accounts AS a3 ON a3.id = journal.from_acct LEFT JOIN accounts AS a4 ON a4.id = journal.to_acct WHERE $where_clause ORDER BY txn_dt, checkno, txnid";
		return $sql;
	}

	/**
	 * Get transactions for display
     *
	 * P parameter gets transactions by payee
	 * C parameter gets transactions by category
	 * (blank) parameter gets transactions by from_acct
	 *
	 * @param int payee_id, to_acct, or nothing
	 * @param char 'P' for payees, 'C' for categories, 'F' for from
	 * accts
	 *
	 * @return array Full data needed for the register screen display
	 */

	function get_transactions($param, $type = 'F')
	{
		// get transaction data
		switch ($type) {
			case 'F':
				$sql = $this->transactions_sql('journal.from_acct = ' . $param);
				break;
			case 'P':
				$sql = $this->transactions_sql('journal.payee_id = ' . $param);
				break;
			case 'C':
				$sql = $this->transactions_sql('journal.to_acct = ' . $param);
				break;
		}

		$txns = $this->db->query($sql)->fetch_all();	

		if ($txns === FALSE)
			return FALSE;

		$max_txns = count($txns);

		if ($type == 'F') {
			$open_bal = $this->get_open_bal($param);
			$end_bal = $open_bal;
		}

		// massage dates, amounts
		for ($i = 0; $i < $max_txns; $i++) {
			
			if ($txns[$i]['amount'] < 0) {
				$txns[$i]['debit'] = - $txns[$i]['amount'];
				$txns[$i]['credit'] = '';
			}
			elseif ($txns[$i]['amount'] > 0) {
				$txns[$i]['credit'] = $txns[$i]['amount'];
				$txns[$i]['debit'] = '';
			}
            else {
                $txns[$i]['credit'] = '';
                $txns[$i]['debit'] = '';
            }

			if ($type == 'F') {
				$end_bal += $txns[$i]['amount'];
				$txns[$i]['balance'] = $end_bal;
			}
		}

		return $txns;
		
	}

	/**
	 * get_transaction()
	 *
	 * Get a transaction, based on txnid, including splits.
	 * Massage values which aren't really human readable.
     * Splits are not included.
	 *
	 * @param integer $txnid
	 *
	 * @return array One or more records representing the same txnid
	 *
	 */

	function get_transaction($txnid)
	{
        global $statuses;

		$sql = "select journal.*, payees.name as payee_name, a3.name as from_acct_name, a4.name as to_acct_name from journal left join payees on journal.payee_id = payees.id left join accounts as a3 on journal.from_acct = a3.id left join accounts as a4 on journal.to_acct = a4.id where journal.txnid = $txnid order by journal.id";

		$txns = $this->db->query($sql)->fetch_all();
		$max_txns = count($txns);

		for ($i = 0; $i < $max_txns; $i++) {
			if ($txns[$i]['amount'] < 0) {
				$txns[$i]['dr_amount'] = int2dec($txns[$i]['amount']);
				$txns[$i]['cr_amount'] = '';
			}
			elseif ($txns[$i]['amount'] > 0) {
				$txns[$i]['cr_amount'] = int2dec($txns[$i]['amount']);
				$txns[$i]['dr_amount'] = '';
			}
			else {
				$txns[$i]['cr_amount'] = 0;
				$txns[$i]['dr_amount'] = 0;
			}
			$txns[$i]['x_status'] = $statuses[$txns[$i]['status']];
		}

		return $txns;
	}

	/**
	 * Get all the info on payees for a payees SELECT HTML component
	 * Note: an added illegal payee is added to the top for NONE.
	 *
	 * @return array Indexed array, 0 = number of payees, 1 = payee records (array)
	 */

	function get_payees()
	{
		$sql = "SELECT * FROM payees ORDER BY lower(name)";
		$payees = $this->db->query($sql)->fetch_all();

		return $payees;
	}
	
	function get_to_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE parent != 0 ORDER BY lower(name)";
		$to_accts = $this->db->query($sql)->fetch_all();
		array_unshift($to_accts, array('id' => '0', 'name' => 'NONE', 'acct_type' => ' '));
		return $to_accts;
	}

	function get_splits($jnlid)
	{
		$sql = "SELECT s.*, p.name AS payee_name, a.name AS to_acct_name FROM splits AS s left JOIN payees AS p ON p.id = s.payee_id LEFT JOIN accounts AS a ON a.id = s.to_acct WHERE jnlid = $jnlid";
		$splits = $this->db->query($sql)->fetch_all();
		return $splits;
	}


	/**
	 * Saves edits from the transaction edit screen
	 *
	 * NOTE: This routine does not check user input; it simply stores
	 * the input.
	 *
	 * @param array $post Normally, the $_POST array
	 *
	 */

	function update_transaction($post)
	{
		$this->db->begin();

		if ($post['txntype'] == 'xfer') {
			$rec = [
				'txn_dt' => $post['txn_dt'],
				'checkno' => $post['checkno'],
				'payee_id' => $post['payee_id'],
				'memo' => $post['memo']
			];
			$this->db->update('journal', $rec, "txnid = {$post['txnid']}");
		}

		if ($post['txntype'] == 'single' || $post['txntype'] == 'split') {
            // if status is 'R' or 'V', no amount present in POST
			if (isset($post['amount'])) {
				$post['amount'] = dec2int($post['amount']);
			}
			$rec = $this->db->prepare('journal', $post);
			$this->db->update('journal', $rec, "txnid = {$post['txnid']}");
		}

		if ($post['txntype'] == 'splits') {
			$max_splits = count($post['split_amount']);
			for ($j = 0; $j < $max_splits; $j++) {
				$rec = array(
					'payee_id' => $post['split_payee_id'][$j],
					'to_acct' => $post['split_to_acct'][$j],
					'memo' => $post['split_memo'][$j],
					'amount' => dec2int($post['split_amount'][$j])
				);
				$prec = $this->db->prepare('splits', $rec);
				$this->db->update('splits', $prec, "id = {$post['split_id'][$j]}");
			}
		}

		$this->db->commit();
		emsg('S', "Transaction UPDATE successful");
		return TRUE;

	}

	function void_transaction($txnid)
	{
		// don't allow user to delete cleared transactions
		$sql = "SELECT * FROM journal WHERE txnid = $txnid";
		$r = $this->db->query($sql)->fetch_all();
		if ($r[0]['status'] == 'R') {
			emsg('F', "Transaction is reconciled. Cannot void.");
			return FALSE;
		}

		if ($r[0]['split']) {
			$this->db->update('splits', array('amount' => 0), "txnid = $txnid");
		}

		$s = array('status' => 'V', 'amount' => 0);
		$this->db->update('journal', $s, "txnid = $txnid");

		emsg('S', "Transaction voided");
		return TRUE;
	}

}
