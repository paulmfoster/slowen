<?php include VIEWDIR . 'head.view.php'; ?>

<?php extract($data); ?>

<form method="post" action="<?php echo $this->return; ?>">

<?php $this->form->hidden('txnid'); ?>

<p>
<label>CONFIRM you wish to void this transaction</label>
&nbsp;
<?php $this->form->submit('s1'); ?>
&nbsp;
<?php form::abandon("register.php?acct_id={$txns[0]['from_acct']}"); ?>
</form>
</p>


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
<?php $txndt = new xdate(); ?>
<?php echo $txndt->iso2amer($txn['txn_dt']); ?>
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
<?php $recondt = new xdate(); ?>
<?php echo $recondt->iso2amer($txn['recon_dt']); ?>
</td>
</tr>

<tr>
<td>
<label>Amount</label>
</td>
<td>
<?php if ($txn['amount'] < 0) {
	echo $txn['dr_amount'];
}
elseif ($txn['amount'] > 0) {
	echo $txn['cr_amount'];
}
else {
	echo 0;
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

<?php include VIEWDIR . 'footer.view.php'; ?>

