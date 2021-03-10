

<?php $row = 0; ?>

<h2>Register for <?php echo $acct['name']; ?></h2>

<table class="border-rules">
<tr>
<th></th>
<th>Date</th>
<th>Check #</th>
<th>Split?</th>
<th>Payee/Memo/Category</th>
<th>Status</th>
<th>Debit</th>
<th>Credit</th>
<th>Balance</th>
</tr>
<tr>
<td colspan="8"></td>
<td class="row<?php echo $row++ & 1;?> align-right"><?php echo int2dec($acct['open_bal']); ?></td>
</tr>
<?php foreach ($r as $txn): ?>
<tr class="row<?php echo $row++ & 1;?>">
<td>

<a href="txnshow.php?txnid=<?php echo $txn['txnid']; ?>">Show</a>
<br/>
<a href="txnedt.php?txnid=<?php echo $txn['txnid']; ?>">Edit</a>
<br/>
<a href="txnvoid.php?txnid=<?php echo $txn['txnid']; ?>">Void</a>

</td>
<td><?php echo pdate::iso2am($txn['txn_dt']); ?></td>
<td><?php echo $txn['checkno']; ?></td>
<td><?php echo ($txn['split'] == 1) ? 'Y' : 'N'; ?></td>
<td><?php echo $txn['payee_name'] . '<br/>' . $txn['memo'] . '<br/>' . $txn['to_acct_name']; ?></td>
<td><?php echo $txn['status']; ?></td>
<td class="align-right"><?php echo ($txn['debit'] != 0) ? int2dec($txn['debit']) : ''; ?></td>
<td class="align-right"><?php echo ($txn['credit'] != 0) ? int2dec($txn['credit']) : ''; ?></td>
<td class="align-right"><?php echo int2dec($txn['balance']); ?></td>
</tr>
<?php endforeach; ?>
</table>
<p>
<a href="#">Top</a>
</p>

