<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $this->return; ?>">
<strong>Payee Name</strong>&nbsp;
<?php $this->form->text('name'); ?>
<br/>
<?php $this->form->submit('s1'); ?>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>
