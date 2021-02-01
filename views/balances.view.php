<?php if ($stage == 'pick_date'): ?>

<form method="post" action="index.php?c=report&m=balances2">
Select a date; balances shown will be as of the end of that date<br/>
Date&nbsp;
<?php $form->date('last_dt', pdate::now2iso()); ?>
<?php $form->submit('s1'); ?>
</form>

<?php elseif ($stage == 'show_bals'): ?>

<?php $j = 0; /* table shading counter */ ?>

<label>Balances as of: <?php echo pdate::iso2am($today); ?></label>

<table>
<tr>
<th>Account Name</th><th>Balance</th>
</tr>
<?php foreach ($bals as $bal): ?>
<tr class="row<?php echo ($j++ & 1);?>">
<td><?php echo $bal['name']; ?></td>
<?php if ($bal['balance'] < 0): ?>
<td align="right" class="red"><?php echo int2dec($bal['balance']); ?></td>
<?php else: ?>
<td align="right"><?php echo int2dec($bal['balance']); ?></td>
<?php endif; ?>
</tr>
<?php endforeach; ?>
</table>

<?php endif; ?>
