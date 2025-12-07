<?php

include 'init.php';
$bg = model('budget', $db);

if (isset($_POST['restart1']) || isset($_POST['restart2'])) {
    list($wedate, $hr_wedate, $cells, $totals) = $bg->restart();
    emsg('S', 'Budget has been RESTARTED');
}
elseif (isset($_POST['recalc1']) || isset($_POST['recalc2'])) {
    $cells = $bg->post2cells($_POST);
    $cells = $bg->recalculate($cells);
    $totals = $bg->get_totals($cells);
    $wedate = $cells[0]['wedate'];
    $xwedate = new xdate;
    $xwedate->from_iso($wedate);
    $hr_wedate = $xwedate->to_amer();
    emsg('S', 'Budget has been RECALCULATED');
}
elseif (isset($_POST['save1']) || isset($_POST['save2'])) {
    $cells = $bg->save($_POST);
    $totals = $bg->get_totals($cells);
    $wedate = $cells[0]['wedate'];
    $xwedate = new xdate;
    $xwedate->from_iso($wedate);
    $hr_wedate = $xwedate->to_amer();
    emsg('S', 'The budget has been SAVED');
}
elseif (isset($_POST['comp1']) || isset($_POST['comp2'])) {
    $bg->complete($_POST);
    emsg('S', 'Budget editing is COMPLETE');
    redirect('showbgt.php');
}
elseif (isset($_POST['abandon1']) || isset($_POST['abandon2'])) {
    $bg->abandon();
    redirect('showbgt.php');
}
else {
    // first time through; or after restart
    list($wedate, $hr_wedate, $cells, $totals) = $bg->start();
}

$max = count($cells);

$fields = [
    'wedate' => [
        'name' => 'wedate',
        'type' => 'hidden',
        'value' => $wedate
    ],
    'hr_wedate' => [
        'name' => 'hr_wedate',
        'type' => 'hidden',
        'value' => $hr_wedate
    ],
    'abandon1' => [
        'name' => 'abandon1',
        'type' => 'submit',
        'value' => 'Abandon'
    ],
    'restart1' => [
        'name' => 'restart1',
        'type' => 'submit',
        'value' => 'Restart'
    ],
    'recalc1' => [
        'name' => 'recalc1',
        'type' => 'submit',
        'value' => 'Recalculate'
    ],
    'save1' => [
        'name' => 'save1',
        'type' => 'submit',
        'value' => 'Save'
    ],
    'comp1' => [
        'name' => 'comp1',
        'type' => 'submit',
        'value' => 'Complete'
    ],
    'abandon2' => [
        'name' => 'abandon2',
        'type' => 'submit',
        'value' => 'Abandon'
    ],
    'restart2' => [
        'name' => 'restart2',
        'type' => 'submit',
        'value' => 'Restart'
    ],
    'recalc2' => [
        'name' => 'recalc2',
        'type' => 'submit',
        'value' => 'Recalculate'
    ],
    'save2' => [
        'name' => 'save2',
        'type' => 'submit',
        'value' => 'Save'
    ],
    'comp2' => [
        'name' => 'comp2',
        'type' => 'submit',
        'value' => 'Complete'
    ],
    'total_wklysa' => [
        'name' => 'total_wklysa',
        'type' => 'hidden',
        'value' => $totals['wklysa']
    ],
    'total_priorsa' => [
        'name' => 'total_priorsa',
        'type' => 'hidden',
        'value' => $totals['priorsa']
    ],
    'total_addlsa' => [
        'name' => 'total_addlsa',
        'type' => 'hidden',
        'value' => $totals['addlsa']
    ],
    'total_paid' => [
        'name' => 'total_paid',
        'type' => 'hidden',
        'value' => $totals['paid']
    ],
    'total_newsa' => [
        'name' => 'total_newsa',
        'type' => 'hidden',
        'value' => $totals['newsa']
    ]

];

$period_options = [
    ['lbl' => 'Year', 'val' => 'Y'],
    ['lbl' => 'Semi-Annual', 'val' => 'S'],
    ['lbl' => 'Quarter', 'val' => 'Q'],
    ['lbl' => 'Month', 'val' => 'M'],
    ['lbl' => 'Week', 'val' => 'W']
];

for ($i = 0; $i < $max; $i++) {

    $fields["from_acct[$i]"] = [
        'name' => "from_acct[$i]",
        'type' => 'hidden',
        'value' => $cells[$i]['from_acct']
    ];

    $fields["to_acct[$i]"] = [
        'name' => "to_acct[$i]",
        'type' => 'hidden',
        'value' => $cells[$i]['to_acct']
    ];

    $fields["payee_id[$i]"] = [
        'name' => "payee_id[$i]",
        'type' => 'hidden',
        'value' => $cells[$i]['payee_id']
    ];

    $fields["acctname[$i]"] = [
        'name' => "acctname[$i]",
        'type' => 'hidden',
        'value' => $cells[$i]['acctname']
    ];

    $fields["acctnum[$i]"] = [
        'name' => "acctnum[$i]",
        'type' => 'hidden',
        'value' => $cells[$i]['acctnum']
    ];

    $fields["typdue[$i]"] = [
        'name' => "typdue[$i]",
        'type' => 'hidden',
        'value' => $cells[$i]['typdue']
    ];

    $fields["period[$i]"] = [
        'name' => "period[$i]",
        'type' => 'hidden',
        'value' => $cells[$i]['period']
    ];

    $fields["wklysa[$i]"] = [
        'name' => "wklysa[$i]",
        'type' => 'text',
        'size' => 10,
        'maxlength' => 10
    ];

    $fields["priorsa[$i]"] = [
        'name' => "priorsa[$i]",
        'type' => 'text',
        'size' => 10,
        'maxlength' => 10
    ];

    $fields["addlsa[$i]"] = [
        'name' => "addlsa[$i]",
        'type' => 'text',
        'size' => 10,
        'maxlength' => 10
    ];

    $fields["paid[$i]"] = [
        'name' => "paid[$i]",
        'type' => 'text',
        'size' => 10,
        'maxlength' => 10
    ];

    $fields["newsa[$i]"] = [
        'name' => "newsa[$i]",
        'type' => 'hidden',
        'value' => $cells[$i]['newsa']
    ];

};

$form->set($fields);

$report = model('report', $db);
$bals = $report->get_balances($cells[0]['wedate']);
$nbals = count($bals);
$dt = new xdate();
$dt->from_iso($cells[0]['wedate']);
$today = $dt->to_amer();

$page_title = 'Edit Budget';
$return = 'editbgt.php';
include VIEWDIR . 'editbgt.view.php';
