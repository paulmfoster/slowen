<?php if ($stage == 'pick_date'): ?>

<form method="post" action="<?php echo $base_url . 'balances.php'; ?>">
Select a date; balances shown will be as of the end of that date<br/>
Date&nbsp;
<?php $form->text('last_dt'); ?>
<?php $form->submit('s1'); ?>
</form>

<?php elseif ($stage == 'show_bals'): ?>

<?php $j = 0; /* table shading counter */ ?>

<label>Balances as of: <?php echo $x_today; ?></label>

<table>
<tr>
<th>Account Name</th><th>Balance</th>
</tr>
<?php for ($i = 0; $i < $nbals; $i++): ?>
<tr class="row<?php echo ($j++ & 1);?>">
<td><?php echo $bals[$i]['name']; ?></td>
<?php if ($bals[$i]['balance'] < 0): ?>
<td align="right" class="red"><?php echo int2dec($bals[$i]['balance']); ?></td>
<?php else: ?>
<td align="right"><?php echo int2dec($bals[$i]['balance']); ?></td>
<?php endif; ?>
</tr>
<?php endfor; ?>
</table>

<?php endif; ?>
