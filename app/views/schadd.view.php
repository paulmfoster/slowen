<?php include VIEWDIR . 'head.view.php'; ?>

<!-- Main transaction data entry screen -->

<form action="<?php echo $return; ?>" method="post">

<table>

<tr>
<td><label for="from_acct">From Acct</label></td>
<td><?php $form->select('from_acct'); ?></td>
</tr>

<tr>
<td><label>Inter-Account Transfer?</label></td>
<td><?php $form->checkbox('xfer', 0); ?></td>
</tr>

<tr>
<td><label>Frequency</label></td>
<td><?php $form->text('freq'); ?></td>
</tr>

<tr>
<td><label>Period</label></td>
<td><?php $form->select('period'); ?></td>
</tr>

<tr>
<td><label>Occurrence in Month</label></td>
<td><?php $form->select('occ'); ?></td>
</tr>

<tr>
<td><label>Last Date</label></td>
<td><?php $form->date('last'); ?></td>
</tr>

<tr>
<td><label for="payee_id">Payee Name</label></td>
<td><?php $form->select('payee_id'); ?></td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td><?php $form->text('memo'); ?></td>
</tr>

<tr>
<td><label for="to_acct">Category/Acct</label></td>
<td><?php $form->select('to_acct'); ?></td>
</tr>

<tr>
<td><label for="dr_amount">Debit</label></td>
<td><?php $form->text('dr_amount'); ?></td>
</tr>

<tr>
<td><label for="cr_amount">Credit</label></td>
<td><?php $form->text('cr_amount'); ?></td>
</tr>

<tr>
<td><?php $form->submit('s1'); ?>&nbsp;<?php form::abandon('addsched.php'); ?></td>
<td></td>
</tr>

</table>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

