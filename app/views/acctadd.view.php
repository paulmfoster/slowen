<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $this->return; ?>">
<table>

<tr>
<td>
<label for="parent">
Parent
</label>
</td>
<td>
<?php $this->form->select('parent'); ?>
</td>
</tr>

<tr>
<td>
<label for="open_dt">
Open Date
</label>
</td>
<td>
<?php $this->form->date('open_dt', pdate::now2iso()); ?>
</td>
</tr>

<tr>
<td>
<label for="recon_dt">
Reconciliation Date
</label>
</td>
<td>
<?php $this->form->date('recon_dt'); ?>
</td>
</tr>

<tr>
<td>
<label for="acct_type">
Account Type
</label>
</td>
<td>
<?php $this->form->select('acct_type'); ?>
</td>
</tr>

<tr>
<td>
<label for="name">
Name
</label>
</td>
<td>
<?php $this->form->text('name'); ?>
</td>
</tr>

<tr>
<td>
<label for="descrip">
Description
</label>
</td>
<td>
<?php $this->form->text('descrip'); ?>
</td>
</tr>

<tr>
<td>
<label for="open_bal">
Opening Balance
</label>
</td>
<td>
<?php $this->form->text('open_bal'); ?>
</td>
</tr>

<tr>
<td>
<label for="rec_bal">
Reconciled Balance
</label>
</td>
<td>
<?php $this->form->text('rec_bal'); ?>
</td>
</tr>

</table>

<p>
<?php echo form::abandon('index.php?url=acct/add'); ?>
&nbsp;
<?php $this->form->submit('s1'); ?>
</p>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

