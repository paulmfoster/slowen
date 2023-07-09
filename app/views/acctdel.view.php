<?php include VIEWDIR . 'head.view.php'; ?>

<?php extract($data); ?>

<form method="post" action="<?php echo $this->return; ?>">
<?php $this->form->hidden('id'); ?>
<table>

<tr>
<td>
<strong>Account ID</strong>
</td>
<td>
<?php echo $acct['id']; ?>
</td>
</tr>

<tr>
<td>
<strong>Name</strong>
</td>
<td>
<?php echo $acct['name']; ?>
</td>
</tr>

<tr>
<td>
<strong>Description</strong>
</td>
<td>
<?php echo $acct['descrip']; ?>
</td>
</tr>

<tr>
<td>
<strong>Parent</strong>
</td>
<td>
<?php echo $acct['x_parent']; ?>
</td>
</tr>

<tr>
<td>
<strong>Open Date</strong>
</td>
<td>
<?php $opendt = new xdate(); ?>
<?php echo $opendt->iso2amer($acct['open_dt']); ?>
</td>
</tr>

<tr>
<td>
<strong>Reconciliation Date</strong>
</td>
<td>
<?php $recondt = new xdate(); ?>
<?php echo $recondt->iso2amer($acct['recon_dt']); ?>
</td>
</tr>

<tr>
<td>
<strong>Account Type</strong>
</td>
<td>
<?php echo $acct['x_acct_type']; ?>
</td>
</tr>

<tr>
<td>
<strong>Opening Balance</strong>
</td>
<td>
<?php echo int2dec($acct['open_bal']); ?>
</td>
</tr>

<tr>
<td>
<strong>Reconciled Balance</strong>
</td>
<td>
<?php echo int2dec($acct['rec_bal']); ?>
</td>
</tr>

</table>

<p>
<?php $this->form->submit('s1'); ?>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

