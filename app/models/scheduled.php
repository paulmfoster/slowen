<?php

class scheduled
{
    public $db;

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
		if (!filled_out($post, ['from_acct', 'freq', 'period', 'payee_id', 'to_acct'])) {
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
            'freq' => $post['freq'],
            'period' => $post['period'],
            'last' => $post['last'],
			'payee_id' => $post['payee_id'],
			'to_acct' => $post['to_acct'],
			'memo' => $post['memo'],
			'amount' => dec2int($amount)
		];

		$this->db->insert('scheduled3', $rec);
		
		if ($post['xfer'] ?? FALSE) {
			$rec = [
				'from_acct' => $post['to_acct'],
                'freq' => $post['freq'],
                'period' => $post['period'],
                'last' => $post['last'],
				'payee_id' => $post['payee_id'],
				'to_acct' => $post['from_acct'],
				'memo' => $post['memo'],
				'amount' => - dec2int($amount)
			];

			$this->db->insert('scheduled3', $rec);
		}

		return TRUE;
	}

	/**
	 * Fetch the name of an account, given the acct id.
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

		$sql = "SELECT name FROM accounts WHERE id = $acct_id";
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
        $sql = "select s.id as id, last, from_acct, freq, period, s.payee_id as payee_id, to_acct, memo, amount, p.name as payee_name, a1.name as from_acct_name, a2.name as to_acct_name from scheduled3 as s left join payees as p on p.id = s.payee_id left join accounts as a1 on a1.id = s.from_acct left join accounts as a2 on a2.id = s.to_acct";
		$txns = $this->db->query($sql)->fetch_all();
		if ($txns === FALSE) {
			return FALSE;
		}

		return $txns;
	}

    /**
     * Fetch a single scheduled record.
     *
     * @param int ID of scheduled transaction
     * @return array the transaction
     */

    function fetch_single_scheduled($id)
    {
        $sql = "select s.id as id, last, from_acct, freq, period, s.payee_id as payee_id, to_acct, memo, amount, p.name as payee_name, a1.name as from_acct_name, a2.name as to_acct_name from scheduled3 as s left join payees as p on p.id = s.payee_id left join accounts as a1 on a1.id = s.from_acct left join accounts as a2 on a2.id = s.to_acct where s.id = $id";
		$txn = $this->db->query($sql)->fetch();
        return $txn;
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
				$this->db->delete('scheduled3', "id = $id");
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
     * Take the ID of a scheduled transaction, and repeat that transaction
     * as needed for the current month. 
     *
     * NOTE: This routine detects the current date, and generates
     * transactions within the current month. Therefore, it should only be
     * run once per month, and will only generate transactions for that
     * month. 
	 *
	 * @param integer $id The transaction ID
     * @return integer number of transactions generated
	 */

	private function activate_single($id)
	{
        $howmany = 0;

		$sql = "SELECT * FROM scheduled3 WHERE id = $id";
		$rec = $this->db->query($sql)->fetch();

        $dt = new xdate();

        $from = clone $dt;
        $from->day = 1;

        $to = clone $dt;
        $to->end_of_month();

        $rpts = library('repeats');

        $dates = $rpts->next($rec->$last, $rec->period, $rec->freq, $rec->occ, $from, $to);

        foreach ($dates as $date) {
            // update journal
            $r = [
                'txn_dt' => $date,
                'txnid' => $this->get_next_txnid(),
                'checkno' => '',
                'memo' => $rec['memo'] ?? 'scheduled transaction',
                'amount' => $rec['amount'],
                'from_acct' => $rec['from_acct'],
                'to_acct' => $rec['to_acct'],
                'payee_id' => $rec['payee_id'],
                'split' => 0,
                'status' => ' ',
                'recon_dt' => ''
            ];
		    $this->db->insert('journal', $r);

            // update the "last" field in scheduled table
            $this->db->update('scheduled3', ['last' => $date], "id = $id");

            $howmany++;
        }

        return $howmany;
	}

	/**
	 * Activate all (selected) scheduled transactions.
	 *
	 * Given an array of scheduled transaction IDs from the POST array,
	 * activate each in turn. This is designed to be run once a month, and
     * will iterate each scheduled record as many times as is necessary to
     * complete the iterations for the month.
     *
     * POST returns records which look like "id_XXX", where XXX is the
     * actual ID,
	 *
	 * @param array $post The POST array
	 * @return boolean TRUE if successful
	 */

	function activate_scheduled($post)
	{
        $howmany = 0;
		foreach ($post as $key => $val) {
			if (strpos($key, 'id_') === 0) {
				$id = (int) substr($key, 3);
                $result = $this->activate_single($id);
                $howmany += $result;
			}
		}

		return $howmany;
	}

    /**
     * Update a scheduled transaction.
     *
     * @param array the POST array
     * @return boolean TRUE for success, else FALSE
     */

    function update_scheduled($post)
    {
        $credit = $post['cr_amount'] ?? NULL;
        $debit = $post['dr_amount'] ?? NULL;
        if (is_null($credit) || strlen(trim($credit)) == 0) {
            $post['amount'] = -dec2int($debit);
        }
        elseif (is_null($debit) || strlen(trim($debit)) == 0) {
            $post['amount'] = dec2int($credit);
        }

        $rec = $this->db->prepare('scheduled3', $post);

        $result = $this->db->update('scheduled3', $rec, "id = {$rec['id']}");

        return $result;
    }

}
