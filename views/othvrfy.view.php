
<p>
<h3>Please ensure the following is what you wish to store:</h3>
</p>

<table>

<tr>
<td align="right">
From Acct
</td>
<td>
<?php echo $data['from_acct'] . ' ' . $data['from_acct_name']; ?>
</td>
</tr>

<tr>
<td align="right">
To Acct/Category
</td>
<td>
<?php echo $data['to_acct'] . ' ' . $data['to_acct_name']; ?>
</td>
</tr>

<tr>
<td align="right">
Transaction Date
</td>
<td>
<?php echo pdate::iso2am($data['txn_dt']); ?>
</td>
</tr>

<tr>
<td align="right">
Check No
</td>
<td>
<?php echo $data['checkno']; ?>
</td>
</tr>

<tr>
<td align="right">
Payee
</td>
<td>
<?php echo $data['payee_id'] . ' ' . $data['payee_name']; ?>
</td>
</tr>

<tr>
<td align="right">
Memo
</td>
<td>
<?php echo $data['memo']; ?>
</td>
</tr>

<tr>
<td align="right">
Amount
</td>
<td>
<?php echo 'DR ' . $data['dr_amount'] . ' | CR ' . $data['cr_amount']; ?>
</td>
</tr>

<tr>
<td align="right">
Status
</td>
<td>
<?php echo $data['status_descrip']; ?>
</td>
</tr>

<tr>
<td align="right">
Recon Date
</td>
<td>
<?php echo pdate::iso2am($data['recon_dt']); ?>
</td>
</tr>

</table>

<?php if (isset($data['xfer']) && $data['xfer'] == 1): ?>

<p>
<h3>Inter-Account Transfer</h3>
</p>

<table>

<tr>
<td align="right">
From Acct
</td>
<td>
<?php echo $data['to_acct'] . ' ' . $data['to_acct_name']; ?>
</td>
</tr>

<tr>
<td align="right">
To Acct/Category
</td>
<td>
<?php echo $data['from_acct'] . ' ' . $data['from_acct_name']; ?>
</td>
</tr>

<tr>
<td align="right">
Transaction Date
</td>
<td>
<?php echo pdate::iso2am($data['txn_dt']); ?>
</td>
</tr>

<tr>
<td align="right">
Check No
</td>
<td>
<?php echo $data['checkno']; ?>
</td>
</tr>

<tr>
<td align="right">
Payee
</td>
<td>
<?php echo $data['payee_id'] . ' ' . $data['payee_name']; ?>
</td>
</tr>

<tr>
<td align="right">
Memo
</td>
<td>
<?php echo $data['memo']; ?>
</td>
</tr>

<tr>
<td align="right">
Amount
</td>
<td>
<?php echo 'DR ' . $data['cr_amount'] . ' | CR ' . $data['dr_amount']; ?>
</td>
</tr>

<tr>
<td align="right">
Status
</td>
<td>
<?php echo $data['status_descrip']; ?>
</td>
</tr>

<tr>
<td align="right">
Recon Date
</td>
<td>
<?php echo pdate::iso2am($data['recon_dt']); ?>
</td>
</tr>

</table>

<?php endif; ?>

<!-- SPLITS SECTION -->

<?php if (isset($data['split']) && $data['max_splits'] > 0): ?>

<br/>

<table rules="all" border="1">

<tr><th colspan="3">Splits</th></tr>
<tr><th>Split #</th><th>Item</th><th>Value</th></tr>

<?php for ($t = 0; $t < $data['max_splits']; $t++): ?> 

<tr>

<td rowspan="4">
Split <?php echo ($t + 1); ?>
</td>

<td>
Payee
</td>
<td>
<?php echo $data['split_payee_id'][$t] . ' ' . $data['split_payee_name'][$t]; ?>
</td>
</tr>

<tr>
<td>
Destination Acct
</td>
<td>
<?php echo $data['split_to_acct'][$t] . ' ' . $data['split_to_name'][$t]; ?>
</td>
</tr>

<tr>
<td>
Memo
</td>
<td>
<?php echo $data['split_memo'][$t]; ?>
</td>
</tr>

<tr>

<?php if (!empty($data['split_dr_amount'][$t])): ?>

<td>
Debit
</td>

<td>
<?php echo $data['split_dr_amount'][$t]; ?>
</td>

<?php elseif (!empty($data['split_cr_amount'][$t])): ?>

<td>
Credit
</td>

<td>
<?php echo $data['split_cr_amount'][$t]; ?>
</td>

<?php endif; ?>

</tr>

<?php endfor; ?>

</table>

<?php endif; ?>

<form action="txnsave.php" method="post">

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('txnadd.php'); ?>
</p>

</form>

