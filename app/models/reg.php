<?php

class reg
{
    function __construct($db)
    {
        $this->db = $db;
    }

	function get_from_accounts()
	{
		$sql = "SELECT * FROM accounts WHERE acct_type IN ('C', 'R', 'S', 'L') AND parent != 0 ORDER BY lower(name)";
		$from_accts = $this->db->query($sql)->fetch_all();
		return $from_accts;
	}
}

