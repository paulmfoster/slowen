<?php

/* PROBLEM:
 *
 * Some queries from Slowen are coming back with "foreign key mismatch"
 * errors. These come from foreign key references where the parent key is
 * not the primary key of the parent table. For example, the accounts table
 * is used as a parent table, and its acct_id field is used as the parent
 * key. But it's not the primary key for that table. This is true for a
 * number of tables. To correct this, I built this script. It creates a new
 * database, with corrected table schemas, and populates it with the old
 * records from the original database.
 */

// define system directories
define('SYSDIR', 'system/');
define('INCDIR', SYSDIR . 'includes/');
define('LIBDIR', SYSDIR . 'libraries/');

// define application directories
define('APPDIR', 'app/');
define('CFGDIR', APPDIR . 'config/');
define('PRINTDIR', APPDIR . 'printq/');
define('IMGDIR', APPDIR . 'images/');
define('DATADIR', APPDIR . 'data/');
define('MODELDIR', APPDIR . 'models/');
define('VIEWDIR', APPDIR . 'views/');
define('CTLDIR', APPDIR . 'controllers/');

$cfg = parse_ini_file(CFGDIR . 'config.ini');
include LIBDIR . 'database.lib.php';

$db = new database($cfg['dsn']);

$newname = 'app/data/slowen6.sq3';
unlink($newname);
$newdsn = 'sqlite:' . $newname;
$newdb = new database($newdsn);

echo 'Migrating from 5.3 to 6.0<br/>';

$newdb->begin();

echo 'Creating and populating new accounts table.<br/>';

// remove acct_id field, replace with id field
$sql = "CREATE TABLE accounts (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    parent INTEGER NOT NULL DEFAULT 0, 
    lft INTEGER, 
    rgt INTEGER, 
    open_dt DATE, 
    recon_dt DATE, 
    acct_type CHAR(1) NOT NULL, 
    name VARCHAR(35) NOT NULL, 
    descrip VARCHAR(50), 
    open_bal INTEGER DEFAULT 0, 
    rec_bal INTEGER DEFAULT 0)";
$newdb->query($sql);

$sql = "SELECT * FROM accounts ORDER BY acct_id";
$accts = $db->query($sql)->fetch_all();
foreach ($accts as $acct) {
    $rec = [
        'id' => $acct['acct_id'],
        'parent' => $acct['parent'],
        'lft' => $acct['lft'],
        'rgt' => $acct['rgt'],
        'open_dt' => $acct['open_dt'],
        'recon_dt' => $acct['recon_dt'],
        'acct_type' => $acct['acct_type'],
        'name' => $acct['name'],
        'descrip' => $acct['descrip'],
        'open_bal' => $acct['open_bal'],
        'rec_bal' => $acct['rec_bal']
    ];
    $newdb->insert('accounts', $rec);
}

echo 'Creating and populating new payees table.<br/>';

// omit payee_id field and replace with id field
$sql = "CREATE TABLE payees (
    id integer primary key autoincrement, 
    name varchar(35) not null)";
$newdb->query($sql);

$sql = "SELECT * FROM payees ORDER BY payee_id";
$payees = $db->query($sql)->fetch_all();
foreach ($payees as $payee) {
    $rec = [
        'id' => $payee['payee_id'],
        'name' => $payee['name']
    ];
    $newdb->insert('payees', $rec);
}

echo 'Creating and populating journal table.<br/>';

// add foreign key on from_acct
// no need for actual change
$sql = "CREATE TABLE journal (
    id integer primary key autoincrement, 
    txnid integer not null, 
    from_acct integer not null references accounts(id), 
    txn_dt date not null, 
    checkno varchar(12), 
    split boolean default 0, 
    payee_id integer, 
    to_acct integer, 
    memo varchar(35), 
    status char(1) not null default ' ', 
    recon_dt date, 
    amount integer not null)";
$newdb->query($sql);

