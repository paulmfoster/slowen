<?php include VIEWDIR . 'head.view.php'; ?>

<!-- Main transaction data entry screen -->

<form action="<?php echo $return; ?>" method="post">

<table>

<tr>
<td><label for="from_acct">From</label></td>
<td colspan="4"><?php $form->select('from_acct'); ?></td>
</tr>

<tr>
<td><label for="payee_id">Payee</label></td>
<td colspan="4"><?php $form->select('payee_id'); ?></td>
</tr>

<tr>
<td><label for="to_acct">To</label></td>
<td colspan="4"><?php $form->select('to_acct'); ?></td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td colspan="4"><?php $form->text('memo'); ?></td>
</tr>

<tr>
<td><label for="txn_dt">Date</label></td>
<?php $txndt = new xdate(); ?>
<td colspan="2"><?php $form->date('txn_dt', $txndt->to_iso()); ?></td>
<td><label for="checkno">Check #</label></td>
<td><?php $form->text('checkno'); ?></td>
</tr>

<tr>
<td><label for="dr_amount">Debit</label></td>
<td colspan="2"><?php $form->text('dr_amount'); ?></td>
<td><label for="cr_amount">Credit</label></td>
<td><?php $form->text('cr_amount'); ?></td>
</tr>

<tr>
<td><label for="status">Status</label></td>
<td colspan="2"><?php $form->select('status', ' '); ?></td>
<td><label for="recon_dt">Recon Dt</label>
<td><?php $form->date('recon_dt'); ?></td>
</tr>

<tr>
<td><label for="split">Has Split?</label></td>
<td colspan="2"><?php $form->checkbox('split', 0); ?></td>
<td><label for="max_splits"># Splits</label></td>
<td colspan="2"><?php $form->text('max_splits'); ?></td>
</tr>

<tr>
<td></td><td colspan="2"><?php $form->submit('save'); ?></td>
<td colspan="2"></td>
</tr>

</table>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

