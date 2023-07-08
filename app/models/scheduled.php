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
			'payee_id' => $post['payee_id'],
			'to_acct' => $post['to_acct'],
			'memo' => $post['memo'],
			'amount' => dec2int($amount)
		];

		$this->db->insert('scheduled2', $rec);
		
		if ($post['xfer'] ?? FALSE) {
			$rec = [
				'from_acct' => $post['to_acct'],
                'freq' => $post['freq'],
                'period' => $post['period'],
				'payee_id' => $post['payee_id'],
				'to_acct' => $post['from_acct'],
				'memo' => $post['memo'],
				'amount' => - dec2int($amount)
			];

			$this->db->insert('scheduled2', $rec);
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
        $sql = "select s.id as id, last, from_acct, freq, period, s.payee_id as payee_id, to_acct, memo, amount, p.name as payee_name, a1.name as from_acct_name, a2.name as to_acct_name from scheduled2 as s left join payees as p on p.id = s.payee_id left join accounts as a1 on a1.id = s.from_acct left join accounts as a2 on a2.id = s.to_acct";
		$txns = $this->db->query($sql)->fetch_all();
		if ($txns === FALSE) {
			return FALSE;
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
				$this->db->delete('scheduled2', "id = $id");
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
     * Returns the next date in a recurring date series.
	 *
     * Based on the period and frequency, it returns a date the appropriate
     * amount of time in the future.
	 * Return is in ISO date format
	 *
	 * @param xdate The reference date (last time)
	 * @param integer The frequency for this job
	 * @param string The periodicity of the job
	 *
	 * @return xdate The next date
     */

    function get_next_date($dt, $freq, $period)
    {
        if ($period == 'M')
			$dt->add_months($freq);
		elseif ($period == 'Q')
			$dt->add_months(3 * $freq);
        elseif ($period == 'D')
			$dt->add_days($freq);
        elseif ($period == 'Y')
			$dt->add_years($freq);
		elseif ($period == 'W')
			$dt->add_days($freq * 7);

        return $dt;
    }

	/**
	 * Activate a single scheduled transaction.
	 *
	 * This method takes the data for a scheduled transaction and creates a
	 * new transaction in the journal table from that data. It also updates
     * the "last" field in the scheduled table. This fails if the next date
     * is later than this month.
	 *
	 * @param integer $id The transaction ID
	 */

	private function activate_single($id)
	{
		$sql = "SELECT * FROM scheduled2 WHERE id = $id";
		$rec = $this->db->query($sql)->fetch();

        $limit = new xdate();
        $limit->day_after_month();

        $lastdt = new xdate();
        $lastdt->from_iso($rec['last']);

        $nextdt = $this->get_next_date($lastdt, $rec['freq'], $rec['period']);

        if ($nextdt->before($limit)) {

            $snextdt = $nextdt->to_iso();

            // update the "last" field in scheduled table
            $this->db->update('scheduled2', ['last' => $snextdt], "id = $id");

            // update journal
            $r = [
                'txn_dt' => $snextdt,
                'txnid' => $this->get_next_txnid(),
                'checkno' => '',
                'memo' => $rec['memo'] ?? '',
                'amount' => $rec['amount'],
                'from_acct' => $rec['from_acct'],
                'to_acct' => $rec['to_acct'],
                'payee_id' => $rec['payee_id'],
                'split' => 0,
                'status' => ' ',
                'recon_dt' => ''
            ];

		    $this->db->insert('journal', $r);

            return TRUE;
        }

        return FALSE;
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
		foreach ($post as $key => $val) {
			if (strpos($key, 'id_') === 0) {
				$id = (int) substr($key, 3);
                do {
                    // repeat until the month is over
                    $result = $this->activate_single($id);
                } while ($result);
			}
		}

		return TRUE;
	}

}
