<form method="post" action="index.php?c=payee&m=index2">
<label>Payee</label>&nbsp;
<?php $form->select('payee_id'); ?>
<br/>
<?php form::abandon('index.php?c=payee'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
&nbsp;
<?php $form->submit('s2'); ?>
&nbsp;
<?php $form->submit('s3'); ?>
</form>

