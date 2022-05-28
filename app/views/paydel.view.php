<?php include VIEWDIR . 'head.view.php'; ?>
<?php extract($data); ?>
<form method="post" action="<?php echo $this->return; ?>">
<?php $this->form->hidden('id'); ?>
<table>

<tr>
<td>
<label>Payee ID</label>
</td>
<td>
<?php echo $payee['id']; ?>
</td>
</tr>

<tr>
<td>
<label>Payee Name</label>
</td>
<td>
<?php echo $payee['name']; ?>
</td>
</tr>

</table>

<p>
<?php $this->form->submit('s1'); ?>
</p>

</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
