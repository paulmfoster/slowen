<?php include VIEWDIR . 'head.view.php'; ?>

<!-- This screen is for single transactions with possible splits. -->

<form action="<?php echo $return; ?>" method="post">
<?php $form->hidden('txntype'); ?>
<?php $form->hidden('txnid'); ?>

<?php $txn = $txns[0]; ?>

<h3>Transaction ID: <?php echo $txn['txnid']; ?></h3>

<table>

<tr>
<td class="tdlabel">From Acct</td>
<td><?php echo $txn['from_acct'] . ' ' . $txn['from_acct_name']; ?></td>
</tr>

<tr>
<td class="tdlabel">Date</td>
<td><?php $form->date('txn_dt'); ?></td>
<td>

<tr>
<td class="tdlabel">Check No</td>
<td><?php $form->text('checkno'); ?></td>
</tr>

<tr>
<td class="tdlabel">Payee</td>
<td><?php $form->select('payee_id'); ?></td>
</tr>

<tr>
<td class="tdlabel">Memo</td>
<td><?php $form->text('memo'); ?></td>
</tr>

<tr>
<td class="tdlabel">Category/Acct</td>
<td><?php $form->select('to_acct'); ?></td>
</tr>

<tr>
<td class="tdlabel">Status</td>
<td><?php echo $txn['x_status']; ?></td>
</tr>

<tr>
<td class="tdlabel">Recon Dt</td>
<?php $recondt = new xdate(); ?>
<td><?php echo $recondt->iso2amer($txn['recon_dt']); ?></td>
</tr>

<tr>
<td class="tdlabel">Amount</td>
<td>
<?php if (array_key_exists('amount', $form->fields)): ?>
<?php $form->text('amount', int2dec($txn['amount'])); ?>
<?php else: ?>
<?php echo int2dec($txn['amount']); ?>
<?php endif; ?>
</td>
</tr>
</table>

<p>
<?php $form->submit('save'); ?>
&nbsp;
<?php form::abandon('showtxn.php?txnid=' . $txn['txnid']); ?>
</p>


</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

