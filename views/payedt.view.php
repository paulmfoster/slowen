<form method="post" action="<?php echo $base_url; ?>payedt.php">
<label>Payee Name</label>&nbsp;
<?php $form->hidden('payee_id', $payee['payee_id']); ?>
<?php $form->text('name', $payee['name']); ?>
<br/>
<?php form::abandon('payees.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

