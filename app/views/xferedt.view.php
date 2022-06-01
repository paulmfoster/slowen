<?php include VIEWDIR . 'head.view.php'; ?>

<?php extract($data); ?>

<form action="<?php echo $this->return; ?>" method="post">
<?php $this->form->hidden('txntype'); ?>
<?php $this->form->hidden('txnid'); ?>

<h3>Transaction ID: <?php echo $txns[0]['txnid']; ?></h3>

<table>

<thead><th></th><th>Primary</th><th>Secondary</th></thead>

<tr>
<td class="tdlabel">From Acct</td>
<td><?php echo $txns[0]['from_acct'] . ' ' . $txns[0]['from_acct_name']; ?></td>
<td><?php echo $txns[1]['from_acct'] . ' ' . $txns[1]['from_acct_name']; ?></td>
</tr>

<tr>
<td class="tdlabel">Date</td>
<td colspan="2"><?php $this->form->date('txn_dt'); ?></td>
</tr>

<tr>
<td class="tdlabel">Check No</td>
<td colspan="2"><?php $this->form->text('checkno'); ?></td>
</tr>

<tr>
<td class="tdlabel">Payee</td>
<td colspan="2"><?php $this->form->select('payee_id'); ?></td>
</tr>

<tr>
<td class="tdlabel">Memo</td>
<td colspan="2"><?php $this->form->text('memo'); ?></td>
</tr>

<tr>
<td class="tdlabel">From Acct</td>
<td><?php echo $txns[0]['to_acct'] . ' ' . $txns[0]['to_acct_name']; ?></td>
<td><?php echo $txns[1]['to_acct'] . ' ' . $txns[1]['to_acct_name']; ?></td>
</tr>

<tr>
<td class="tdlabel">Status</td>
<td><?php echo $txns[0]['x_status']; ?></td>
<td><?php echo $txns[1]['x_status']; ?></td>
</tr>

<tr>
<td class="tdlabel">Recon Dt</td>
<td colspan="2"><?php echo pdate::iso2am($txns[0]['recon_dt']); ?></td>
</tr>

<tr>
<td class="tdlabel">Amount</td>
<td><?php echo int2dec($txns[0]['amount']); ?></td>
<td><?php echo int2dec($txns[1]['amount']); ?></td>
</tr>

</table>

<p>
<?php $this->form->submit('save'); ?>
&nbsp;
<?php form::abandon(url('txn', 'show', $txns[0]['txnid'])); ?>
</p>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

