
<?php if ($txns === FALSE): ?>
<h3>No transactions for this category/time period...</h3>
<?php else: ?>

<h3>Total of transactions: <?php echo int2dec($bal); ?></h3>
<h4>(Split amounts are not included.)</h4>

<table class="border-rules">
<tr>
<th>From Acct</th>
<th>Date</th>
<th>Check #</th>
<th>Split?</th>
<th>Payee/Memo/Category</th>
<th>Status</th>
<th>Amount</th>
</tr>
<tr>
<?php $k = 0; ?>
<?php $max_txns = count($txns); ?>
<?php for ($j = 0; $j < $max_txns; $j++): ?>
<tr class="row<?php echo $k++ & 1;?>">
<td><?php echo $txns[$j]['from_acct_name']; ?></td>
<td><?php echo pdate::iso2am($txns[$j]['txn_dt']); ?></td>
<td><?php echo $txns[$j]['checkno']; ?></td>
<td>
<?php 
if ($txns[$j]['split'] == 0) {
	echo 'N';
}
else {
	echo 'Y';
}
?>
</td>
<td><?php echo $txns[$j]['payee_name'] . '<br/>' . $txns[$j]['memo'] . '<br/>' . $txns[$j]['to_acct_name']; ?></td>
<td><?php echo $txns[$j]['status']; ?></td>
<td class="align-right"><?php echo int2dec($txns[$j]['amount']); ?></td>
</tr>
<?php endfor; ?>
</table>

<?php endif; ?>

