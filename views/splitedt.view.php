
<fieldset>
<table>

<tr>
<td>
<label for="split">Has Split?</label>
&nbsp;
<?php echo $txn['split'] ? 'Yes' : 'No'; ?>
</td>
</tr>

<tr>
<td>
<label for="max_splits">Number of Splits</label>
&nbsp;
<?php echo $max_splits; ?>
</td>
</tr>

</table>
</fieldset>

<h3>Splits</h3>

<?php for ($b = 0; $b < $max_splits; $b++): ?>

<?php $form->hidden('split_id', $splits[$b]['id']); ?>

<fieldset>
<table>
<tr><th>#</th><th>Item</th><th>Value</th></tr>

<tr>
<td rowspan="4"><?php echo $b + 1; ?></td>
<td><label>Payee</label></td>
<td>
<?php $form->select('split_payee_id', $splits[$b]['payee_id']); ?>
</td>
</tr>

<tr>
<td><label>Destination Acct</label></td>
<td>
<?php $form->select('split_to_acct', $splits[$b]['to_acct']); ?>
</td>
</tr>

<tr>
<td><label>Memo</label></td>
<td>
<?php $form->text('split_memo', $splits[$b]['memo']); ?>
</td>
</tr>

<tr>
<td><label>Amount</label></td>
<td>
<?php $form->text('split_amount', int2dec($splits[$b]['amount'])); ?>
</td>
</tr>

</table>
</fieldset>

<?php endfor; ?>

