<form method="post" action="<?php echo $return; ?>">
<strong>Payee Name</strong>&nbsp;
<?php $form->text('name'); ?>
<br/>
<?php form::abandon('payadd.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

