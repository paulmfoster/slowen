
<!-- Main transaction data entry screen -->

<form action="index.php?c=addtxn&m=verify" method="post">

<?php $form->hidden('txntype'); ?>
<?php $form->hidden('status'); ?>
<?php $form->hidden('recon_dt'); ?>

<fieldset>
<table>

<tr>
<td><label for="from_acct">From Acct</label></td>
<td>
<!-- from_acct -->
<?php $form->select('from_acct'); ?>
</td>
</tr>

<?php $form->hidden('xfer'); ?>

</table>

<table>
<tr>
<td><label for="txn_dt">Date</label>
&nbsp;
<!-- txn_dt -->
<?php $form->date('txn_dt', pdate::now2iso()); ?>
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
<label for="amount">Debit</label>
&nbsp;
<?php $form->text('amount'); ?>
</td>
</tr>
</table>

</fieldset>


</table>
</fieldset>

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('index.php?c=addtxn&m=add'); ?>
</p>


</form>

