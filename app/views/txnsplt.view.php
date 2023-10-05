<?php include VIEWDIR . 'head.view.php'; ?>

<?php extract($data); ?>

<form action="<?php echo $this->return; ?>" method="post">

<?php $this->form->hidden('max_splits'); ?>

<table>

<?php for ($j = 0; $j < $max_splits; $j++): ?>

<tr>
<td><label>Payee</label></td>
<td><?php $this->form->select('split_payee_id'); ?></td>
</tr>

<tr>
<td><label>Category</label></td>
<td><?php $this->form->select('split_to_acct'); ?></td>
</tr>

<tr>
<td><label>Memo</label></td>
<td><?php $this->form->text('split_memo'); ?></td>
</tr>

<tr>
<td><label>Debit</label></td>
<td><?php $this->form->text('split_dr_amount'); ?></td>
</tr>

<tr>
<td><label>Credit</label></td>
<td><?php $this->form->text('split_cr_amount'); ?></td>
</tr>

<?php if ($j < $max_splits - 1): ?>
<!-- spacer -->
<tr><td colspan="2"></td></tr>
<!-- spacer -->
<?php endif; ?>

<?php endfor; ?>

<tr>
<td>
<?php $this->form->submit('save'); ?>
&nbsp;
<?php form::abandon('index.php?c=txn&m=add'); ?>
</td>
<td></td>
</tr>

</table>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

