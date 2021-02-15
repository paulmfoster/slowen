<?php

class account
{
	function __construct($db)
	{
		$this->db = $db;
	}

	function get_from_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'R', 'S', 'L', 'Q') AND parent != 0 ORDER BY lower(name)";
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

	function get_parents()
	{
		$sql = "SELECT acct_id, name FROM accounts ORDER BY lower(name)";
		$results = $this->db->query($sql)->fetch_all();
		return $results;
	}

	function get_account($acct_id)
	{
		$sql = "SELECT a1.*, a2.name as x_parent FROM accounts as a1 left join accounts as a2 on a2.acct_id = a1.parent WHERE a1.acct_id = $acct_id ORDER BY lower(a1.name)";
		$acct = $this->db->query($sql)->fetch();

		if ($acct === FALSE) {
			return FALSE;
		}

		return $acct;
	}

	function update_account($post)
	{
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
		// is it a real account
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

		// don't allow things like "Expense", "Income", "Asset" to be
		// deleted
		$sql = "SELECT parent FROM accounts WHERE acct_id = $acct_id";
		$result = $this->db->query($sql)->fetch();
		if ($result['parent'] == 0) {
			emsg('F', "This account is foundational, and cannot be deleted.");
			return FALSE;
		}

		// no accounts which have children
		$sql = "SELECT acct_id FROM accounts WHERE parent = $acct_id";
		$result = $this->db->query($sql)->fetch_all();
		if ($result !== FALSE) {
			emsg('F', "This account has child accounts. Deletion aborted.");
			return FALSE;
		}

		$this->db->delete('accounts', "acct_id = $acct_id");
		emsg('S', 'Account deleted');
		return TRUE;
	}

	function add_account($post)
	{
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

}
