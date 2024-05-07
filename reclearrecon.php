<?php

// NOTE: Here we continue a reconciliation, clearing transactions.

include 'init.php';

$from_acct = $_GET['from_acct'] ?? NULL;
if (is_null($from_acct))
    redirect('prerecon.php');

$reconcile = model('reconcile', $db);

$continue = $_POST['continue'] ?? 0;

if ($continue == 0) {
    $reconcile->clear_saved_work($_POST['from_acct']);
    redirect('prerecon.php');
}

$acct = $reconcile->get_account($_POST['from_acct']);

$saved = $reconcile->get_saved_work($_POST['from_acct']);
// clear saved work; we're now in the middle of reconciliation again
$reconcile->clear_saved_work($_POST['from_acct']);

$saved['stmt_start_bal'] = int2dec($saved['stmt_start_bal']);
$saved['stmt_end_bal'] = int2dec($saved['stmt_end_bal']);

$from_acct = $acct['id'];
$from_acct_name = $acct['name'];

$stmt_start_bal = $saved['stmt_start_bal'];
$stmt_end_bal = $saved['stmt_end_bal'];
$stmt_close_date = $saved['stmt_close_date'];

$txns = $reconcile->get_uncleared_transactions($_POST['from_acct']);

// hidden fields...
$fields = array(
    'from_acct' => array(
        'name' => 'from_acct',
        'type' => 'hidden',
        'value' => $from_acct
    ),
    'stmt_start_bal' => array(
        'name' => 'stmt_start_bal',
        'type' => 'hidden',
        'value' => $stmt_start_bal
    ),
    'stmt_end_bal' => array(
        'name' => 'stmt_end_bal',
        'type' => 'hidden',
        'value' => $stmt_end_bal
    ),
    'stmt_close_date' => array(
        'name' => 'stmt_close_date',
        'type' => 'hidden',
        'value' => $stmt_close_date
    ),
    'from_acct_name' => array(
        'name' => 'from_acct_name',
        'type' => 'hidden',
        'value' => $from_acct_name
    ),
    's3' => array(
        'name' => 's3',
        'type' => 'submit',
        'value' => 'Continue'
    )
);

$form->set($fields);
$page_title = 'Continue Reconciliation';
$return = 'finrecon.php';
include VIEWDIR . 'reconlist.view.php';

