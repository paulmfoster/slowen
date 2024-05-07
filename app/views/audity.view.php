<?php include VIEWDIR . 'head.view.php'; ?>
<strong>Enter year for yearly audit.</strong>

<form method="post" action="<?php echo $return; ?>">
<table>
<tr>
	<td><?php $form->select('year'); ?></td>
	<td><?php $form->submit('s1'); ?></td>
</tr>
</table>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>
