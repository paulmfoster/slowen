<?php include VIEWDIR . 'head.view.php'; ?>

<!-- This screen is for single transactions with possible splits. -->

<?php extract($data); ?>

<form action="<?php echo $this->return; ?>" method="post">
<?php $this->form->hidden('txntype'); ?>
<?php $this->form->hidden('txnid'); ?>

<?php $txn = $txns[0]; ?>

<h3>Transaction ID: <?php echo $txn['txnid']; ?></h3>

<table>

<tr>
<td class="tdlabel">From Acct</td>
<td><?php echo $txn['from_acct'] . ' ' . $txn['from_acct_name']; ?></td>
</tr>

<tr>
<td class="tdlabel">Date</td>
<td><?php $this->form->date('txn_dt'); ?></td>
<td>

<tr>
<td class="tdlabel">Check No</td>
<td><?php $this->form->text('checkno'); ?></td>
</tr>

<tr>
<td class="tdlabel">Payee</td>
<td><?php $this->form->select('payee_id'); ?></td>
</tr>

<tr>
<td class="tdlabel">Memo</td>
<td><?php $this->form->text('memo'); ?></td>
</tr>

<tr>
<td class="tdlabel">Category/Acct</td>
<td><?php $this->form->select('to_acct'); ?></td>
</tr>

<tr>
<td class="tdlabel">Status</td>
<td><?php echo $txn['x_status']; ?></td>
</tr>

<tr>
<td class="tdlabel">Recon Dt</td>
<td><?php echo pdate::iso2am($txn['recon_dt']); ?></td>
</tr>

<tr>
<td class="tdlabel">Amount</td>
<td>
<?php echo int2dec($txn['amount']); ?>
</td>
</tr>

<?php for ($k = 0; $k < $max_splits; $k++): ?>
<?php $this->form->hidden('split_id', $splits[$k]['id']); ?>

<tr>
<td class="tdlabel">Split Payee <?php echo $k + 1; ?></td>
<td><?php $this->form->select('split_payee_id', $splits[$k]['payee_id']); ?></td>
</tr>

<tr>
<td class="tdlabel">Split To Acct <?php echo $k + 1; ?></td>
<td><?php $this->form->select('split_to_acct', $splits[$k]['to_acct']); ?></td>
</tr>

<tr>
<td class="tdlabel">Split Memo <?php echo $k + 1; ?></td>
<td><?php $this->form->text('split_memo', $splits[$k]['memo']); ?></td>
</tr>

<tr>
<td class="tdlabel">Split Amount <?php echo $k + 1; ?></td>
<td><?php $this->form->text('split_amount', int2dec($splits[$k]['amount'])); ?></td>
</tr>

<?php endfor; ?>

</table>

<p>
<?php $this->form->submit('save'); ?>
&nbsp;
<?php form::abandon(url('txn', 'show', $txn['txnid'])); ?>
</p>


</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

