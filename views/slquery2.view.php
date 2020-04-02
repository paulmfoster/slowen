<h2>Query</h2>
<h3>
<?php echo $query; ?>
</h3>
<br/>
<h2>Query Answer</h2>
<?php if (defined('EXPERT')): ?>
<table>

<!-- HEADERS -->
<tr>
<?php $ct = count($headings); ?>
<?php for ($k = 0; $k < $ct; $k++): ?>
<th>
	<?php echo strtoupper($headings[$k]); ?>
</th>
<?php endfor; ?>
</tr>

<!-- DETAIL -->
<?php $ct2 = count($answer); ?>
<?php $y = 0; ?>
<?php for ($m = 0; $m < $ct2; $m++): ?>
<tr class="row<?php echo $y++ & 1;?>">
	<?php foreach ($answer[$m] as $key => $value): ?>
	<td>
<?php echo $value; ?>
	</td>
	<?php endforeach; ?>
</tr>
<?php endfor; ?>

</table>
<?php endif; ?>
