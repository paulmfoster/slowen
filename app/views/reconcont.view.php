<?php include VIEWDIR . 'head.view.php'; ?>
<?php extract($data); ?>
<form method="post" action="<?php echo $this->return; ?>">

<?php $this->form->hidden('from_acct'); ?>

<h3>
    Account: <?php echo $name; ?><br/>
    A reconciliation is in progress on this account.<br/>
    If you want to continue using previously entered values<br/>
    and date, click this checkbox: <?php $this->form->checkbox('continue'); ?><br/>
</h3>

<?php $this->form->submit('s1'); ?>

</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
