<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $return; ?>">
<label>Account</label>&nbsp;
<?php $form->select('id'); ?>
<br/>
<?php form::abandon('index.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

