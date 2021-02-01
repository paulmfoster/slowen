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

}
