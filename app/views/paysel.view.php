<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $this->return; ?>">
<label>Payee</label>&nbsp;
<?php $this->form->select('id'); ?>
<br/>
<?php form::abandon('index.php'); ?>
&nbsp;
<?php $this->form->submit('s1'); ?>
</form>
<?php include VIEWDIR . 'footer.view.php'; ?>

