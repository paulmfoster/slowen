
<form method="post" action="<?php echo $base_url; ?>accounts.php">
<label>Account</label>&nbsp;
<?php $form->select('acct_id'); ?>
<br/>
<?php form::abandon($base_url . 'accounts.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
&nbsp;
<?php $form->submit('s2'); ?>
&nbsp;
<?php $form->submit('s3'); ?>
</form>