$sql = "SELECT * FROM journal ORDER BY id";
$jnls = $db->query($sql)->fetch_all();
foreach ($jnls as $jnl) {
    $rec = [
        'id' => $jnl['id'],
        'txnid' => $jnl['txnid'],
        'from_acct' => $jnl['from_acct'],
        'txn_dt' => $jnl['txn_dt'],
        'checkno' => $jnl['checkno'],
        'split' => $jnl['split'],
        'payee_id' => $jnl['payee_id'],
        'to_acct' => $jnl['to_acct'],
        'memo' => $jnl['memo'],
        'status' => $jnl['status'],
        'recon_dt' => $jnl['recon_dt'],
        'amount' => $jnl['amount']
    ];
    $newdb->insert('journal', $rec);
}

echo 'Creating and populating recon table.<br/>';

$sql = "CREATE TABLE recon (
    id integer primary key autoincrement, 
    from_acct integer, 
    stmt_start_bal integer, 
    stmt_end_bal integer, 
    stmt_close_date date)";
$newdb->query($sql);

$sql = "SELECT * FROM recon ORDER BY id";
$recons = $db->query($sql)->fetch_all();
foreach ($recons as $recon) {
    $rec = [
        'id' => $recon['id'],
        'from_acct' => $recon['from_acct'],
        'stmt_start_bal' => $recon['stmt_start_bal'],
        'stmt_end_bal' => $recon['stmt_end_bal'],
        'stmt_close_date' => $recon['stmt_close_date']
    ];
    $newdb->insert('recon', $rec);
}

echo 'Creating and populating new scheduled table.<br/>';

// changes to referenced fields
// no change to contents
$sql = "CREATE TABLE scheduled (
    id integer primary key autoincrement, 
    from_acct integer not null references accounts(id), 
    txn_dom integer not null, 
    payee_id integer references payees(id), 
    to_acct integer references accounts(id), 
    memo varchar(35), 
    amount integer not null,
    last date)";
$newdb->query($sql);

$sql = "SELECT * FROM scheduled ORDER BY id";
$scheds = $db->query($sql)->fetch_all();
foreach ($scheds as $sched) {
    $rec = [
        'id' => $sched['id'],
        'from_acct' => $sched['from_acct'],
        'txn_dom' => $sched['txn_dom'],
        'payee_id' => $sched['payee_id'],
        'to_acct' => $sched['to_acct'],
        'memo' => $sched['memo'],
        'amount' => $sched['amount']
    ];
    $newdb->insert('scheduled', $rec);
}

echo 'Creating and populating new splits table.<br/>';

// change to referenced fields
// no change to contents
$sql = "CREATE TABLE splits (
    id integer primary key autoincrement, 
    jnlid integer not null references journal(id), 
    to_acct integer not null references accounts(id), 
    memo varchar(35), 
    payee_id integer references payees(id), 
    amount integer not null)";
$newdb->query($sql);

$sql = "SELECT * FROM splits ORDER BY id";
$splits = $db->query($sql)->fetch_all();
foreach ($splits as $split) {
    $sql = "SELECT id FROM journal WHERE txnid = {$split['txnid']}";
    $journal = $db->query($sql)->fetch();
    $rec = [
        'id' => $split['id'],
        'jnlid' => $journal['id'],
        'to_acct' => $split['to_acct'],
        'memo' => $split['memo'],
        'payee_id' => $split['payee_id'],
        'amount' => $split['amount']
    ];
    $newdb->insert('splits', $rec);
}

echo 'Creating and populating journal_ndx index.<br/>';

$sql = "CREATE INDEX journal_ndx on journal (
    from_acct, 
    txn_dt, 
    checkno, 
    txnid)";
$newdb->query($sql);

$newdb->commit();

// SANITY CHECK

echo '<h3>Results:</h3>';

echo '<table>';
echo '<tr><th>Table</th><th>Old Records</th><th>New Records</th></tr>';
$tables = ['accounts', 'journal', 'payees', 'recon', 'scheduled', 'splits'];
foreach ($tables as $table) {
    $old = $db->query("SELECT count(id) AS count FROM $table")->fetch();
    $new = $newdb->query("SELECT count(id) AS count FROM $table")->fetch();
    echo "<tr><td>$table</td><td>{$old['count']}</td><td>{$new['count']}</td></tr>";
}
echo '</table>';

echo 'Done.<br/>';

