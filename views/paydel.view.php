<form method="post" action="index.php?c=payee&m=delete2">
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
<?php form::abandon('index.php?c=payee'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</p>

</form>

