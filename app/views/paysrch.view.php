<?php include VIEWDIR . 'head.view.php'; ?>
<form method="post" action="<?php echo $return; ?>">

<label for="payee">Search by payee/vendor</label>

<?php $form->select('payee'); ?>
&nbsp;
<?php $form->submit('s1'); ?>

</form>
<?php include VIEWDIR . 'footer.view.php'; ?>

