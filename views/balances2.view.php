
<?php $j = 0; /* table shading counter */ ?>

<h2>Balances as of: <?php echo pdate::iso2am($today); ?></h2>

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

