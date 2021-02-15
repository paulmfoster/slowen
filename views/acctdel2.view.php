<form method="post" action="acctdel3.php">
<?php $form->hidden('acct_id'); ?>
<table>

<tr>
<td>
<strong>Account ID</strong>
</td>
<td>
<?php echo $acct['acct_id']; ?>
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
<?php echo pdate::iso2am($acct['open_dt']); ?>
</td>
</tr>

<tr>
<td>
<strong>Reconciliation Date</strong>
</td>
<td>
<?php echo pdate::iso2am($acct['recon_dt']); ?>
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
<?php form::abandon('acctdel.php'); ?>
&nbsp;
<?php $form->submit('s1'); ?>

</form>
