<form method="post" action="index.php?c=payee&m=edit2">
<label>Payee Name</label>&nbsp;
<?php $form->hidden('payee_id'); ?>
<?php $form->text('name'); ?>
<br/>
<?php form::abandon('index.php?c=payee'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

