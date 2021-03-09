
<!-- Main transaction data entry screen -->

<form action="<?php echo $return; ?>" method="post">

<table>

<tr>
<td><label for="from_acct">From Acct</label></td>
<td>
<?php $form->select('from_acct'); ?>
</td>
</tr>

<?php $form->hidden('xfer'); ?>

<tr>
<td><label for="txn_dt">Date</label>
</td>
<td>
<?php $form->date('txn_dt', pdate::now2iso()); ?>
</td>
</tr>

<tr>
<td>
<label for="checkno">Check No</label>
</td>
<td>
<?php $form->text('checkno'); ?>
</td>
</tr>

<tr>
<td><label for="payee_id">Payee Name</label></td>
<td>
<?php $form->select('payee_id'); ?>
</td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td>
<?php $form->text('memo'); ?>
</td>
</tr>

<tr>
<td><label for="to_acct">Category/Acct</label></td>
<td>
<?php $form->select('to_acct'); ?>
</td>
</tr>

<tr>
<td>
<label for="dr_amount">Debit</label>
</td>
<td>
<?php $form->text('dr_amount'); ?>
</td>
</tr>
</table>

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('index.php'); ?>
</p>

</form>

