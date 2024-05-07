<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $return; ?>">
<?php $form->select('id'); ?>
<br/>
<?php $form->submit('show'); ?>
&nbsp;
<?php $form->submit('edit'); ?>
&nbsp;
<?php $form->submit('delete'); ?>
</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
