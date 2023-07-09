<?php include VIEWDIR . 'head.view.php' ?>

<?php extract($data); ?>

<?php $row = 0; ?>
<?php $p = 0; ?>

<?php foreach ($txns as $txn): ?>

<?php if ($p > 0): ?>
<h3>Transfer Information</h3>
<?php endif; ?>

<table>
<tr><th>Item</th><th>Value</th></tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Transaction ID</label>
</td>
<td>
<?php echo $txn['txnid']; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>From Account</label>
</td>
<td>
<?php echo $txn['from_acct'] . ' ' . $txn['from_acct_name']; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Date</label>
</td>
<td>
<?php $txndt = new xdate(); ?>
<?php echo $txndt->iso2amer($txn['txn_dt']); ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Check #</label>
</td>
<td>
<?php echo $txn['checkno']; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Split?</label>
</td>
<td>
<?php echo $txn['split'] ? 'Yes' : 'No';?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Payee</label>
</td>
<td>
<?php echo $txn['payee_id'] . ' ' . $txn['payee_name']; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>To Account</label>
</td>
<td>
<?php echo $txn['to_acct'] . ' ' .  $txn['to_acct_name']; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Memo</label>
</td>
<td>
<?php echo $txn['memo']; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Status</label>
</td>
<td>
<?php echo $txn['x_status']; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Reconciliation Date</label>
</td>
<td>
<?php $recondt = new xdate(); ?>
<?php echo $recondt->iso2amer($txn['recon_dt']); ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td>
<label>Amount</label>
</td>
<td>
<?php if ($txn['amount'] < 0) {
	echo $txn['dr_amount'];
}
else {
	echo $txn['cr_amount'];
}
?>

</table>

<?php if ($txn['split'] == 1): ?>
<?php $splitno = 1; ?>
<h3>Splits</h3>
<table rules="all" border="1">
<tr><th>#</th><th>Item</th><th>Value</th></tr>
<?php foreach ($splits as $split): ?>

<tr>
<td rowspan="4"><?php echo $splitno; ?></td>
<td><label>Payee</label></td>
<td>
<?php echo $split['payee_id'] . ' ' . $split['payee_name']; ?>
</td>
</tr>

<tr>
<td><label>Destination Acct</label></td>
<td>
<?php echo $split['to_acct'] . ' ' . $split['to_acct_name']; ?>
</td>
</tr>

<tr>
<td><label>Memo</label></td>
<td>
<?php echo $split['memo']; ?>
</td>
</tr>

<tr>
<td><label>Amount</label></td>
<td>
<?php echo int2dec($split['amount']); ?>
</td>
</tr>

<?php $splitno++; ?>

<?php endforeach; ?>
</table>
<?php endif; /* has splits */ ?>

<?php $p++; ?>

<?php endforeach; ?>

<p>
<?php form::button('Edit', url('txn', 'edit', $txns[0]['txnid'])); ?>
&nbsp;
<?php if ($txns[0]['status'] != 'V'): ?>
<?php form::button('Void', url('txn', 'void', $txns[0]['txnid'])); ?>
&nbsp;
<?php endif; ?>
<?php form::abandon("index.php"); ?>
</p>

<?php include VIEWDIR . 'footer.view.php'; ?>

