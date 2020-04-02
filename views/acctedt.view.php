<form method="post" action="<?php echo $base_url . 'acctedt.php'; ?>">
<?php $form->hidden('acct_id', $acct['acct_id']); ?>
<table>

<tr>
<td>
<label>Account ID</label>
</td>
<td>
<?php echo $acct['acct_id']; ?>
</td>
</tr>

<tr>
<td>
<label for="parent">
Parent
</label>
</td>
<td>
<?php $form->select('parent', $acct['parent']); ?>
</td>
</tr>

<tr>
<td>
<label for="open_dt">
Open Date
</label>
</td>
<td>
<?php $form->text('open_dt', $acct['x_open_dt']); ?>
</td>
</tr>

<tr>
<td>
<label for="recon_dt">
Reconciliation Date
</label>
</td>
<td>
<?php $form->text('recon_dt', $acct['x_recon_dt']); ?>
</td>
</tr>

<tr>
<td>
<label for="acct_type">
Account Type
</label>
</td>
<td>
<?php $form->select('acct_type', $acct['acct_type']); ?>
</td>
</tr>

<tr>
<td>
<label for="name">
Name
</label>
</td>
<td>
<?php $form->text('name', $acct['name']); ?>
</td>
</tr>

<tr>
<td>
<label for="descrip">
Description
</label>
</td>
<td>
<?php $form->text('descrip', $acct['descrip']); ?>
</td>
</tr>

<tr>
<td>
<label for="open_bal">
Opening Balance
</label>
</td>
<td>
<?php $form->text('open_bal', $acct['x_open_bal']); ?>
</td>
</tr>

<tr>
<td>
<label for="balance">
Reconciled Balance
</label>
</td>
<td>
<?php $form->text('rec_bal', $acct['x_rec_bal']); ?>
</td>
</tr>

</table>

<p>
<?php form::abandon($base_url . 'accounts.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>
</p>

</form>
