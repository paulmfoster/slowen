<?php include VIEWDIR . 'head.view.php'; ?>

<h2>Budget for week ending <?php echo $budgetweek; ?></h2>

<form action="<?php echo $return; ?>" method="post">

<?php $form->hidden('wedate'); ?>
<?php $form->hidden('hr_wedate'); ?>

<!-- buttons -->

<p>
<?php $form->submit('abandon1'); ?>
<?php $form->submit('restart1'); ?>
<?php $form->submit('recalc1'); ?>
<?php $form->submit('save1'); ?>
<?php $form->submit('comp1'); ?>
</p>

<!-- end buttons -->

<table>

<!-- titles -->
<tr>
<th>Acct Name</th>
<th>Typ Due</th>
<th>Period</th>
<th>Wkly S/A</th>
<th>Prior S/A</th>
<th>Addl S/A</th>
<th>Paid</th>
<th>New S/A</th>
</tr>
<!-- end titles -->

<?php $max_recs = count($cells); ?>
<?php $z = 0; ?>
<?php for ($j = 0; $j < $max_recs; $j++): ?>

<tr class="row<?php echo $z++ & 1; ?>">

<?php $form->hidden("acctname[$j]"); ?>
<?php $form->hidden("acctnum[$j]"); ?>
<?php $form->hidden("from_acct[$j]"); ?>
<?php $form->hidden("payee_id[$j]"); ?>
<?php $form->hidden("to_acct[$j]"); ?>
<?php $form->hidden("period[$j]"); ?>
<?php $form->hidden("typdue[$j]"); ?>


<td><?php echo $cells[$j]['acctname']; ?></td>

<td class="align-right">
<?php echo int2dec($cells[$j]['typdue']); ?>
</td>

<td>
<?php
switch ($cells[$j]['period']) {
case 'Y': echo 'Yearly';
    break;
case 'S': echo 'Semi-Annually';
    break;
case 'Q': echo 'Quarterly';
    break;
case 'M': echo 'Monthly';
    break;
case 'W': echo 'Weekly';
    break;
}
?>
</td>

<td class="align-right">
<?php $form->text("wklysa[$j]", int2dec($cells[$j]['wklysa'])); ?>
</td>

<td class="align-right">
<?php $form->text("priorsa[$j]", int2dec($cells[$j]['priorsa'])); ?>
</td>

<td class="align-right">
<?php $form->text("addlsa[$j]", int2dec($cells[$j]['addlsa'])); ?>
</td>

<td class="align-right">
<?php $form->text("paid[$j]", int2dec($cells[$j]['paid'])); ?>
</td>

<td class="align-right">
<?php $form->hidden("newsa[$j]"); ?>

<?php if ($cells[$j]['red']): ?>
<span class="red">
<?php echo int2dec($cells[$j]['newsa']); ?>
</span>
<?php else: ?>
<?php echo int2dec($cells[$j]['newsa']); ?>
<?php endif; ?>

</td>

</tr>

<?php endfor; ?>

<tr class="row<?php echo $z++ & 1; ?>">

<td><bold>TOTALS</bold></td>
<td></td>
<td></td>

<td>
<?php $form->hidden('total_wklysa'); ?>
<?php echo int2dec($totals['wklysa']); ?>
</td>

<td>
<?php $form->hidden('total_priorsa'); ?>
<?php echo int2dec($totals['priorsa']); ?>
</td>

<td>
<?php $form->hidden('total_addlsa'); ?>
<?php echo int2dec($totals['addlsa']); ?>
</td>

<td>
<?php $form->hidden('total_paid'); ?>
<?php echo int2dec($totals['paid']); ?>
</td>

<td>
<?php $form->hidden('total_newsa'); ?>
<?php echo int2dec($totals['newsa']); ?>
</td>

</tr>

</table>

<!-- buttons -->

<p>
<?php $form->submit('abandon2'); ?>        
<?php $form->submit('restart2'); ?>
<?php $form->submit('recalc2'); ?>
<?php $form->submit('save2'); ?>
<?php $form->submit('comp2'); ?>
</p>

<!-- end buttons -->

<p>
<strong>Abandon</strong> the budget process and revert to pre-budget numbers<br/>
<strong>Restart</strong> and eliminate all changes made to this point.<br/>
<strong>Recalculate</strong> recalculates the "New S/A" column and the Totals row.<br/>
<strong>Save</strong> saves your work to disk, but does not end the edit process.<br/>
<strong>Complete</strong> marks your budget as complete. Does not "save" your work. See above.<br/>
</p>

<form>

<?php $row = 0; /* table shading counter */ ?>

<h2>Balances as of: <?php echo $totalsweek; ?></h2>

<table>
<tr>
<th>Account Name</th><th>Balance</th>
</tr>
<?php foreach ($bals as $bal): ?>
<?php if ($bal['balance'] != 0): ?>
<tr class="row<?php echo ($row++ & 1);?>">
<td><?php echo $bal['name']; ?></td>
<?php if ($bal['balance'] < 0): ?>
<td align="right" class="red"><?php echo int2dec($bal['balance']); ?></td>
<?php else: ?>
<td align="right"><?php echo int2dec($bal['balance']); ?></td>
<?php endif; ?>
</tr>
<?php endif; ?>
<?php endforeach; ?>
</table>

<?php include VIEWDIR . 'footer.view.php'; ?>
