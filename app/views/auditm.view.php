<?php include VIEWDIR . 'head.view.php'; ?>
<strong>Enter month and year for monthly audit.</strong>

<form method="post" action="<?php echo $return; ?>">
<table>
<tr>
	<td><?php $form->select('month'); ?></td>
	<td><?php $form->select('year'); ?></td>
	<td><?php $form->submit('s1'); ?></td>
</tr>
</table>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>
