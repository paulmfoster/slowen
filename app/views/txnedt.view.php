<?php include VIEWDIR . 'head.view.php'; ?>

<?php extract($data); ?>

<form action="<?php echo $this->return; ?>" method="post">

<?php $txn = $txns[0]; ?>

<?php $this->form->hidden('txnid', $txn['txnid']); ?>

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
<?php $this->form->date('txn_dt', $txn['txn_dt']); ?>
</td>
<td>
<label for="checkno">Check No</label>
&nbsp;
<!-- checkno -->
<?php $this->form->text('checkno', $txn['checkno']); ?>
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
<?php $this->form->select('payee_id', $txn['payee_id']); ?>
</td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td>
<!-- memo -->
<?php $this->form->text('memo', $txn['memo']); ?>
</td>
</tr>

<tr>
<td><label for="to_acct">Category/Acct</label></td>
<td>
<!-- to_acct -->
<?php $this->form->select('to_acct', $txn['to_acct']); ?>
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
<?php echo pdate::iso2am($txn['recon_dt']); ?>
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
	$this->form->text('amount', int2dec($txn['amount']));
}
?>
</td>
</tr>
</table>

</fieldset>

<!-- SPLITS HERE -->

<?php if ($txns[0]['split']): ?>
<?php $this->form->hidden('txntype', 'splits'); ?>
<?php include 'views/splitedt.view.php'; ?>
<?php else: ?>
<?php $this->form->hidden('txntype', 'single'); ?>
<?php endif; ?>

<p>
<?php $this->form->submit('s1'); ?>
&nbsp;
<?php form::abandon("index.php?url=txn/show/{$txn['txnid']}"); ?>
</p>


</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

