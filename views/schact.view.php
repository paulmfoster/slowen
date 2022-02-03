
<?php if ($r === FALSE): ?>
<h2>No scheduled transactions.</h2>
<?php else: ?>

<h2>"Activating" means to cause a scheduled transaction to become<br/>
an <em>actual</em> transaction for this month. Check the boxes of<br/>
any scheduled transactions you wish to activate.</h2>

<form action="<?php echo $return; ?>" method="post">

<?php $row = 0; ?>
<table class="border-rules">

<tr>
<th>Activate?</th>
<th>From Acct</th>
<th>Day of Month</th>
<th>Payee/Memo</th>
<th>To Acct/Category</th>
<th>Debit</th>
<th>Credit</th>
</tr>

<?php foreach ($r as $txn): ?>
<tr class="row<?php echo $row++ & 1;?>">

<td><input type="checkbox" name="id_<?php echo $txn['id']; ?>" value="1"/></td>

<td><?php echo $txn['from_acct_name']; ?></td>
<td class="align-right"><?php echo $txn['txn_dom']; ?></td>

<td>
<?php echo $txn['payee_name']; ?>
<br/>
<?php echo $txn['memo']; ?>
</td>

<td><?php echo $txn['to_acct_name']; ?></td>

<?php if ($txn['amount'] < 0): ?>
<td class="align-right"><?php echo int2dec(- $txn['amount']); ?></td><td></td>
<?php else: ?>
<td></td><td class="align-right"><?php echo int2dec($txn['amount']); ?></td>
<?php endif; ?>

</tr>

<?php endforeach; ?>
</table>

<p>
<input type="submit" name="s1" value="Activate"/>
</p>

</form>

<?php endif; ?>
