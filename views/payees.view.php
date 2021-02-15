<form method="post" action="<?php echo $destination; ?>">
<label>Payee</label>&nbsp;
<?php $form->select('payee_id'); ?>
<br/>
<?php form::abandon('index.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

