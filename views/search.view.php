
<form method="post" action="index.php?c=report&m=results">

<label for="vendor">Search by payee/vendor</label>

<?php $form->select('vendor'); ?>
&nbsp;
<?php $form->submit('s1'); ?>

<br/>

<label for="category">Search by category</label>
<?php $form->select('category'); ?>
&nbsp;
<?php $form->submit('s2'); ?>

</form>

