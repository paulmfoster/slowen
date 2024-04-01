<?php

class addtxn
{
    public $db, $statuses;

	function __construct($db)
	{
		$this->db = $db;
		$this->statuses = array(
			'C' => 'Cleared',
			'R' => 'Reconciled',
			'V' => 'Void',
			' ' => 'Uncleared'
		);
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

	function get_account($id)
	{
		global $acct_types;

		$sql = "SELECT a1.*, a2.name as x_parent FROM accounts as a1 left join accounts as a2 on a2.id = a1.parent WHERE a1.id = $id ORDER BY lower(a1.name)";
		$acct = $this->db->query($sql)->fetch();

		if ($acct === FALSE) {
			return FALSE;
		}

		$acct['x_acct_type'] = $acct_types[$acct['acct_type']];

		return $acct;
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

    /**
     * Get all accounts, and return them categorized.
     *
     * There are numerous calls for various categories of accounts. To
     * avoid the need to make all those calls, this fetches *once* from the
     * database, and then categorizes the results.
     *
     * @return array The categorized accounts
     */

    function get_all_accounts()
    {
		$sql = "SELECT * FROM accounts WHERE parent != 0 ORDER BY lower(name)";
		$accounts = $this->db->query($sql)->fetch_all();

        $raccts = [];
        foreach ($accounts as $a) {
            if ($a['parent'] != 0) {
                switch ($a['acct_type']) {
                case 'R':
                    $raccts['ccard'][] = $a;
                    $raccts['from'][] = $a;
                    $raccts['to'][] = $a;
                    break;
                case 'C':
                    $raccts['from'][] = $a;
                    $raccts['bank'][] = $a;
                    $raccts['to'][] = $a;
                    break;
                case 'S':
                    $raccts['from'][] = $a;
                    $raccts['bank'][] = $a;
                    $raccts['to'][] = $a;
                    break;
                case 'L':
                    $raccts['from'][] = $a;
                    break;
                case 'Q':
                    $raccts['from'][] = $a;
                    break;
                default:
                    // I, E, A
                    $raccts['to'][] = $a;
                }
            }
        }
        return $raccts;
    }

	function get_bank_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'S') AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}

    function should_xfer($from_acct, $to_acct)
    {
        $sql = "SELECT acct_type FROM accounts WHERE id = $from_acct";
        $from = $this->db->query($sql)->fetch();
        $sql = "SELECT acct_type FROM accounts WHERE id = $to_acct";
        $to = $this->db->query($sql)->fetch();

        if (!$from || !$to) {
            // if one or another account is false, no xfer; probably a VOID
            // transaction
            return FALSE;
        }

        $xfer = FALSE;
        if ($from['acct_type'] == 'C' || $from['acct_type'] == 'S') {
            switch ($to['acct_type']) {
            case 'R':
            case 'C':
            case 'S':
                $xfer = TRUE;
                break;
            }
        }
        return $xfer;
    }

	/**
	 * add_transaction()
	 *
	 * @param array The POST data to save
	 *
	 * @return boolean TRUE on success, FALSE on error
	 */

