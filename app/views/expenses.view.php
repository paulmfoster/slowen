<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $return; ?>">
<label>From: </label><?php $form->date('from_date'); ?>
&nbsp;
<label>To: </label><?php $form->date('to_date'); ?>
<p>
<?php $form->submit('s1'); ?>
</form>
<?php include VIEWDIR . 'footer.view.php'; ?>

