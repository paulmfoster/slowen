<?php include VIEWDIR . 'head.view.php'; ?>
<?php if ($expenses === FALSE): ?>
<h2>No expenses for the week</h2>
<?php else: ?>

<table>
<tr><th>Date</th><th>Account</th><th>Payee</th><th>Memo</th><th>Category</th><th>Amount</th></tr>

<?php $balance = 0; ?>

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

<?php $balance += $expense['amount']; ?>

<?php endforeach; ?>

<tr>
<td><strong>TOTAL</strong></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td align="right"><strong><?php echo int2dec($balance); ?></strong></td>
</tr>

</table>

<?php endif; ?>

<?php include VIEWDIR . 'footer.view.php'; ?>

