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

		if (!file_exists('coldstart.sql')) {
			emsg('F', "Selected entity needs the file 'coldstart.sql' to start, and it's missing.");
			redirect('index.php');
		}

		$ncfg = [
			'libdir' => $cfg['libdir'],
			'dbdriv' => 'SQLite3',
			'dbdata' => $cfg['app_nick'] . $post['number'] . '.sq3'
		];
		
		$ndb = new database($ncfg);

		$lines = file('coldstart.sql', FILE_IGNORE_NEW_LINES);
		foreach ($lines as $line) {
			$ndb->query($line);
		}

		$config = file_get_contents('config/config.ini');
		$config .= "\nentity[{$post['number']}] = {$post['name']}";
		file_put_contents('config/config.ini', $config);

		emsg('S', 'Database created for new entity.');
	}
}

