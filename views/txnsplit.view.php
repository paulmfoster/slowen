
<form action="index.php?c=addtxn&m=split2" method="post">

<?php $form->hidden('txnid', $txn['txnid']); ?>

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
<?php $form->date('txn_dt', $txn['txn_dt']); ?>
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
<?php echo pdate::reformat('Y-m-d', $txn['recon_dt'], 'm/d/y'); ?>
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








<fieldset>
<table>

<tr>
<td>
<label for="split">Has Split?</label>
&nbsp;
<?php echo $txn['split'] ? 'Yes' : 'No'; ?>
</td>
</tr>

<tr>
<td>
<label for="max_splits">Number of Splits</label>
&nbsp;
<?php echo $max_splits; ?>
</td>
</tr>

</table>
</fieldset>

<h3>Splits</h3>

<?php for ($b = 0; $b < $max_splits; $b++): ?>

<?php $sform->hidden('split_id', $splits[$b]['id']); ?>

<fieldset>
<table>
<tr><th>#</th><th>Item</th><th>Value</th></tr>

<tr>
<td rowspan="4"><?php echo $b + 1; ?></td>
<td><label>Payee</label></td>
<td>
<?php $sform->select('split_payee_id', $splits[$b]['payee_id']); ?>
</td>
</tr>

<tr>
<td><label>Destination Acct</label></td>
<td>
<?php $sform->select('split_to_acct', $splits[$b]['to_acct']); ?>
</td>
</tr>

<tr>
<td><label>Memo</label></td>
<td>
<?php $sform->text('split_memo', $splits[$b]['memo']); ?>
</td>
</tr>

<tr>
<td><label>Amount</label></td>
<td>
<?php $sform->text('split_amount', int2dec($splits[$b]['amount'])); ?>
</td>
</tr>

</table>
</fieldset>

<?php endfor; ?>


























<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon("txnshow.php?acct_id={$acct_id}&txnid={$txn['txnid']}"); ?>
</p>


</form>


