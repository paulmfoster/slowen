<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $this->return; ?>">
<?php $this->form->select('id'); ?>
<br/>
<?php $this->form->submit('show'); ?>
&nbsp;
<?php $this->form->submit('edit'); ?>
&nbsp;
<?php $this->form->submit('delete'); ?>
</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
