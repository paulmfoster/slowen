<?php

class report
{
	function __construct($db)
	{
		$this->db = $db;
	}

	function transactions_sql($where_clause)
	{
		$sql = "SELECT journal.*, payees.name AS payee_name, a3.name AS from_acct_name, a4.name AS to_acct_name FROM journal LEFT JOIN payees ON payees.id = journal.payee_id LEFT JOIN accounts AS a3 ON a3.id = journal.from_acct LEFT JOIN accounts AS a4 ON a4.id = journal.to_acct WHERE $where_clause ORDER BY txn_dt, checkno, txnid";

		return $sql;
	}

	/**
	 *
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
				$txns[$i]['debit'] = int2dec(abs($txns[$i]['amount']));
				$txns[$i]['credit'] = '';
			}
			else {
				$txns[$i]['credit'] = int2dec($txns[$i]['amount']);
				$txns[$i]['debit'] = '';
			}

			if ($type == 'F') {
				$end_bal += $txns[$i]['amount'];
				$txns[$i]['balance'] = int2dec($end_bal);
			}
		}

		return $txns;
		
	}

	/**
	 * Get income and expense accounts for budget query.
	 *
	 * @return array Income/Expense accounts
	 */

	function get_budget_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type in ('I', 'E') ORDER BY acct_type, name";
		$accts = $this->db->query($sql)->fetch_all();
		return $accts;
	}

	/**
	 * Get income/expenses for a given category and from/to dates
	 *
	 * @param date $from The FROM date
	 * @param date $to The TO date
	 * @param integer $category The category in question
	 *
	 * @return array Transactions and total
	 */

	function budget($from, $to, $category)
	{
		$where = "txn_dt >= '$from' AND txn_dt <= '$to' AND to_acct = $category AND status != 'V'";
		$sql = $this->transactions_sql($where);
		$txns = $this->db->query($sql)->fetch_all();
		$max = count($txns);
		$balance = 0;
		for ($i = 0; $i < $max; $i++) {
			/*
			if ($txns[$i]['amount'] < 0) {
				$txns[$i]['debit'] = - $txns[$i]['amount'];
				$txns[$i]['credit'] = '';
			}
			elseif ($txns[$i]['amount'] > 0) {
				$txns[$i]['credit'] = $txns[$i]['amount'];
				$txns[$i]['debit'] = '';
			}
			 */
			$balance += $txns[$i]['amount'];
		}
		return [$txns, $balance];
	}

	/**
	 *
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

	/**
	 *
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
	 * Get all data needed to display the balances of all bank accounts as of a given date
	 *
	 * @param string $last_dt If NULL, balance is from all transactions; else only those to selected date
	 *
	 * @return array Contains all relevant data for each account displayed.
	 */

	function get_balances($last_dt = NULL)
	{
		if (is_null($last_dt)) {
			$last_dt = pdate::now2iso();
		}

		$accts = $this->get_from_accounts();
		$max_accts = count($accts);

		// should give us all totals, plus from_acct and account name
		// | from_acct | total | name |

		$sql = "SELECT DISTINCT from_acct, sum(amount) as total, accounts.name as name FROM journal, accounts WHERE txn_dt <= '$last_dt' and accounts.id = journal.from_acct GROUP BY from_acct order by accounts.name";

		$balances = $this->db->query($sql)->fetch_all();

		if ($balances === FALSE) {
			return FALSE;
		}

		$max_bals = count($balances);

		$bals = array();
		for ($i = 0; $i < $max_accts; $i++) {
			for ($j = 0; $j < $max_bals; $j++) {
				if ($accts[$i]['id'] == $balances[$j]['from_acct']) {
					$bals[] = array('name' => $accts[$i]['name'],
						'balance' => $balances[$j]['total'] + $accts[$i]['open_bal']);
					break;
				}
			}
		}

		return $bals;
	}

	function get_from_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'R', 'S', 'L', 'Q') AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}
	
	function get_expenses($from_date, $to_date)
	{
        $sql = "select j.*, 
            a.name as from_acct_name, 
            b.name as to_acct_name, 
            p.name as payee_name 
            from journal as j 
            join accounts as a on (a.id = j.from_acct) 
            join accounts as b on (b.id = j.to_acct) 
            join payees as p on (p.id = j.payee_id) 
            where b.acct_type = 'E' 
            and j.txn_dt >= '$from_date' 
            and j.txn_dt <= '$to_date' 
            order by to_acct_name";
		$result = $this->db->query($sql)->fetch_all();

        if ($result === FALSE) {
            return FALSE;
        }

        /*
        // fixme: is the following needed?
		// add from_acct name
		$sql = "SELECT id, name FROM accounts WHERE acct_type in ('C', 'R', 'S')";
		$accts = $this->db->query($sql)->fetch_all();

		$nres = count($result);
		for ($i = 0; $i < $nres; $i++) {
			foreach ($accts as $acct) {
				if ($result[$i]['from_acct'] == $acct['id']) {
					$result[$i]['fromname'] = $acct['name'];
					break;
				}
			}
		}
         */

		return $result;
	}
}
