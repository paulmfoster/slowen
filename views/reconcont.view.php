<form method="post" action="<?php echo $return; ?>">

<?php $form->hidden('from_acct'); ?>

<h3>Do you want to continue the reconciliation for <?php echo $name; ?>?</h3>
Click the check box to continue: <?php $form->checkbox('continue'); ?>
&nbsp;
<?php $form->submit('s1'); ?>

</form>
