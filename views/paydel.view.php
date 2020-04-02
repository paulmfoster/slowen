<form method="post" action="<?php echo $base_url; ?>paydel.php">
<?php $form->hidden('payee_id', $payee['payee_id']); ?>
<table>

<tr>
<td>
<label>Payee ID</label>
</td>
<td>
<?php echo $payee['payee_id']; ?>
</td>
</tr>

<tr>
<td>
<label>Payee Name</label>
</td>
<td>
<?php echo $payee['name']; ?>
</td>
</tr>

</table>

<p>
<?php form::abandon('payees.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</p>

</form>

