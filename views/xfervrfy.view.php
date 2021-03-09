
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
<?php echo 'DR ' . $data['dr_amount']; ?>
</td>
</tr>

</table>


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
<?php echo 'CR ' . $data['dr_amount']; ?>
</td>
</tr>

</table>


<form action="<?php echo $return; ?>" method="post">

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('xferadd.php'); ?>
</p>

</form>

