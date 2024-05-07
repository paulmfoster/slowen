<?php include VIEWDIR . 'head.view.php'; ?>

<form action="<?php echo $return; ?>" method="post">
<?php $form->hidden('txntype'); ?>
<?php $form->hidden('txnid'); ?>

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
<td colspan="2"><?php $form->date('txn_dt'); ?></td>
</tr>

<tr>
<td class="tdlabel">Check No</td>
<td colspan="2"><?php $form->text('checkno'); ?></td>
</tr>

<tr>
<td class="tdlabel">Payee</td>
<td colspan="2"><?php $form->select('payee_id'); ?></td>
</tr>

<tr>
<td class="tdlabel">Memo</td>
<td colspan="2"><?php $form->text('memo'); ?></td>
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
<?php $recondt = new xdate(); ?>
<td colspan="2"><?php echo $recondt->iso2amer($txns[0]['recon_dt']); ?></td>
</tr>

<tr>
<td class="tdlabel">Amount</td>
<td><?php echo int2dec($txns[0]['amount']); ?></td>
<td><?php echo int2dec($txns[1]['amount']); ?></td>
</tr>

</table>

<p>
<?php $form->submit('save'); ?>
&nbsp;
<?php form::abandon('showtxn.php?txnid=' . $txns[0]['txnid']); ?>
</p>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

