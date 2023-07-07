<?php

/* PROBLEM:
 *
 * Some recurring payments don't recur on the same day every month.
 * Instead, they may occur every two or three months, etc. But the system,
 * in particular the "scheduled" table, isn't set up to handle this. This
 * script creates a revised "scheduled2" table to hold the new records.
 *
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

echo 'Migrating from 6.5 to 6.6<br/>';

$db->begin();

echo 'Creating and populating new scheduled2 table.<br/>';

// remove txn_dom, add freq, period

$sql = "CREATE TABLE scheduled2 (
    id integer primary key autoincrement, 
    from_acct integer not null references accounts(id), 
    freq integer not null,
    period char not null,
    payee_id integer references payees(id), 
    to_acct integer references accounts(id), 
    memo varchar(35), 
    amount integer not null,
    last date)";

$db->query($sql);

// populate new table from old table

$sql = "SELECT * FROM scheduled";
$oldrecs = $db->query($sql)->fetch_all();
foreach ($oldrecs as $old) {
    $rec = [
        'id' => $old['id'],
        'from_acct' => $old['from_acct'],
        'freq' => 1,
        'period' => 'M',
        'payee_id' => $old['payee_id'],
        'to_acct' => $old['to_acct'],
        'memo' => $old['memo'],
        'amount' => $old['amount'],
        'last' => $old['last']
    ];

    $db->insert('scheduled2', $rec);
}

$db->commit();

// SANITY CHECK

echo '<h3>Results:</h3>';

echo '<table>';
echo '<tr><th>Table</th><th>Old Records</th><th>New Records</th></tr>';
$tables = ['scheduled', 'scheduled2'];
foreach ($tables as $table) {
    $old = $db->query("SELECT count(id) AS count FROM $table")->fetch();
    $new = $db->query("SELECT count(id) AS count FROM $table")->fetch();
    echo "<tr><td>$table</td><td>{$old['count']}</td><td>{$new['count']}</td></tr>";
}
echo '</table>';

echo 'Done.<br/>';

