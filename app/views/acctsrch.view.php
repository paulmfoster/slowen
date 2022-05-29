<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $this->return; ?>">

<label for="category">Search by category</label>
<?php $this->form->select('category'); ?>
&nbsp;
<?php $this->form->submit('s1'); ?>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>
