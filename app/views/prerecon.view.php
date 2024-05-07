<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $return; ?>">

<!-- Preliminary screen -->

<table>
<tr>
<td>
<label for="from_acct">Account to reconcile</label>
</td>
<td>
<?php $form->select('from_acct'); ?>
</td>
</tr>

<tr>
<td colspan="2" class="red">NOTE: Adjust signs of balances to the type of account and circumstances.<br/>
Example: credit card balances are normally negative. Bank acct balances<br/>
are normally positive.</td>
</tr>

<tr>
<td>
<label for="stmt_start_bal">Starting balance</label>
</td>
<td>
<?php $form->text('stmt_start_bal'); ?>
</td>
</tr>

<tr>
<td>
<label for="stmt_end_bal">Ending balance</label>
</td>
<td>
<?php $form->text('stmt_end_bal'); ?>
</td>
</tr>

<tr>
<td>
<label for="stmt_close_date">Closing Date</label>
</td>
<td>
<?php $form->date('stmt_close_date'); ?>
</td>
</tr>

<tr>
<td></td>
<td><h3>Statement Fee</h3></td>
</tr>

<tr>
<td>
<label for="Payee">Payee</label>
</td>
<td><?php $form->select('payee_id'); ?></td>
</tr>

<tr>
<td>
<label for="Payee">To Account</label>
</td>
<td><?php $form->select('to_acct'); ?></td>
</tr>
<tr>

<td>
<label for="fee">Fee Amount</label>
</td>
<td><?php $form->text('fee'); ?></td>
</tr>

</table>

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('reconcile.php'); ?>
</p>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>
