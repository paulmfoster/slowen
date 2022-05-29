<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $this->return; ?>">

<label for="payee">Search by payee/vendor</label>

<?php $this->form->select('payee'); ?>
&nbsp;
<?php $this->form->submit('s1'); ?>

</form>
<?php include VIEWDIR . 'footer.view.php'; ?>

