<?php include VIEWDIR . 'head.view.php'; ?>

<!-- Main transaction data entry screen -->

<form action="<?php echo $this->return; ?>" method="post">

<table>

<tr>
<td><label for="from_acct">From Acct</label></td>
<td>
<!-- from_acct -->
<?php $this->form->select('from_acct'); ?>
</td>
</tr>

<tr>
<td><label for="txn_dt">Date</label>
</td>
<td>
<!-- txn_dt -->
<?php $this->form->date('txn_dt', pdate::now2iso()); ?>
</td>
</tr>

<tr>
<td><label for="payee_id">Payee Name</label></td>
<td>
<!-- payee_id -->
<?php $this->form->select('payee_id'); ?>
</td>
</tr>

<tr>
<td><label for="memo">Memo</label></td>
<td>
<!-- memo -->
<?php $this->form->text('memo'); ?>
</td>
</tr>

<tr>
<td><label for="to_acct">Category/Acct</label></td>
<td>
<!-- to_acct -->
<?php $this->form->select('to_acct'); ?>
</td>
</tr>

<!-- amount -->
<tr>
<td>
<label for="cr_amount">Credit</label>
</td>
<td>
<?php $this->form->text('cr_amount'); ?>
</td>
</tr>
</table>

<p>
<?php $this->form->submit('s1'); ?>
&nbsp;
<?php form::abandon('index.php?url=atxn/deposit'); ?>
</p>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

