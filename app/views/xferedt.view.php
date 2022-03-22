<?php include VIEWDIR . 'head.view.php'; ?>

<?php extract($data); ?>

<form action="<?php echo $this->return; ?>" method="post">

<?php $this->form->hidden('txnid', $txns[0]['txnid']); ?>
<?php $this->form->hidden('txntype', 'xfer'); ?>

<h3>Transaction ID: <?php echo $txns[0]['txnid']; ?></h3>

<table>

<tr>
<td><label>From Acct</label></td>
<td><?php echo $txns[0]['from_acct'] . ' ' . $txns[0]['from_acct_name']; ?></td>
<td><?php echo $txns[1]['from_acct'] . ' ' . $txns[1]['from_acct_name']; ?></td>
</tr>

<tr>
<td><label>Date</label></td>
<td colspan="2"><?php $this->form->date('txn_dt', $txns[0]['txn_dt']); ?></td>
</tr>

<tr>
<td><label>Check No</label></td>
<td colspan="2"><?php $this->form->text('checkno', $txns[0]['checkno']); ?></td>
</tr>

<tr>
<td><label>Payee</label></td>
<td colspan="2"><?php $this->form->select('payee_id', $txns[0]['payee_id']); ?></td>
</tr>

<tr>
<td><label>Memo</label></td>
<td colspan="2"><?php $this->form->text('memo', $txns[0]['memo']); ?></td>
</tr>

<tr>
<td><label>From Acct</label></td>
<td><?php echo $txns[0]['to_acct'] . ' ' . $txns[0]['to_acct_name']; ?></td>
<td><?php echo $txns[1]['to_acct'] . ' ' . $txns[1]['to_acct_name']; ?></td>
</tr>

<tr>
<td><label>Status</label></td>
<td colspan="2"><?php echo $statuses[$txns[0]['status']]; ?></td>
</tr>

<tr>
<td><label>Recon Dt</label></td>
<td colspan="2"><?php echo pdate::iso2am($txns[0]['recon_dt']); ?></td>
</tr>

<tr>
<td><label>Amount</label></td>
<td><?php echo int2dec($txns[0]['amount']); ?></td>
<td><?php echo int2dec($txns[1]['amount']); ?></td>
</tr>

</table>

<p>
<?php $this->form->submit('s1'); ?>
&nbsp;
<?php form::abandon("index.php?url=txn/show/{$txns[0]['txnid']}"); ?>
</p>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

