<?php

include 'init.php';

$report = model('report', $db);

$dt = new xdate();
if (!empty($_POST['last_dt'])) {
    $dt->from_iso($_POST['last_dt']);
    $today = $dt->to_amer();
    $bals = $report->get_balances($_POST['last_dt']);
}
else {
    $today = $dt->to_amer();
    $bals = $report->get_balances();
}

if ($bals === FALSE) {
    emsg('F', 'Date is too early to show balances');
    redirect('balances.php');
}
else {
    $nbals = count($bals);
}

$d = [
    'today' => $today,
    'nbals' => $nbals,
    'bals' => $bals
];

$page_title = 'Balances';
include VIEWDIR . 'balshow.view.php';

