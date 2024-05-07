<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $return; ?>">
<label>Payee Name</label>&nbsp;
<?php $form->hidden('id'); ?>
<?php $form->text('name'); ?>
<br/>
<?php $form->submit('s1'); ?>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

