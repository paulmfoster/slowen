<?php

class payee
{
    public $db;

	function __construct($db)
	{
		$this->db = $db;
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
	
	function get_payee($id)
	{
		$sql = "SELECT * FROM payees WHERE id = $id";
		$result = $this->db->query($sql)->fetch();
		if ($result === FALSE) {
			return $FALSE;
		}
		return $result;
	}

	function get_payee_name($id)
	{
		$sql = "SELECT name FROM payees WHERE id = $id";
		$rec = $this->db->query($sql)->fetch();
		return $rec['name'];
	}

	function add_payee($name)
	{
		$sql = 'SELECT max(id) as lastid FROM payees';
		$rec = $this->db->query($sql)->fetch();
		$this->db->insert('payees', array('id' => $rec['lastid'] + 1, 'name' => $name));
		emsg('S', 'Payee added');
		return TRUE;
	}

	function update_payee($id, $name)
	{
		// real payee?
		if ($this->get_payee($id) === FALSE) {
			emsg('F', "Cannot edit non-existent payee");
			return FALSE;
		}

		$rec = $this->db->prepare('payees', array('name' => $name));
		$this->db->update('payees', $rec, "id = $id");
		emsg('S', 'Payee updated');
		return TRUE;
	}

	function delete_payee($id)
	{
		if ($this->get_payee($id) === FALSE) {
			emsg('F', "Attempt to delete non-existent payee. Aborted.");
			return FALSE;
		}

		// is this payee in use?
		$sql = "SELECT id FROM journal WHERE payee_id = $id";
		if ($this->db->query($sql)->fetch()) {
			emsg('F', "Payee is linked to transactions. Aborted");
			return FALSE;
		}
			
		$this->db->delete('payees', "id = $id");
		emsg('S', 'Payee deleted');
		return TRUE;
	}
}
