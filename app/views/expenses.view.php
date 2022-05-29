<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $this->return; ?>">
<label>From: </label><?php $this->form->date('from_date'); ?>
&nbsp;
<label>To: </label><?php $this->form->date('to_date'); ?>
<p>
<?php $this->form->submit('s1'); ?>
</form>
<?php include VIEWDIR . 'footer.view.php'; ?>

