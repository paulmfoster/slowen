
<!-- Main transaction data entry screen -->

<form action="<?php echo $base_url . 'txnaddk.php'; ?>" method="post">

<fieldset>
<table>

<tr>
<td><label for="from_acct">From Acct</label></td>
<td>
<!-- from_acct -->
<?php $form->select('from_acct'); ?>
</td>
</tr>

</table>

<table>
<tr>
<td><label for="txn_dt">Date</label>
&nbsp;
<!-- txn_dt -->
<?php $form->text('txn_dt', date::get(date::now(), 'm/d/y')); ?>
</td>
<td>
<label for="checkno">Check No</label>
&nbsp;
<!-- checkno -->
<?php $form->text('checkno'); ?>
</td>
</tr>

</table>
</fieldset>

<fieldset>

<table>

<tr>
<td><label for="payee_id">Payee Name</label></td>
<td>
<!-- payee_id -->
<?php $form->select('payee_id'); ?>
</td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td>
<!-- memo -->
<?php $form->text('memo'); ?>
</td>
</tr>

<tr>
<td><label for="to_acct">Category/Acct</label></td>
<td>
<!-- to_acct -->
<?php $form->select('to_acct'); ?>
</td>
</tr>

</table>

</fieldset>

<fieldset>

<table>

<!-- amount -->
<tr>
<td>
<label for="dr_amount">Amount</label>
&nbsp;
<?php $form->text('dr_amount'); ?>
</td>
</tr>
</table>

</fieldset>

<!-- max_splits -->
<fieldset>
<table>

<tr>
<td>
<label for="split">Has Split?</label>
&nbsp;
<?php $form->checkbox('split', 0); ?>
</td>
</tr>

<tr>
<td>
<label for="max_splits">Number of Splits</label>
&nbsp;
<?php $form->text('max_splits'); ?>
</td>
</tr>

</table>
</fieldset>

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('txnadd.php'); ?>
</p>


</form>

