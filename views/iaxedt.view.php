
<form action="index.php?c=transaction&m=editx" method="post">

<?php $form->hidden('txnid', $txns[0]['txnid']); ?>
<?php $form->hidden('id1'); ?>
<?php $form->hidden('id2'); ?>

<h2>Inter-Account Transfer</h2>

<h3>Transaction ID: <?php echo $txns[0]['txnid']; ?></h3>

<table>

<tr>
<td><label for="txn_dt">Date</label></td>
<td>
<?php $form->date('txn_dt', $txns[0]['txn_dt']); ?>
</td>
</tr>

<tr>
<td>
<label for="checkno">Check No</label></td>
<td>
<?php $form->text('checkno', $txns[0]['checkno']); ?>
</td>
</tr>

<tr>
<td><label>Status</label></td>
<td>
<?php echo $this->statuses[$txns[0]['status']]; ?>
</td>
</tr>

<tr>
<td><label>Recon Dt</label></td>
<td>
<?php echo pdate::iso2am($txns[0]['recon_dt']); ?>
</td>
</tr>

</table>

<table>

<tr>
<td><label>Accounts</label></td>
<td>
<?php echo $txns[0]['from_acct'] . ' ' . $txns[0]['from_acct_name']; ?>
</td>
<td>
<?php echo $txns[1]['from_acct'] . ' ' . $txns[1]['from_acct_name']; ?>
</td>
</tr>

<tr>
<td><label>Payee</label></td>
<td>
<?php $form->select('payee_id1', $txns[0]['payee_id']); ?>
</td>
<td>
<?php $form->select('payee_id2', $txns[1]['payee_id']); ?>
</td>
</tr>

<tr>
<td><label>Memo</label></td>
<td>
<?php $form->text('memo1', $txns[0]['memo']); ?>
</td>
<td>
<?php $form->text('memo2', $txns[1]['memo']); ?>
</td>
</tr>

<tr>
<td><label>Amount</label></td>
<td>
<?php echo int2dec($txns[0]['amount']); ?>
</td>
<td>
<?php echo int2dec($txns[1]['amount']); ?>
</td>
</tr>

<table>


<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('index.php?c=transaction&m=show&txnid=' . $txns[0]['txnid']); ?>
</p>


</form>

