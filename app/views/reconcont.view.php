<?php include VIEWDIR . 'head.view.php'; ?>
<?php extract($data); ?>
<form method="post" action="<?php echo $this->return; ?>">

<?php $this->form->hidden('from_acct'); ?>

<h3>Do you want to continue the reconciliation for <?php echo $name; ?>?</h3>
Click the check box to continue: <?php $this->form->checkbox('continue'); ?>
&nbsp;
<?php $this->form->submit('s1'); ?>

</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
