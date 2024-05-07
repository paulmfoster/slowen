<?php include VIEWDIR . 'head.view.php'; ?>

<?php if ($transactions === FALSE): ?>
<h3>No transactions for this payee...</h3>
<?php else: ?>

<h3>A "split" is a break-down of an overall credit or debit.<br/>
You will likely see the overall payment first, and then the splits in the table.<br/>
Splits will generally be positive. If your transaction involves<br/>
a check number, you may find that the splits share that check number.
</h3>

<table class="border-rules">
<tr>
<th></th>
<th>From Acct</th>
<th>Date</th>
<th>Check #</th>
<th>Split?</th>
<th>Payee/Memo/Category</th>
<th>Status</th>
<th>Debit</th>
<th>Credit</th>
</tr>
<tr>
<?php $k = 0; ?>
<?php $max_txns = count($transactions); ?>
<?php for ($j = 0; $j < $max_txns; $j++): ?>
<tr class="row<?php echo $k++ & 1;?>">
<td><a href="<?php echo 'showtxn.php?txnid=' . $transactions[$j]['txnid']; ?>">Show</a></td>
<td><?php echo $transactions[$j]['from_acct_name']; ?></td>
<?php $txndt = new xdate(); ?>
<?php $txndt->from_iso($transactions[$j]['txn_dt']); ?>
<td><?php echo $txndt->to_amer(); ?></td>
<td><?php echo $transactions[$j]['checkno']; ?></td>
<td>
<?php 
if ($transactions[$j]['split'] == 0) {
	echo 'N';
}
else {
	echo 'Y';
}
?>
</td>
<td><?php echo $transactions[$j]['payee_name'] . '<br/>' . $transactions[$j]['memo'] . '<br/>' . $transactions[$j]['to_acct_name']; ?></td>
<td><?php echo $transactions[$j]['status']; ?></td>

<?php if ($transactions[$j]['debit'] == 0): ?>
<td>&nbsp;</td>
<?php else: ?>
<td class="align-right"><?php echo int2dec($transactions[$j]['debit']); ?></td>
<?php endif; ?>

<?php if ($transactions[$j]['credit'] == 0): ?>
<td>&nbsp;</td>
<?php else: ?>
<td class="align-right"><?php echo int2dec($transactions[$j]['credit']); ?></td>
<?php endif; ?>

</tr>
<?php endfor; ?>
</table>

<?php endif; ?>

<?php include VIEWDIR . 'footer.view.php'; ?>
