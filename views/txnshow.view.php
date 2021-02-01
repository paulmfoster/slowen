
<?php $p = 0; ?>

<?php foreach ($txns as $txn): ?>

<?php if ($p > 0): ?>
<h3>Transfer Information</h3>
<?php endif; ?>

<table>
<tr><th>Item</th><th>Value</th></tr>

<tr>
<td>
<label>Transaction ID</label>
</td>
<td>
<?php echo $txn['txnid']; ?>
</td>
</tr>

<tr>
<td>
<label>From Account</label>
</td>
<td>
<?php echo $txn['from_acct'] . ' ' . $txn['from_acct_name']; ?>
</td>
</tr>

<tr>
<td>
<label>Date</label>
</td>
<td>
<?php echo pdate::iso2am($txn['txn_dt']); ?>
</td>
</tr>

<tr>
<td>
<label>Check #</label>
</td>
<td>
<?php echo $txn['checkno']; ?>
</td>
</tr>

<tr>
<td>
<label>Split?</label>
</td>
<td>
<?php echo $txn['split'] ? 'Yes' : 'No';?>
</td>
</tr>

<tr>
<td>
<label>Payee</label>
</td>
<td>
<?php echo $txn['payee_id'] . ' ' . $txn['payee_name']; ?>
</td>
</tr>

<tr>
<td>
<label>To Account</label>
</td>
<td>
<?php echo $txn['to_acct'] . ' ' .  $txn['to_acct_name']; ?>
</td>
</tr>

<tr>
<td>
<label>Memo</label>
</td>
<td>
<?php echo $txn['memo']; ?>
</td>
</tr>

<tr>
<td>
<label>Status</label>
</td>
<td>
<?php echo $txn['x_status']; ?>
</td>
</tr>

<tr>
<td>
<label>Reconciliation Date</label>
</td>
<td>
<?php echo pdate::iso2am($txn['recon_dt']); ?>
</td>
</tr>

<tr>
<td>
<label>Amount</label>
</td>
<td>
<?php if ($txn['amount'] < 0) {
	echo int2dec($txn['dr_amount']);
}
else {
	echo int2dec($txn['cr_amount']);
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
<?php form::button('Edit', "index.php?c=transaction&m=edit&txnid={$txns[0]['txnid']}"); ?>
&nbsp;
<?php if ($txns[0]['status'] != 'V'): ?>
<?php form::button('Void', 'index.php?c=transaction&m=void&txnid=' . $txns[0]['txnid']); ?>
&nbsp;
<?php endif; ?>
<?php form::abandon("index.php?c=transaction&m=register&acct_id={$txns[0]['from_acct']}"); ?>
</p>

