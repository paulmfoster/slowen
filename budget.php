<?php

// Allow the user to specify a from and to date, and a category to get the
// amount and transactions for that category in that time period.

include 'init.php';
$rpts = model('report');

$accts = $rpts->get_budget_accounts();
$cat_options = [];
foreach ($accts as $acct) {
	if ($acct['acct_type'] == 'I') {
		$type = ' (income)';
	}
	elseif ($acct['acct_type'] == 'E') {
		$type = ' (expense)';
	}
	$cat_options[] = ['lbl' => $acct['name'] . $type, 'val' => $acct['acct_id']];
}	

$fields = [
	'from' => [
		'name' => 'from',
		'type' => 'date',
		'label' => 'Start Date',
		'required' => 1
	],
	'to' => [
		'name' => 'to',
		'type' => 'date',
		'label' => 'End Date',
		'required' => 1
	],
	'category' => [
		'name' => 'category',
		'type' => 'select',
		'label' => 'Category',
		'required' => 1,
		'options' => $cat_options
	],
	's1' => [
		'name' => 's1',
		'type' => 'submit',
		'value' => 'Report'
	]
];

$form->set($fields);

view('Budget Query', [], 'budget2.php', 'budget');

