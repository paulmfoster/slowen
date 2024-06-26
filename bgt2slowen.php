<?php

include 'init.php';

function get_acctnum_from_acctname($acctname)
{
    global $blines;

    $max = count($blines);
    for ($i = 0; $i < $max; $i++)
        if ($blines[$i]['acctname'] == $acctname)
            return $blines[$i]['id'];
    return 0;
}

echo 'Begin conversion.<br/>';

// Following table was in use at one time.
$sql = 'DROP TABLE IF EXISTS totals';
$db->query($sql);
$sql = 'DROP TABLE IF EXISTS history';
$db->query($sql);
$sql = 'DROP TABLE IF EXISTS staging';
$db->query($sql);
$sql = 'DROP TABLE IF EXISTS cells';
$db->query($sql);
$sql = 'DROP TABLE IF EXISTS blines';
$db->query($sql);

echo 'Building blines table.<br/>';
flush();

$sql = 'CREATE TABLE IF NOT EXISTS blines (
id INTEGER PRIMARY KEY AUTOINCREMENT,
acctname VARCHAR(20),
from_acct INTEGER NOT NULL DEFAULT 0,
payee_id INTEGER NOT NULL DEFAULT 0,
to_acct INTEGER NOT NULL DEFAULT 0,
period CHAR(1) NOT NULL,
typdue INTEGER NOT NULL DEFAULT 0)';
$db->query($sql);

echo 'Building cells table.<br/>';
flush();

$sql = 'CREATE TABLE IF NOT EXISTS cells ( 
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
acctnum INTEGER NOT NULL REFERENCES blines(id),
wedate date, 
wklysa INTEGER, 
priorsa INTEGER, 
addlsa INTEGER, 
paid INTEGER, 
newsa INTEGER)';
$db->query($sql);

echo 'Building staging table.<br/>';
flush();

$sql = 'CREATE TABLE IF NOT EXISTS staging ( 
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
acctnum INTEGER NOT NULL REFERENCES blines(id),
wedate date, 
wklysa INTEGER, 
priorsa INTEGER, 
addlsa INTEGER, 
paid INTEGER, 
newsa INTEGER)';
$db->query($sql);

echo 'Building history table.<br/>';
flush();

$sql = 'CREATE TABLE IF NOT EXISTS history ( 
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
acctname VARCHAR(20),
wedate date, 
wklysa INTEGER, 
priorsa INTEGER, 
addlsa INTEGER, 
paid INTEGER, 
newsa INTEGER)';
$db->query($sql);

echo 'Load old budget database.<br/>';
flush();

$file = '/var/www/html/bgtpers/app/data/budget.sq3';
if (!file_exists($file))
    exit("File ($file) doesn't exist! Aborting!");

$bgtdb = new database("sqlite:$file");

echo 'Populating blines table.<br/>';
flush();

$sql = 'SELECT * FROM cells';
$cells = $bgtdb->query($sql)->fetch_all();
$max = count($cells);

for ($i = 0; $i < $max; $i++) {
    if ($cells[$i]['accttype'] == 'P') {
        $bgt = [
            'acctname' => $cells[$i]['acctname'],
            'from_acct' => 0,
            'payee_id' => $cells[$i]['acctnum'],
            'to_acct' => 0,
            'period' => $cells[$i]['period'],
            'typdue' => $cells[$i]['typdue']
        ];
    }
    elseif ($cells[$i]['accttype'] == 'A') {
        $bgt = [
            'acctname' => $cells[$i]['acctname'],
            'from_acct' => 0,
            'payee_id' => 0,
            'to_acct' => $cells[$i]['acctnum'],
            'period' => $cells[$i]['period'],
            'typdue' => $cells[$i]['typdue']
        ];
    }
    elseif ($cells[$i]['accttype'] == 'C') {
        $bgt = [
            'acctname' => $cells[$i]['acctname'],
            'from_acct' => $cells[$i]['acctnum'],
            'payee_id' => 0,
            'to_acct' => 0,
            'period' => $cells[$i]['period'],
            'typdue' => $cells[$i]['typdue']
        ];
    }
    $db->insert('blines', $bgt);
}

echo 'Retrieving blines records for further processing.<br/>';
flush();

$sql = "SELECT * FROM blines";
$blines = $db->query($sql)->fetch_all();

echo 'Populating cells table.<br/>';
flush();

$sql = 'SELECT * FROM cells';
$cells = $bgtdb->query($sql)->fetch_all();
$max = count($cells);
for ($i = 0; $i < $max; $i++) {
    $acctnum = get_acctnum_from_acctname($cells[$i]['acctname']);
    $newcell['acctnum'] = $acctnum;
    $newcell['wedate'] = $cells[$i]['wedate'];
    $newcell['wklysa'] = $cells[$i]['wklysa'];
    $newcell['priorsa'] = $cells[$i]['priorsa'];
    $newcell['addlsa'] = $cells[$i]['addlsa'];
    $newcell['paid'] = $cells[$i]['paid'];
    $newcell['newsa'] = $cells[$i]['newsa'];
    $db->insert('cells', $newcell);
}

echo 'Populating history table.<br/>';
echo '... This may take a while.<br/>';
flush();

$sql = 'SELECT * FROM history';
$cells = $bgtdb->query($sql)->fetch_all();
$max = count($cells);
for ($i = 0; $i < $max; $i++) {
    $history['acctname'] = $cells[$i]['acctname'];
    $history['wedate'] = $cells[$i]['wedate'];
    $history['wklysa'] = $cells[$i]['wklysa'];
    $history['priorsa'] = $cells[$i]['priorsa'];
    $history['addlsa'] = $cells[$i]['addlsa'];
    $history['paid'] = $cells[$i]['paid'];
    $history['newsa'] = $cells[$i]['newsa'];
    $db->insert('history', $history);
}

echo 'Populating staging table.<br/>';
flush();

$sql = 'SELECT * FROM staging';
$cells = $bgtdb->query($sql)->fetch_all();
if ($cells != FALSE) {
    $max = count($cells);
    for ($i = 0; $i < $max; $i++) {
        $acctnum = get_acctnum_from_acctname($cells[$i]['acctname']);
        $newcell['acctnum'] = $acctnum;
        $newcell['wedate'] = $cells[$i]['wedate'];
        $newcell['wklysa'] = $cells[$i]['wklysa'];
        $newcell['priorsa'] = $cells[$i]['priorsa'];
        $newcell['addlsa'] = $cells[$i]['addlsa'];
        $newcell['paid'] = $cells[$i]['paid'];
        $newcell['newsa'] = $cells[$i]['newsa'];
        $db->insert('staging', $newcell);
    }
}

echo 'End conversion.<br/>';

