
<p>
<h3>Please ensure the following is what you wish to store:</h3>
</p>

<table>

<tr>
<td align="right">
<label>From Acct</label>
</td>
<td>
<?php echo $data['from_acct'] . ' ' . $data['from_acct_name']; ?>
</td>
</tr>

<tr>
<td align="right">
<label>To Acct/Category</label>
</td>
<td>
<?php echo $data['to_acct'] . ' ' . $data['to_acct_name']; ?>
</td>
</tr>

<tr>
<td align="right">
<label>Check Date</label>
</td>
<td>
<?php echo $data['x_txn_dt']; ?>
</td>
</tr>

<tr>
<td align="right">
<label>Check No</label>
</td>
<td>
<?php echo $data['checkno']; ?>
</td>
</tr>

<tr>
<td align="right">
<label>Payee</label>
</td>
<td>
<?php echo $data['payee_id'] . ' ' . $data['payee_name']; ?>
</td>
</tr>

<tr>
<td align="right">
<label>Memo</label>
</td>
<td>
<?php echo $data['memo']; ?>
</td>
</tr>

<tr>
<td align="right">
<label>Amount</label>
</td>
<td>
<?php echo $data['dr_amount']; ?>
</td>
</tr>

</table>

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

<form action="<?php $base_url . 'txnvrfy.php'; ?>" method="post">

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('txnadd.php'); ?>
</p>

</form>

