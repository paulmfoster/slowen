<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $return; ?>">

<?php $form->hidden('from_acct'); ?>

<h3>
    Account: <?php echo $name; ?><br/>
    A reconciliation is in progress on this account.<br/>
    If you want to continue using previously entered values<br/>
    and date, click this checkbox: <?php $form->checkbox('continue'); ?><br/>
</h3>

<?php $form->submit('s1'); ?>

</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
