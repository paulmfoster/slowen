<?php include VIEWDIR . 'head.view.php'; ?>

<?php extract($data); ?>

<?php $row = 0; ?>

<table class="border-rules">

<tr>
<th>ID</th>
<th>Day</th>
<th>From Acct</th>
<th>Payee/Memo/Category</th>
<th>Debit</th>
<th>Credit</th>
</tr>

<?php foreach ($list as $t): ?>
<tr class="row<?php echo $row++ & 1; ?>">
<td><?php echo $t['id']; ?></td>
<td align="right"><?php echo $t['txn_dom']; ?></td>
<td><?php echo $t['from_acct_name']; ?></td>
<td>
<?php echo $t['payee_name']; ?><br/>
<?php echo $t['memo']; ?><br/>
<?php echo $t['to_acct_name']; ?>
</td>
<?php if ($t['amount'] > 0): ?>
<td>
</td>
<td align="right">
<?php echo int2dec($t['amount']); ?>
</td>
<?php elseif ($t['amount'] < 0): ?>
<td align="right">
<?php $amt = - $t['amount']; ?>
<?php echo int2dec($amt); ?>
</td>
<td>
</td>
<?php else: ?>
<td>
</td>
<td>
</td>
<?php endif; ?>
</tr>

<?php endforeach; ?>

</table>

<?php include VIEWDIR . 'footer.view.php'; ?>
