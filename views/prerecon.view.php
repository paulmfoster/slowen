
<form method="post" action="<?php echo $base_url . 'reconcile.php'; ?>">

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
Example: credit card balances are normally negative. Bank acct balances are normally positive.</td>
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
<?php $form->text('stmt_close_date'); ?>
</td>
</tr>

<tr>
<td colspan="2">Enter any fees as a separate transaction before proceeding.</td>
</tr>

</table>

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('reconcile.php'); ?>
</p>

</form>

