<?php include VIEWDIR . 'head.view.php' ?>

<form method="post" action="<?php echo $return; ?>">

<?php $row = 0; ?>

<table>

<tr><th>Item</th><th>Value</th></tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">From Account</td>
<td>
<?php echo $from_acct . ' ' . $from_acct_name; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">Date</td>
<td>
<?php $txndt = new xdate(); ?>
<?php echo $txndt->iso2amer($txn_dt); ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">Check #</td>
<td>
<?php echo $checkno; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel"># Split</td>
<td>
<?php echo $max_splits; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">Payee</td>
<td>
<?php echo $payee_id . ' ' . $payee_name; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">To Account</td>
<td>
<?php echo $to_acct . ' ' .  $to_acct_name; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">Memo</td>
<td>
<?php echo $memo; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">Status</td>
<td>
<?php echo $x_status; ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">Reconciliation Date</td>
<td>
<?php $recondt = new xdate(); ?>
<?php echo $recondt->iso2amer($recon_dt); ?>
</td>
</tr>

<tr class="row<?php echo $row++ & 1; ?>">
<td class="tdlabel">Amount</td>
<td>
<?php echo $amount; ?>
</td>
</tr>

</table>

<?php if ($max_splits > 0): ?>

<h3>Splits</h3>

<table rules="all" border="1">
<tr><th>#</th><th>Item</th><th>Value</th></tr>

<?php for ($k = 0; $k < $max_splits; $k++): ?>

<tr>
<td rowspan="4"><?php echo $k; ?></td>

<td><label>Payee</label></td>
<td>
<?php echo $split_payee_id[$k] . ' ' . $split_payee_name[$k]; ?>
</td>
</tr>

<tr>
<td><label>Destination Acct</label></td>
<td>
<?php echo $split_to_acct[$k] . ' ' . $split_to_name[$k]; ?>
</td>
</tr>

<tr>
<td><label>Memo</label></td>
<td>
<?php echo $split_memo[$k]; ?>
</td>
</tr>

<tr>
<td><label>Amount</label></td>
<td>
<?php 
if (!empty($split_cr_amount[$k])) {
    $split_amount[$k] = $split_cr_amount[$k];
}
else {
    $split_amount[$k] = - $split_dr_amount[$k];
}
?>
<?php echo $split_amount[$k]; ?>
</td>
</tr>

<?php endfor; ?>
</table>

<?php endif; /* has splits */ ?>

<p>
<?php $form->submit('confirm'); ?>
&nbsp;
<?php form::abandon('addtxn.php'); ?>
</p>

</form>

<?php include VIEWDIR . 'footer.view.php'; ?>

