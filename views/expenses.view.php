
<form method="post" action="expenses2.php">
<label>From: </label><?php $form->date('from_date', $ifrom_date); ?>
&nbsp;
<label>To: </label><?php $form->date('to_date', $ito_date); ?>
<p>
<?php $form->submit('s1'); ?>
</form>

