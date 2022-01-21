<?php

class entity
{
	function __construct($db)
	{
		$this->db = $db;
	}

	function add_entity($post)
	{
		global $cfg;

		$cfg['dbdata'] = $cfg['datadir'] . $cfg['app_nick'] . $post['number'] . '.sq3';
		// the entadd.php script should already prevent this, but just in
		// case...
		if (file_exists($cfg['dbdata'])) {
			emsg('F', 'A database file already exists for that entity. Aborting.');
			return;
		}
		
		$ndb = new database($cfg);
		coldstart($ndb);

		// update config file
		$config = file_get_contents('config/config.ini');
		$config .= "\nentity[{$post['number']}] = \"{$post['name']}\"";
		file_put_contents('config/config.ini', $config);

		emsg('S', 'Database created for new entity.');
		return;
	}
}

