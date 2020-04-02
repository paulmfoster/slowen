<?php if ($state == 0): ?>

<strong>Enter month and year:&nbsp;</strong>
<form method="post" action="<?php echo $base_url . 'audit.php'; ?>">
<?php $form->select('month'); ?>
&nbsp;
<?php $form->select('year'); ?>
<?php $form->submit('s1'); ?>
</form>

<?php elseif ($state == 1): ?>

<a name="top"></a>

<!-- balances -->

<h2>Audit for <?php echo $data['time_frame']; ?></h2>
<h2>Account Balances</h2>
<table>
<th>Account Name</th><th>Start Balance</th><th>End Balance</th><th>Difference</th>
<?php $nbals = count($data['balances']); ?>
<?php $row = 0; ?>
<?php for ($i = 0; $i < $nbals; $i++): ?>
<tr class="row<?php echo ($row++ & 1);?>">
<td><?php echo $data['balances'][$i]['acct_name']; ?></td>
<td align="right"><?php echo int2dec($data['balances'][$i]['from_bal']); ?></td>
<td align="right"><?php echo int2dec($data['balances'][$i]['to_bal']); ?></td>
<td align="right"><?php echo int2dec($data['balances'][$i]['diff_bal']); ?></td>
</tr>
<?php endfor; ?>
</table>

<h2>Income</h2>
<table>
<th>Category Name</th><th>Amount</th>
<?php $nincs = count($data['incomes']); ?>
<?php for ($i = 0; $i < $nincs; $i++): ?>
<tr class="row<?php echo ($row++ & 1);?>">
<td><?php echo $data['incomes'][$i]['cat_name']; ?></td>
<td align="right"><?php echo int2dec($data['incomes'][$i]['amount']); ?></td>
</tr>
<?php endfor; ?>
</table>

<h2>Expense</h2>
<table>
<th>Category Name</th><th>Amount</th>
<?php $nexps = count($data['expenses']); ?>
<?php for ($i = 0; $i < $nexps; $i++): ?>
<tr class="row<?php echo ($row++ & 1);?>">
<td><?php echo $data['expenses'][$i]['cat_name']; ?></td>
<td align="right"><?php echo int2dec($data['expenses'][$i]['amount']); ?></td>
</tr>
<?php endfor; ?>
</table>

<h2>Analysis</h2>
<table>
<th>Item</th><th>Total</th>
<?php $nitems = count($data['analysis']); ?>
<?php for ($i = 0; $i < $nitems; $i++): ?>
<tr class="row<?php echo ($row++ & 1);?>">
<td><?php echo $data['analysis'][$i]['name']; ?></td>
<td align="right"><?php echo int2dec($data['analysis'][$i]['total']); ?></td>
</tr>
<?php endfor; ?>
</table>

<a href="#top">Top</a>

<?php endif; ?>

