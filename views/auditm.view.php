
<strong>Enter month and year for monthly audit.</strong>

<form method="post" action="audit2.php">
<table>
<tr>
	<td><?php $form->select('month'); ?></td>
	<td><?php $form->select('year'); ?></td>
	<td><?php $form->submit('s1'); ?></td>
</tr>
</table>
</form>

