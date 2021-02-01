
<?php if ($stage == 1): ?>

<form method="post" action="index.php?c=report&m=expenses2">
<label>From: </label><?php $form->date('from_date'); ?>
&nbsp;
<label>To: </label><?php $form->date('to_date'); ?>
<p>
<?php $form->submit('s1'); ?>
</form>

<?php elseif ($stage == 2): ?>

<?php if ($expenses === FALSE): ?>
<h2>No expenses for the week</h2>
<?php else: ?>

<table>
<tr><th>Date</th><th>Payee</th><th>Memo</th><th>Category</th><th>Amount</th></tr>

<?php foreach ($expenses as $expense): ?>

<tr>

<td>
<?php echo pdate::iso2am($expense['txn_dt']); ?>
</td>

<td>
<?php echo $expense['payeename']; ?>
</td>

<td>
<?php echo $expense['memo']; ?>
</td>

<td>
<?php echo $expense['acctname']; ?>
</td>

<td align="right">
<?php echo int2dec($expense['amount']); ?>
</td>


</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

<?php endif; ?>

