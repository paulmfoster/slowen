
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
<?php echo $data['x_txn_dt']; ?>
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
<?php echo 'CR ' . $data['cr_amount']; ?>
</td>
</tr>

</table>

<form action="<?php $base_url . 'depvrfy.php'; ?>" method="post">

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('txnadd.php'); ?>
</p>

</form>

