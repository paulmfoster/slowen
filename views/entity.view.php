
<form method="post" action="<?php echo $return; ?>">

<table>
<?php $num = count($entities); ?>
<?php for ($i = 0; $i < $num; $i++): ?>
<tr>
	<td>
	<h2>
	<input type="radio" id="entity_num" name="entity_num" value="<?php echo $entities[$i]['entity_num']; ?>" <?php if ($i == 0) echo 'checked="checked"'; ?>/>
	</h2>
	</td>
	<td>
	<h2>
		<?php echo $entities[$i]['entity_name']; ?>
	</h2>
	</td>
</tr>
<?php endfor; ?>
</table>
<p>
<input type="submit" id="selected" name="selected" value="Select"/>
</p>

</form>
