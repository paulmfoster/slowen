<?php include VIEWDIR . 'head.view.php'; ?>
<strong>Enter year for yearly audit.</strong>

<form method="post" action="<?php echo $this->return; ?>">
<table>
<tr>
	<td><?php $this->form->select('year'); ?></td>
	<td><?php $this->form->submit('s1'); ?></td>
</tr>
</table>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>
