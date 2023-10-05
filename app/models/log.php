<?php

class log
{
    public $db;

	function __construct($db)
	{
        $this->db = $db;
	}

    function getrecs()
    {
        $sql = "SELECT * FROM sqllog ORDER BY timestamp";
        $recs = $this->db->query($sql)->fetch_all();
        return $recs;
    }

    // remove all records older than 30 days
    function purge()
    {
        $dt = new xdate();
        $now = $dt->add_days(-30);
        $nowstr = $dt->to_iso() . ' 00:00:00';

        $sql = "DELETE FROM sqllog WHERE timestamp < '$nowstr'";
        $this->db->query($sql);

        return TRUE;
    }

}
