<?php include VIEWDIR . 'head.view.php'; ?>
<form action="<?php echo $return; ?>" method="post">
<?php $form->select('id'); ?>
<br/>
<?php $form->submit('edit'); ?>
</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
