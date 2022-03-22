<?php include VIEWDIR . 'head.view.php'; ?>
<?php extract($data); ?>
<form method="post" action="<?php echo $this->return; ?>">
<?php $this->form->hidden('payee_id'); ?>
<table>

<tr>
<td>
<label>Payee ID</label>
</td>
<td>
<?php echo $payee['payee_id']; ?>
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
<?php form::abandon('index.php?url=pay/delete'); ?>
&nbsp;
<?php $this->form->submit('s1'); ?>
</p>

</form>
<?php include VIEWDIR . 'footer.view.php'; ?>
