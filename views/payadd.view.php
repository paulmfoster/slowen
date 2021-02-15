<form method="post" action="payadd2.php">
<strong>Payee Name</strong>&nbsp;
<?php $form->text('name'); ?>
<br/>
<?php form::abandon('payadd.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

