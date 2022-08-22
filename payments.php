<?php

// This is designed to be called remotely and provide the payments to
// credit card and credit accounts. For use by the Budget app.

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

// transactions:
// where txn_dt between from and to dates
// where to_acct is a credit card
// where transaction is a credit

$sql = "select journal.* from journal, accounts 
where txn_dt >= '$from_date' 
and txn_dt <= '$to_date' 
and amount > 0 
and journal.from_acct = accounts.id 
and accounts.acct_type = 'R'
and status != 'V'";

$result = $db->query($sql)->fetch_all();

if ($result === FALSE) {
    exit('');
}

$json = json_encode($result);
echo $json;

