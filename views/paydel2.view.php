<form method="post" action="paydel3.php">
<?php $form->hidden('payee_id'); ?>
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
<?php form::abandon('paydel.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</p>

</form>
