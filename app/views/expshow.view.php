<?php include VIEWDIR . 'head.view.php'; ?>
<?php extract($data); ?>
<?php if ($expenses === FALSE): ?>
<h2>No expenses for the week</h2>
<?php else: ?>

<table>
<tr><th>Date</th><th>Account</th><th>Payee</th><th>Memo</th><th>Category</th><th>Amount</th></tr>

<?php foreach ($expenses as $expense): ?>

<tr>

<td>
<?php $txndt = new xdate(); ?>
<?php echo $txndt->iso2amer($expense['txn_dt']); ?>
</td>

<td>
<?php echo $expense['from_acct_name']; ?>
</td>

<td>
<?php echo $expense['payee_name']; ?>
</td>

<td>
<?php echo $expense['memo']; ?>
</td>

<td>
<?php echo $expense['to_acct_name']; ?>
</td>

<td align="right">
<?php echo int2dec($expense['amount']); ?>
</td>


</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

<?php include VIEWDIR . 'footer.view.php'; ?>

