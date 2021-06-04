
<h3>Existing Entities</h3>
<table>
<th>Number</th><th>Name</th>
<?php foreach ($entities as $e): ?>
<tr>
<td><?php echo $e['entity_num']; ?></td><td><?php echo $e['entity_name']; ?></td>
</tr>
<?php endforeach; ?>
</table>
<h3>New Entity</h3>
<form method="post" action="<?php echo $return; ?>">
<?php $form->hidden('number'); ?>
<label>Number</label>&nbsp;<?php echo $next; ?>
<br/>
	<label>Name</label>&nbsp;<?php $form->text('name'); ?>
<br/>
<?php $form->submit('s1'); ?>

</form>
