<form method="post" action="<?php echo $return; ?>">

<label for="category">Search by category</label>
<?php $form->select('category'); ?>
&nbsp;
<?php $form->submit('s1'); ?>

</form>
