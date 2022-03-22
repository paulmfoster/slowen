<?php

class scheduled
{
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Add scheduled transaction.
	 *
	 * @param array $post The POST array
	 * @return boolean TRUE on success, FALSE on failure
	 */

	function add_scheduled($post)
	{
		if (!filled_out($post, ['from_acct', 'txn_dom', 'payee_id', 'to_acct'])) {
			emsg('F', 'One or more mandatory fields not filled out.');
			return FALSE;
		}

		if (!empty($post['dr_amount'])) {
			$amount = - $post['dr_amount'];
		}
		elseif (!empty($post['cr_amount'])) {
			$amount = $post['cr_amount'];
		}
		else {
			emsg('F', 'No amount entered. Restart.');
			return FALSE;
		}

		$rec = [
			'from_acct' => $post['from_acct'],
			'txn_dom' => $post['txn_dom'],
			'payee_id' => $post['payee_id'],
			'to_acct' => $post['to_acct'],
			'memo' => $post['memo'],
			'amount' => dec2int($amount)
		];

		$this->db->insert('scheduled', $rec);
		
		if ($post['xfer'] ?? FALSE) {
			$rec = [
				'from_acct' => $post['to_acct'],
				'txn_dom' => $post['txn_dom'],
				'payee_id' => $post['payee_id'],
				'to_acct' => $post['from_acct'],
				'memo' => $post['memo'],
				'amount' => - dec2int($amount)
			];

			$this->db->insert('scheduled', $rec);
		}

		return TRUE;
	}

	/**
	 * Fetch the name of an account, given the acct_id.
	 *
	 * Using a list of account IDs and account names accumulated from
	 * repeated calls to this function, return the name.
	 *
	 * @param integer $acct_id Account ID
	 * @return string Account name
	 */

	function get_acct_name($acct_id)
	{
		static $accts = [];

		foreach ($accts as $id => $name) {
			if ($acct_id == $id) {
				return $name;
			}
		}

		$sql = "SELECT name FROM accounts WHERE acct_id = $acct_id";
		$rec = $this->db->query($sql)->fetch();
		$accts[] = ['id' => $acct_id, 'name' => $rec['name']];

		return $rec['name'];
	}

	/**
	 * Fetch all scheduled transaction records.
	 *
	 * @return array All scheduled transaction records
	 */

	function fetch_scheduled()
	{
		// fetch transaction and payee name
		$sql = "select s.id as id, from_acct, txn_dom, s.payee_id as payee_id, to_acct, memo, amount, p.name as payee_name from scheduled as s, payees as p where p.payee_id = s.payee_id";
		$txns = $this->db->query($sql)->fetch_all();
		if ($txns === FALSE) {
			return FALSE;
		}
		$max_txns = count($txns);

		// get from and to account names
		for ($i = 0; $i < $max_txns; $i++) {
			$txns[$i]['from_acct_name'] = $this->get_acct_name($txns[$i]['from_acct']);
			$txns[$i]['to_acct_name'] = $this->get_acct_name($txns[$i]['to_acct']);
		}

		return $txns;
	}

	/**
	 * Delete selected scheduled transactions.
	 *
	 * Given an array of scheduled transaction IDs from the POST array,
	 * delete each from the "scheduled" table in turn.
	 *
	 * @param array $post The POST array
	 * @return boolean True if successful
	 */

	function delete_scheduled($post)
	{
		foreach ($post as $key => $val) {
			if (strpos($key, 'id_') === 0) {
				$id = (int) substr($key, 3);
				$this->db->delete('scheduled', "id = $id");
			}
		}

		return TRUE;
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

	/**
	 * Activate a single scheduled transaction.
	 *
	 * This method takes the data for a scheduled transaction and creates a
	 * new transaction in the journal table from that data.
	 *
	 * @param integer $id The transaction ID
	 */

	private function activate_single($id)
	{
		$sql = "SELECT * FROM scheduled WHERE id = $id";
		$rec = $this->db->query($sql)->fetch();

		$refdt = pdate::now();
		$dtarr = pdate::set($refdt['y'], $refdt['m'], $rec['txn_dom']);
		$today = pdate::get($dtarr, 'Y-m-d');

		// these will throw errors if copied into journal table
		unset($rec['id']);
		unset($rec['txn_dom']);

		$rec['txn_dt'] = $today;
		$rec['txnid'] = $this->get_next_txnid();
		$rec['checkno'] = '';
		$rec['memo'] = $rec['memo'] ?? '';

		$this->db->insert('journal', $rec);
	}

	/**
	 * Activate all (selected) scheduled transactions.
	 *
	 * Given an array of scheduled transaction IDs from the POST array,
	 * activate each in turn.
	 *
	 * @param array $post The POST array
	 * @return boolean TRUE if successful
	 */

	function activate_scheduled($post)
	{
		foreach ($post as $key => $val) {
			if (strpos($key, 'id_') === 0) {
				$id = (int) substr($key, 3);
				$this->activate_single($id);
			}
		}

		return TRUE;
	}

    function scheduled_list()
    {
        $sql = 'select s.*, a.name as from_acct_name, b.name as to_acct_name, p.name as payee_name from scheduled as s join accounts as a on (a.acct_id = s.from_acct) join accounts as b on (b.acct_id = s.to_acct) join payees as p on (p.payee_id = s.payee_id) order by txn_dom';
        $list = $this->db->query($sql)->fetch_all();
        return $list;
    }

}
