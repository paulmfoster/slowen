<form method="post" action="<?php echo $base_url; ?>payadd.php">
<strong>Payee Name</strong>&nbsp;
<?php $form->text('name'); ?>
<br/>
<?php form::abandon('payees.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

