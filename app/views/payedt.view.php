<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $this->return; ?>">
<label>Payee Name</label>&nbsp;
<?php $this->form->hidden('payee_id'); ?>
<?php $this->form->text('name'); ?>
<br/>
<?php form::abandon('index.php?url=pay/edit'); ?>
&nbsp;
<?php $this->form->submit('s1'); ?>
</form>
<?php include VIEWDIR . 'footer.view.php'; ?>

