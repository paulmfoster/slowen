<?php include VIEWDIR . 'head.view.php'; ?>

<h2>Expenses from: <?php echo $from; ?> to <?php echo $to; ?></h2>
<h2>Category: <?php echo $txns[0]['to_acct_name']; ?></h2>

<?php $j = 0; ?>

<table>

<tr><th>Date</th><th>Account</th><th>Payee</th><th>Category</th><th>Amount</th></tr>

<?php foreach ($txns as $txn): ?>
<tr>
<td><?php echo $txn['txn_dt']; ?></td>
<td><?php echo $txn['from_acct_name']; ?></td>
<td><?php echo $txn['payee_name']; ?></td>
<td><?php echo $txn['to_acct_name']; ?></td>
<td align="right"><?php echo int2dec($txn['amount']); ?></td>
</tr>
<?php endforeach; ?>
<tr>
<td><strong>TOTAL</strong></td>
<td></td>
<td></td>
<td></td>
<td align="right"><strong><?php echo int2dec($balance); ?></strong></td>
</tr>

</table>

<?php include VIEWDIR . 'footer.view.php'; ?>
