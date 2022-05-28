<?php include VIEWDIR . 'head.view.php'; ?>

<!-- Main transaction data entry screen -->

<form action="<?php echo $this->return; ?>" method="post">

<table>

<tr>
<td><label for="from_acct">From Acct</label></td>
<td><?php $this->form->select('from_acct'); ?></td>
</tr>

<tr>
<td><label>Inter-Account Transfer?</label></td>
<td><?php $this->form->checkbox('xfer', 0); ?></td>
</tr>

<tr>
<td><label for="txn_dt">Date</label></td>
<td><?php $this->form->select('txn_dom'); ?></td>
</tr>

<tr>
<td><label for="payee_id">Payee Name</label></td>
<td><?php $this->form->select('payee_id'); ?></td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td><?php $this->form->text('memo'); ?></td>
</tr>

<tr>
<td><label for="to_acct">Category/Acct</label></td>
<td><?php $this->form->select('to_acct'); ?></td>
</tr>

<tr>
<td><label for="dr_amount">Debit</label></td>
<td><?php $this->form->text('dr_amount'); ?></td>
</tr>

<tr>
<td><label for="cr_amount">Credit</label></td>
<td><?php $this->form->text('cr_amount'); ?></td>
</tr>

<tr>
<td><?php $this->form->submit('s1'); ?>&nbsp;<?php form::abandon(url('sched', 'add')); ?></td>
<td></td>
</tr>

</table>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

