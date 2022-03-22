<?php include VIEWDIR . 'head.view.php'; ?>

<h3>Use this operation to determine how much you've spent<br/>
on a certain category within a specified time frame.</h3>
<form method="post" action="<?php echo $this->return; ?>">

<table>

<?php $this->form->show(); ?>

</table>

</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
