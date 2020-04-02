
<form action="<?php echo $base_url . 'txnedt.php'; ?>" method="post">

<?php $iaxform->hidden('txnid', $txns[0]['txnid']); ?>
<?php $iaxform->hidden('txntype', 'iaxfer'); ?>
<?php $iaxform->hidden('acct_id', $acct_id); ?>

<h2>Inter-Account Transfer</h2>

<h3>Transaction ID: <?php echo $txns[0]['txnid']; ?></h3>

<?php foreach ($txns as $txn): ?>

<?php $iaxform->hidden('iaxid', $txn['id']); ?>
<?php $iaxform->hidden('from_acct', $txn['from_acct']); ?>

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
<?php $iaxform->text('txn_dt', date::reformat('Y-m-d', $txn['txn_dt'], 'm/d/y')); ?>
</td>
<td>
<label for="checkno">Check No</label>
&nbsp;
<!-- checkno -->
<?php $iaxform->text('checkno', $txn['checkno']); ?>
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
<?php $iaxform->select('payee_id', $txn['payee_id']); ?>
</td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td>
<!-- memo -->
<?php $iaxform->text('memo', $txn['memo']); ?>
</td>
</tr>

<tr>
<td><label for="to_acct">Category/Acct</label></td>
<td>
<!-- to_acct -->
<?php echo $txn['to_acct_name']; ?>
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
<?php echo int2dec($txn['amount']); ?>
</td>
</tr>
</table>

</fieldset>
<br/>
<?php endforeach; ?>

<p>
<?php $iaxform->submit('s1'); ?>
&nbsp;
<?php form::abandon('txnshow.php?txnid=' . $txns[0]['txnid']); ?>
</p>


</form>

