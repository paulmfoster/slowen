
<form action="index.php?c=transaction&m=edit1" method="post">

<?php $form->hidden('txnid'); ?>

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
<?php echo $this->statuses[$txn['status']]; ?>
</td>

<td><label for="recon_dt">Recon Dt</label>
&nbsp;
<!-- recon_dt -->
<?php echo pdate::iso2am($txn['recon_dt']); ?>
</td>
</tr>

<!-- amount -->
<tr>
<td>
<label for="amount">Amount</label>
&nbsp;
<?php
$max_txns = 1;
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

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon("index.php?c=transaction&m=show&txnid={$txn['txnid']}"); ?>
</p>


</form>

