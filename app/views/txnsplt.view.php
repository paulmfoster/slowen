<?php include VIEWDIR . 'head.view.php'; ?>

<form action="<?php echo $return; ?>" method="post">

<?php $form->hidden('max_splits'); ?>

<table>

<?php for ($j = 0; $j < $max_splits; $j++): ?>

<tr>
<td><label>Payee</label></td>
<td><?php $form->select('split_payee_id'); ?></td>
</tr>

<tr>
<td><label>Category</label></td>
<td><?php $form->select('split_to_acct'); ?></td>
</tr>

<tr>
<td><label>Memo</label></td>
<td><?php $form->text('split_memo'); ?></td>
</tr>

<tr>
<td><label>Debit</label></td>
<td><?php $form->text('split_dr_amount'); ?></td>
</tr>

<tr>
<td><label>Credit</label></td>
<td><?php $form->text('split_cr_amount'); ?></td>
</tr>

<?php if ($j < $max_splits - 1): ?>
<!-- spacer -->
<tr><td colspan="2"></td></tr>
<!-- spacer -->
<?php endif; ?>

<?php endfor; ?>

<tr>
<td>
<?php $form->submit('save'); ?>
&nbsp;
<?php form::abandon('addtxn.php'); ?>
</td>
<td></td>
</tr>

</table>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

