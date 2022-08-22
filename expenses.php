<?php

// This is designed to be called remotely and provide the expenditures for
// the week, according to Slowen. It's used for the Budget application.

// define system directories
define('SYSDIR', 'system/');
define('INCDIR', SYSDIR . 'includes/');
define('LIBDIR', SYSDIR . 'libraries/');

// define application directories
define('APPDIR', 'app/');
define('CFGDIR', APPDIR . 'config/');
define('DATADIR', APPDIR . 'data/');

// provide common utilities
include INCDIR . 'utils.inc.php';

$cfg = parse_ini_file(CFGDIR . 'config.ini');
$db = load('database', $cfg['dsn']);

$to_date = $_GET['to'] ?? NULL;
$from_date = $_GET['from'] ?? NULL;

if (is_null($from_date) || is_null($to_date)) {
    exit('');
}

$sql = "select journal.*, 
a.name as from_acct_name, 
b.name as to_acct_name, 
payees.name as payee_name from journal 
join accounts as a on a.id = journal.from_acct 
join accounts as b on b.id = journal.to_acct 
join payees on payees.id = journal.payee_id
where b.acct_type = 'E' 
and journal.txn_dt >= '$from_date' 
and journal.txn_dt <= '$to_date'
and status != 'V'  
order by to_acct_name";

$result = $db->query($sql)->fetch_all();
if ($result === FALSE) {
    exit('');
}

$json = json_encode($result);
echo $json;

