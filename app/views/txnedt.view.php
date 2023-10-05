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
<?php $recondt = new xdate(); ?>
<td><?php echo $recondt->iso2amer($txn['recon_dt']); ?></td>
</tr>

<tr>
<td class="tdlabel">Amount</td>
<td>
<?php if (array_key_exists('amount', $this->form->fields)): ?>
<?php $this->form->text('amount', int2dec($txn['amount'])); ?>
<?php else: ?>
<?php echo int2dec($txn['amount']); ?>
<?php endif; ?>
</td>
</tr>
</table>

<p>
<?php $this->form->submit('save'); ?>
&nbsp;
<?php form::abandon('index.php?c=txn&m=show&txnid=' . $txn['txnid']); ?>
</p>


</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

