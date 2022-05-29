<?php include VIEWDIR . 'head.view.php'; ?>

<?php extract($data); ?>

<form method="post" action="<?php echo $this->return; ?>">
<?php $this->form->hidden('id'); ?>
<table>

<tr>
<td class="tdlabel">
Account ID
</td>
<td>
<?php echo $acct['id']; ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Name
</td>
<td>
<?php echo $acct['name']; ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Description
</td>
<td>
<?php echo $acct['descrip']; ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Parent
</td>
<td>
<?php echo $acct['x_parent']; ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Open Date
</td>
<td>
<?php echo pdate::iso2am($acct['open_dt']); ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Reconciliation Date
</td>
<td>
<?php $recon_dt = ($acct['recon_dt'] == 'NULL') ? NULL : $acct['recon_dt']; ?>
<?php echo pdate::iso2am($recon_dt); ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Account Type
</td>
<td>
<?php echo $acct['x_acct_type']; ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Opening Balance
</td>
<td>
<?php echo int2dec($acct['open_bal']); ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Reconciled Balance
</td>
<td>
<?php echo int2dec($acct['rec_bal']); ?>
</td>
</tr>

<tr>
<td class="tdlabel">
<?php $this->form->submit('edit'); ?>
</td>
<td>
<?php $this->form->submit('delete'); ?>
</td>
</tr>

</table>


</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

