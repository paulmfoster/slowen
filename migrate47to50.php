<?php

echo "Converting version 4.7 to 5.0<br/>";

$sql = "CREATE TABLE scheduled (id integer primary key autoincrement, from_acct integer not null references accounts(acct_id), txn_dom integer not null, payee_id integer references payees(payee_id), to_acct integer references accounts(acct_id), memo varchar(35), amount integer not null)";

$cfg = parse_ini_file('config/config.ini');
include $cfg['libdir'] . 'database.lib.php';

foreach ($cfg['entity_data'] as $dbdata) {
	$cfg['dbdata'] = $dbdata;
	$db = new database($cfg);
	$db->query($sql);
}

echo "Done.";

