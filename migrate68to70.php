<?php

/* PROBLEM:
 *
 * Increase the sophistication of recurrences. Add "occ" field to
 * scheduled2 table and make it scheduled3. Elsewhere, we add code to use
 * the new field.
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

echo 'Migrating from 6.8 to 7.0<br/>';

$db->begin();

echo 'Creating and populating new scheduled3 table.<br/>';

// remove txn_dom, add freq, period

$sql = "CREATE TABLE scheduled3 (
    id integer primary key autoincrement, 
    from_acct integer not null references accounts(id), 
    freq integer default 1,
    period char not null,
    occ integer default 0,
    payee_id integer references payees(id), 
    to_acct integer references accounts(id), 
    memo varchar(35), 
    amount integer not null,
    last date)";

$db->query($sql);

// populate new table from old table

$sql = "SELECT * FROM scheduled2";
$oldrecs = $db->query($sql)->fetch_all();
foreach ($oldrecs as $old) {
    $rec = [
        'id' => $old['id'],
        'from_acct' => $old['from_acct'],
        'freq' => $old['freq'],
        'occ' => 0,
        'period' => $old['period'],
        'payee_id' => $old['payee_id'],
        'to_acct' => $old['to_acct'],
        'memo' => $old['memo'],
        'amount' => $old['amount'],
        'last' => $old['last']
    ];

    $db->insert('scheduled3', $rec);
}

$db->commit();

// SANITY CHECK

echo '<h3>Results:</h3>';

echo '<table>';
echo '<tr><th>Table</th><th>Old Records</th><th>New Records</th></tr>';
$tables = ['scheduled2', 'scheduled3'];
foreach ($tables as $table) {
    $old = $db->query("SELECT count(id) AS count FROM $table")->fetch();
    $new = $db->query("SELECT count(id) AS count FROM $table")->fetch();
    echo "<tr><td>$table</td><td>{$old['count']}</td><td>{$new['count']}</td></tr>";
}
echo '</table>';

echo 'Done.<br/>';

