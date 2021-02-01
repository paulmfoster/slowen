
<form method="post" action="index.php?c=master&m=entity2">

<table>
<?php foreach ($entities as $entity): ?>
<tr>
	<td>
	<h2>
	<input type="radio" id="entity_num" name="entity_num" value="<?php echo $entity['entity_num']; ?>"/>
	</h2>
	</td>
	<td>
	<h2>
		<?php echo $entity['entity_name']; ?>
	</h2>
	</td>
</tr>
<?php endforeach; ?>
</table>
<p>
<input type="submit" id="selected" name="selected" value="Select"/>
</p>

</form>
