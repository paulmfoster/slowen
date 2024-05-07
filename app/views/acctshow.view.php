<?php include VIEWDIR . 'head.view.php'; ?>

<form method="post" action="<?php echo $return; ?>">
<?php $form->hidden('id'); ?>
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
<?php $opendt = new xdate(); ?>
<?php echo $opendt->iso2amer($acct['open_dt']); ?>
</td>
</tr>

<tr>
<td class="tdlabel">
Reconciliation Date
</td>
<td>
<?php $rdt = new xdate(); ?>
<?php echo $rdt->iso2amer($acct['recon_dt']); ?>
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
<?php $form->submit('edit'); ?>
</td>
<td>
<?php $form->submit('delete'); ?>
</td>
</tr>

</table>


</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

