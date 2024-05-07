<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $return; ?>">
<strong>Payee Name</strong>&nbsp;
<?php $form->text('name'); ?>
<br/>
<?php $form->submit('s1'); ?>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>
