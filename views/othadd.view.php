
<!-- Main transaction data entry screen -->

<form action="txnsplt.php" method="post">

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
<td><label for="txn_dt">Date</label></td>
<td><?php $form->date('txn_dt', pdate::now2iso()); ?></td>
</tr>

<tr>
<td><label for="checkno">Check No</label></td>
<td><?php $form->text('checkno'); ?></td>
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
<td><label for="status">Status</label></td>
<td><?php $form->select('status', ' '); ?></td>
</tr>

<tr>
<td><label for="recon_dt">Recon Dt</label>
<td><?php $form->date('recon_dt'); ?></td>
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
<td><label for="split">Has Split?</label></td>
<td><?php $form->checkbox('split', 0); ?></td>
</tr>

<tr>
<td><label for="max_splits">Number of Splits</label></td>
<td><?php $form->text('max_splits'); ?></td>
</tr>

<tr>
<td><?php $form->submit('s1'); ?>&nbsp;<?php form::abandon('othadd.php'); ?></td>
<td></td>
</tr>

</table>

</form>

