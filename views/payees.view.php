<form method="post" action="<?php echo $base_url; ?>payees.php">
<label>Payee</label>&nbsp;
<?php $form->select('payee_id'); ?>
<br/>
<?php form::abandon('payees.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
&nbsp;
<?php $form->submit('s2'); ?>
&nbsp;
<?php $form->submit('s3'); ?>
</form>

