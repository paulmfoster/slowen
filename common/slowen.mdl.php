<?php

class slowen
{
	function __construct($db)
	{
		$this->db = $db;
	}

	function get_expenses($from_date, $to_date)
	{
		$sql = "select j.*, a.name, a.name as acctname, p.name as payeename from journal as j, accounts as a, payees as p where j.txn_dt >= '$from_date' and j.txn_dt <= '$to_date' and a.acct_id = j.to_acct and a.acct_type = 'E' and p.payee_id = j.payee_id order by j.to_acct";
		$result = $this->db->query($sql)->fetch_all();
		return $result;
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
	 * Get all data needed to display the balances of all bank accounts as of a given date
	 *
	 * @param string $last_dt If NULL, balance is from all transactions; else only those to selected date
	 *
	 * @return array Contains all relevant data for each account displayed.
	 */

	function get_balances($last_dt = NULL)
	{
		if (is_null($last_dt)) {
			$last_dt = pdate::get(pdate::now(), 'Y-m-d');
		}

		$accts = $this->get_from_accounts();
		$max_accts = count($accts);

		// should give us all totals, plus from_acct and account name
		// | from_acct | total | name |

		$sql = "SELECT DISTINCT from_acct, sum(amount) as total, accounts.name as name FROM journal, accounts WHERE txn_dt <= '$last_dt' and accounts.acct_id = journal.from_acct GROUP BY from_acct order by accounts.name";

		$balances = $this->db->query($sql)->fetch_all();

		if ($balances === FALSE) {
			return FALSE;
		}

		$max_bals = count($balances);

		$bals = array();
		for ($i = 0; $i < $max_accts; $i++) {
			for ($j = 0; $j < $max_bals; $j++) {
				if ($accts[$i]['acct_id'] == $balances[$j]['from_acct']) {
					$bals[] = array('name' => $accts[$i]['name'],
						'balance' => $balances[$j]['total'] + $accts[$i]['open_bal']);
					break;
				}
			}
		}

		return $bals;
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

		$sql1 = "select name from accounts where acct_id = $from_acct";
		$d = $this->db->query($sql1)->fetch();
		$rtn['from_acct_name'] = $d['name'] ? $d['name'] : '';

		$sql2 = "select name from accounts where acct_id = $to_acct";
		$e = $this->db->query($sql2)->fetch();
		$rtn['to_acct_name'] = $e['name'] ? $e['name'] : '';

		$sql3 = "select name from payees where payee_id = $payee_id";
		$f = $this->db->query($sql3)->fetch();
		$rtn['payee_name'] = $f['name'] ? $f['name'] : '';

		return $rtn;

	}

	// TRANSACTION METHODS ///////////////////////////////////

	function transactions_sql($where_clause)
	{
		$sql = "SELECT journal.*, payees.name AS payee_name, a3.name AS from_acct_name, a4.name AS to_acct_name FROM journal LEFT JOIN payees ON payees.payee_id = journal.payee_id LEFT JOIN accounts AS a3 ON a3.acct_id = journal.from_acct LEFT JOIN accounts AS a4 ON a4.acct_id = journal.to_acct WHERE $where_clause ORDER BY txn_dt, checkno, txnid";

		return $sql;
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
			
			$txns[$i]['x_txn_dt'] = pdate::reformat('Y-m-d', $txns[$i]['txn_dt'], 'm/d/y');

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

	function get_splits($txnid)
	{
		$sql = "SELECT s.*, p.name AS payee_name, a.name AS to_acct_name FROM splits AS s left JOIN payees AS p ON p.payee_id = s.payee_id LEFT JOIN accounts AS a ON a.acct_id = s.to_acct WHERE txnid = $txnid";
		$splits = $this->db->query($sql)->fetch_all();
		return $splits;
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
		$this->db->begin();

		// get next transaction ID
		$post['txnid'] = $this->get_next_txnid();

		// checkboxes, unchecked don't show up in $_POST

		if (!isset($post['xfer'])) {
			$post['xfer'] = 0;
		}

		if (!isset($post['split'])) {
			$post['split'] = 0;
		}

		// massage dates

		// $post['txn_dt'] = pdate::reformat($date_template, $post['txn_dt'], 'Y-m-d');

		// if (!empty($post['recon_dt'])) {
		//	$post['recon_dt'] = pdate::reformat($date_template, $post['recon_dt'], 'Y-m-d');
		// }

		// integerize amount

		if (!isset($post['status'])) {
			$post['status'] = ' ';
		}

		if ($post['status'] == 'V') {
			$post['amount'] = 0;
		}
		else {
			if (!empty($post['dr_amount'])) {
				$post['amount'] = - dec2int($post['dr_amount']);
			}

			if (!empty($post['cr_amount'])) {
				$post['amount'] = dec2int($post['cr_amount']);
			}
		}

		if ($post['split'] == 0) {

			if ($post['payee_id'] == '0') {
				emsg('F', 'Normal transactions must have a valid payee');
				return FALSE;
			}
		
			if ($post['to_acct'] == '0') {
				emsg('F','Normal transactions  must have a valid to account');
				return FALSE;
			}
		}

		if ($post['xfer'] == 1 && $post['to_acct'] == 0) {
			emsg('F', 'Transfers must have a valid to account');
			return FALSE;
		}

		if (!isset($post['checkno'])) {
			$post['checkno'] = '';
		}

		if (!isset($post['recon_dt'])) {
			$post['recon_dt'] = '';
		}

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

		if ($post['split'] && $post['max_splits'] > 0) {

			$check_amount = $post['amount'];

			for ($k = 0; $k < $post['max_splits']; $k++) {
				if ($post['split_to_acct'][$k] == 0) {
					emsg('F', 'Splits must have a valid to account');
					$this->db->rollback();
					return FALSE;
				}

				if (!empty($post['split_dr_amount'][$k])) {
					$amount = - $post['split_dr_amount'][$k];
				}
				elseif (!empty($post['split_cr_amount'][$k])) {
					$amount = $post['split_cr_amount'][$k];
				}

				$split = array(
					'txnid' => $post['txnid'],
					'to_acct' => $post['split_to_acct'][$k],
					'memo' => $post['split_memo'][$k],
					'payee_id' => $post['split_payee_id'][$k],
					'amount' => dec2int($amount)
				);
				$check_amount -= dec2int($amount);
				$rec = $this->db->prepare('splits', $split);
				$this->db->insert('splits', $rec);
			}

			if ($check_amount != 0) {
				emsg('F', "Split amounts don't add up to transaction amount");
				$this->db->rollback();
				return FALSE;
			}

		}

		$this->db->end();
		emsg('S', 'Transaction(s) stored.');
		return TRUE;
	}

	/**
	 * update_transaction()
	 *
	 * Saves edits from the transaction edit screen
	 * NOTE: This routine does not check user input; it simply stores
	 * the input.
	 *
	 * @param array $post Normally, the $_POST array
	 *
	 */

	function update_transaction($post)
	{
		global $date_template;

		$this->db->begin();

		if ($post['txntype'] == 'iaxfer') {
			for ($i = 0; $i < 2; $i++) {
				$txn_dt = $post['txn_dt'][$i];
				$from_acct = $post['from_acct'][$i];
				$rec = array(
					'txn_dt' => $txn_dt,
					'checkno' => $post['checkno'][$i],
					'payee_id' => $post['payee_id'][$i],
					'memo' => $post['memo'][$i]
				);
				$prec = $this->db->prepare('journal', $rec);
				$this->db->update('journal', $prec, "id = {$post['iaxid'][$i]}");
			}
		}

		if ($post['txntype'] == 'single' || $post['txntype'] == 'splits') {
			// $post['txn_dt'] = pdate::reformat($date_template, $post['txn_dt'], 'Y-m-d');
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

	// PAYEE METHODS /////////////////////////////////////////

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
	
	function get_payee($payee_id)
	{
		$sql = "SELECT * FROM payees WHERE payee_id = $payee_id";
		$result = $this->db->query($sql)->fetch();
		if ($result === FALSE) {
			return $FALSE;
		}
		return $result;
	}

	function get_payee_name($payee_id)
	{
		$sql = "SELECT name FROM payees WHERE payee_id = $payee_id";
		$rec = $this->db->query($sql)->fetch();
		return $rec['name'];
	}

	function add_payee($name)
	{
		$sql = 'SELECT max(payee_id) as lastid FROM payees';
		$rec = $this->db->query($sql)->fetch();
		$this->db->insert('payees', array('payee_id' => $rec['lastid'] + 1, 'name' => $name));
		emsg('S', 'Payee added');
		return TRUE;
	}

	function update_payee($payee_id, $name)
	{
		// real payee?
		if ($this->get_payee($payee_id) === FALSE) {
			emsg('F', "Cannot edit non-existent payee");
			return FALSE;
		}

		$rec = $this->db->prepare('payees', array('name' => $name));
		$this->db->update('payees', $rec, "payee_id = $payee_id");
		emsg('S', 'Payee updated');
		return TRUE;
	}

	function delete_payee($payee_id)
	{
		if ($this->get_payee($payee_id) === FALSE) {
			emsg('F', "Attempt to delete non-existent payee. Aborted.");
			return FALSE;
		}

		// is this payee in use?
		$sql = "SELECT id FROM journal WHERE payee_id = $payee_id";
		if ($this->db->query($sql)->fetch()) {
			emsg('F', "Payee is linked to transactions. Aborted");
			return FALSE;
		}
			
		$this->db->delete('payees', "payee_id = $payee_id");
		emsg('S', 'Payee deleted');
		return TRUE;
	}

	// ACCOUNT METHODS ///////////////////////////////////////

	function get_bank_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'S') AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}

	function get_ccard_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type = 'R' AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}

	function get_from_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'R', 'S', 'L', 'Q') AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}
	
	function get_to_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE parent != 0 ORDER BY lower(name)";
		$to_accts = $this->db->query($sql)->fetch_all();
		array_unshift($to_accts, array('acct_id' => '0', 'name' => 'NONE', 'acct_type' => ' '));
		return $to_accts;
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
		array_unshift($split_to_accts, array('id' => '0', 'name' => 'NONE', 'acct_type' => ' ', 'acct_id' => 0));
		return $split_to_accts;
	}

	/**
	 * Get all details on accounts like bank, checking and credit card accounts
	 *
	 * @return array Indexed array of 0 = number of accounts, 1 = array of account records
	 */
	
	function get_bank_accts()
	{
		$sql = "SELECT * from accounts WHERE acct_type IN ('R', 'C', 'S', 'L', 'Q') AND parent != 0 ORDER BY name";
		$accts = $this->db->query($sql)->fetch_all();

		return $accts;
	}

	function get_open_bal($acct_id)
	{
		$sql = "SELECT open_bal FROM accounts WHERE acct_id = $acct_id";
		$rec = $this->db->query($sql)->fetch();
		return $rec['open_bal'];
	}

	function get_parents()
	{
		$sql = "SELECT acct_id, name FROM accounts ORDER BY lower(name)";
		$results = $this->db->query($sql)->fetch_all();
		return $results;
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

	function get_checking_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type = 'C' ORDER BY lower(name)";
		$accounts = $this->db->query($sql)->fetch_all();

		return $accounts;
	}

	function get_expense_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type = 'E' ORDER BY lower(name)";
		$accounts = $this->db->query($sql)->fetch_all();

		return $accounts;
	}

	function get_income_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type = 'I' ORDER BY lower(name)";
		$accounts = $this->db->query($sql)->fetch_all();

		return $accounts;
	}

	function get_acct_name($acct_id)
	{
		$sql = "SELECT name FROM accounts WHERE acct_id = $acct_id";
		$rec = $this->db->query($sql)->fetch();
		return $rec['name'];
	}

	function add_account($post)
	{
		global $date_template;

		if (!empty($post['rec_bal'])) {
			$post['rec_bal'] = dec2int($post['rec_bal']);
		}
		else {
			$post['rec_bal'] = 0;
		}

		if (!empty($post['open_bal'])) {
			$post['open_bal'] = dec2int($post['open_bal']);
		}
		else {
			$post['open_bal'] = 0;
		}

		// must have a new acct_id
		$sql = "SELECT max(acct_id) as last_id FROM accounts";
		$ai = $this->db->query($sql)->fetch();
		$post['acct_id'] = $ai['last_id'] + 1;

		$rec = $this->db->prepare('accounts', $post);
		$this->db->insert('accounts', $rec);
		emsg('S', 'New account has been saved');
		return TRUE;
	}

	function get_account($acct_id)
	{
		global $acct_types;

		$sql = "SELECT a1.*, a2.name as x_parent FROM accounts as a1 left join accounts as a2 on a2.acct_id = a1.parent WHERE a1.acct_id = $acct_id ORDER BY lower(a1.name)";
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

	function update_account($post)
	{
		global $date_template;

		if ($this->get_account($post['acct_id']) === FALSE) {
			emsg('F', "Cannot edit non-existent account");
			return FALSE;
		}

		$post['open_bal'] = dec2int($post['open_bal']);
		$post['rec_bal'] = dec2int($post['rec_bal']);

		$prec = $this->db->prepare('accounts', $post);
		$this->db->update('accounts', $prec, "acct_id = {$post['acct_id']}");

		return TRUE;
	}

	function delete_account($acct_id)
	{
		if ($this->get_account($acct_id) === FALSE) {
			emsg('F', "Cannot delete non-existent account");
			return FALSE;
		}

		// is this account in use?
		$sql = "SELECT id FROM journal WHERE from_acct = $acct_id OR to_acct = $acct_id";
		if ($this->db->query($sql)->fetch()) {
			emsg('F', "Account is linked to transactions. Aborted");
			return FALSE;
		}

		$this->db->delete('accounts', "acct_id = $acct_id");
		emsg('S', 'Account deleted');
		return TRUE;
	}

	// ARCHIVE METHODS ///////////////////////////////////////


	// AUDIT METHODS /////////////////////////////////////////

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
		$accts = $this->get_bank_accts();
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
		$smax = count($s);

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

		$x_stmt_end_bal = dec2int($stmt_end_bal);
		$check_bal = $x_stmt_end_bal + $comp_uncleared_txns;
	
		$difference = $comp_end_bal - $check_bal;

		if ($difference == 0) {
			return TRUE;
		}

		$data = array(
			'from_acct_name' => $acct['name'],
			'comp_start_bal' => int2dec($comp_start_bal),
			'comp_all_txns' => int2dec($comp_all_txns),
			'comp_end_bal' => int2dec($comp_end_bal),
			'stmt_start_bal' => $stmt_start_bal,
			'stmt_end_bal' => $stmt_end_bal,
			'comp_uncleared_txns' => int2dec($comp_uncleared_txns),
			'check_bal' => int2dec($check_bal),
			'difference' => int2dec(abs($comp_end_bal - $check_bal))
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

	/**
	 * get_transaction()
	 *
	 * Get a transaction, based on txnid, including splits.
	 * Massage values which aren't really human readable.
	 *
	 * @param integer $txnid
	 *
	 * @return array One or more records representing the same txnid
	 *
	 */

	function get_transaction($txnid)
	{
		global $statuses;

		$sql = "select journal.*, payees.name as payee_name, a3.name as from_acct_name, a4.name as to_acct_name from journal left join payees on journal.payee_id = payees.payee_id left join accounts as a3 on journal.from_acct = a3.acct_id left join accounts as a4 on journal.to_acct = a4.acct_id where journal.txnid = $txnid order by journal.id";

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
			$txns[$i]['x_txn_dt'] = pdate::reformat('Y-m-d', $txns[$i]['txn_dt'], 'm/d/y');
			if (!empty($txns[$i]['recon_dt'])) {
				$txns[$i]['x_recon_dt'] = pdate::reformat('Y-m-d', $txns[$i]['recon_dt'], 'm/d/y');
			}
			else {
				$txns[$i]['x_recon_dt'] = '';
			}
			$txns[$i]['x_status'] = $statuses[$txns[$i]['status']];
		}

		return $txns;
	}

	function version()
	{
		return 2.1;
	}
}
