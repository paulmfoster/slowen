
<form method="post" action="<?php echo $destination; ?>">
<label>Account</label>&nbsp;
<?php $form->select('acct_id'); ?>
<br/>
<?php form::abandon('index.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

