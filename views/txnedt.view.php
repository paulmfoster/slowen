
<form action="<?php echo $base_url . 'txnedt.php'; ?>" method="post">

<?php $txn = $txns[0]; ?>

<?php $form->hidden('txnid', $txn['txnid']); ?>
<?php $form->hidden('acct_id', $acct_id); ?>

<h3>Transaction ID: <?php echo $txn['txnid']; ?></h3>

<fieldset>
<table>

<tr>
<td><label for="from_acct">From Acct</label></td>
<td>
<!-- from_acct -->
<?php echo $txn['from_acct'] . ' ' . $txn['from_acct_name']; ?>
</td>
</tr>

</table>

<table>
<tr>
<td><label for="txn_dt">Date</label>
&nbsp;
<!-- txn_dt -->
<?php $form->text('txn_dt', date::reformat('Y-m-d', $txn['txn_dt'], 'm/d/y')); ?>
</td>
<td>
<label for="checkno">Check No</label>
&nbsp;
<!-- checkno -->
<?php $form->text('checkno', $txn['checkno']); ?>
</td>
</tr>

</table>
</fieldset>

<fieldset>

<table>

<tr>
<td><label for="payee_id">Payee</label></td>
<td>
<!-- payee_id -->
<?php $form->select('payee_id', $txn['payee_id']); ?>
</td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td>
<!-- memo -->
<?php $form->text('memo', $txn['memo']); ?>
</td>
</tr>

<tr>
<td><label for="to_acct">Category/Acct</label></td>
<td>
<!-- to_acct -->
<?php $form->select('to_acct', $txn['to_acct']); ?>
</td>
</tr>

</table>

</fieldset>

<fieldset>

<table>

<tr>
<td><label for="status">Status</label>
&nbsp;
<!-- status -->
<?php echo $statuses[$txn['status']]; ?>
</td>

<td><label for="recon_dt">Recon Dt</label>
&nbsp;
<!-- recon_dt -->
<?php echo date::reformat('Y-m-d', $txn['recon_dt'], 'm/d/y'); ?>
</td>
</tr>

<!-- amount -->
<tr>
<td>
<label for="amount">Amount</label>
&nbsp;
<?php
if ($max_txns > 1 || $txn['status'] == 'R' || $txn['status'] == 'V') {
	echo int2dec($txn['amount']);
}
else {
	$form->text('amount', int2dec($txn['amount']));
}
?>
</td>
</tr>
</table>

</fieldset>

<!-- SPLITS HERE -->

<?php if ($txns[0]['split']): ?>
<?php $form->hidden('txntype', 'splits'); ?>
<?php include 'views/splitedt.view.php'; ?>
<?php else: ?>
<?php $form->hidden('txntype', 'single'); ?>
<?php endif; ?>

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon("txnshow.php?acct_id={$acct_id}&txnid={$txn['txnid']}"); ?>
</p>


</form>

