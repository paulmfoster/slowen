<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $this->return; ?>">
Select a date; balances shown will be as of the end of that date<br/>
Date&nbsp;
<?php $this->form->date('last_dt'); ?>
<?php $this->form->submit('s1'); ?>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

