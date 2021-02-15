<form method="post" action="payedt3.php">
<label>Payee Name</label>&nbsp;
<?php $form->hidden('payee_id'); ?>
<?php $form->text('name', $payee['name']); ?>
<br/>
<?php form::abandon('payedt.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