	function add_transaction($post)
	{
        // figure out transfers first
        if ($post['max_splits'] != 0) {
            // no transfers on splits
            $post['xfer'] = FALSE;
        }
        else {
            $post['xfer'] = $this->should_xfer($post['from_acct'], $post['to_acct']);
        }

		// checkboxes, unchecked don't show up in $_POST

		if (!isset($post['split'])) {
			$post['split'] = 0;
		}

		if (empty($post['amount']) || $post['status'] == 'V') {
			$post['amount'] = 0;
		}
		else {
			// we assume amount is properly signed
			$post['amount'] = dec2int($post['amount']);
		}

        if ($post['status'] != 'V') {
            if ($post['split'] == 0) {

                if (empty($post['payee_id'])) {
                    emsg('F', 'Normal transactions must have a valid payee');
                    return FALSE;
                }

                if (empty($post['to_acct'])) {
                    emsg('F','Normal transactions must have a valid to account');
                    return FALSE;
                }
            }
            else {
                // derive amount before we store the journal record
                $post['amount'] = 0;
			    for ($k = 0; $k < $post['max_splits']; $k++) {
                    if (!empty($post['split_dr_amount'][$k])) {
                        $post['amount'] += - dec2int($post['split_dr_amount'][$k]);
                    }
                    elseif (!empty($post['split_cr_amount'][$k])) {
                        $post['amount'] += dec2int($post['split_cr_amount'][$k]);
                    }
                }
            }
        }

		if ($post['xfer'] && empty($post['to_acct'])) {
			emsg('F', 'Transfers must have a valid TO account');
			return FALSE;
		}

        // start actually saving data
		$this->db->begin();

		// get next transaction ID
		$post['txnid'] = $this->get_next_txnid();

		$rec = $this->db->prepare('journal', $post);
		$this->db->insert('journal', $rec);

		// handle transfers, if any

		if ($post['xfer'] == 1) {

			$temp = $post['from_acct'];
			$post['from_acct'] = $post['to_acct'];
			$post['to_acct'] = $temp;
			$post['amount'] = - $post['amount'];

			$rec = $this->db->prepare('journal', $post);
			$this->db->insert('journal', $rec);
		}

		// handle splits as needed

		if ($post['split'] == 1 && $post['max_splits'] > 0) {

            // use journal ID, not transaction ID
            $jnlid = $this->db->lastid('journal');

			// $splits_amount = 0;
			// $check_amount = $post['amount']; // integer

			for ($k = 0; $k < $post['max_splits']; $k++) {
				if (empty($post['split_to_acct'][$k])) {
					emsg('F', 'Splits must have a valid to account');
					$this->db->rollback();
					return FALSE;
				}

				if (!empty($post['split_dr_amount'][$k])) {
					$amount = - dec2int($post['split_dr_amount'][$k]);
				}
				elseif (!empty($post['split_cr_amount'][$k])) {
					$amount = dec2int($post['split_cr_amount'][$k]);
				}

				$split = array(
					'jnlid' => $jnlid,
					'to_acct' => $post['split_to_acct'][$k],
					'memo' => $post['split_memo'][$k],
					'payee_id' => $post['split_payee_id'][$k],
					'amount' => $amount
				);
				// $splits_amount += $amount;
				$rec = $this->db->prepare('splits', $split);
				$this->db->insert('splits', $rec);
			} // for

			// if ($check_amount != $splits_amount) {
			// 	emsg('F', "Split amounts don't add up to transaction amount");
			//	$this->db->rollback();
		    //	return FALSE;
			// }

		} // if split

		$this->db->end();
		emsg('S', 'Transaction(s) stored.');
		return $post['txnid'];
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

	function get_names($from_acct = NULL, $payee_id = NULL, $to_acct = NULL)
	{
        if (!is_null($from_acct)) {
            $sql1 = "select name from accounts where id = $from_acct";
            $d = $this->db->query($sql1)->fetch();
            $rtn['from_acct_name'] = $d['name'] ?? '';
        }
        else {
            $rtn['from_acct_name'] = '';
        }

        if (!is_null($to_acct)) {
            $sql2 = "select name from accounts where id = $to_acct";
            $e = $this->db->query($sql2)->fetch();
            $rtn['to_acct_name'] = $e['name'] ?? '';
        }
        else {
            $rtn['to_acct_name'] = '';
        }

        if (!is_null($payee_id)) {
            $sql3 = "select name from payees where id = $payee_id";
            $f = $this->db->query($sql3)->fetch();
            $rtn['payee_name'] = $f['name'] ?? '';
        }
        else {
            $rtn['payee_name'] = '';
        }

		return $rtn;
	}

	function get_ccard_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type = 'R' AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}

	function get_split_names($payee_id, $to_acct)
	{
		$sql2 = "select name from accounts where id = $to_acct";
		$e = $this->db->query($sql2)->fetch();
		$rtn['split_to_name'] = $e['name'] ?? '';

		$sql3 = "select name from payees where id = $payee_id";
		$f = $this->db->query($sql3)->fetch();
		$rtn['split_payee_name'] = $f['name'] ?? '';

		return $rtn;
	}

	function get_from_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'R', 'S', 'L', 'Q') AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}
	
	function get_split_to_accounts()
	{
		// basic accounts, all of them
		$accounts = $this->get_accounts();
		$max_accounts = count($accounts);
		$split_to_accts = array();

		for ($i = 0; $i < $max_accounts; $i++) {

			if ($accounts[$i]['parent'] != 0 && 
				($accounts[$i]['acct_type'] != 'C' && 
				$accounts[$i]['acct_type'] != 'R' && 
				$accounts[$i]['acct_type'] != 'S' &&
				$accounts[$i]['acct_type'] != 'L')) {
					$split_to_accts[] = $accounts[$i];
			}
		}
		array_unshift($split_to_accts, array('id' => '0', 'name' => 'NONE', 'acct_type' => ' '));
		return $split_to_accts;
	}

}
