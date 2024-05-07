<?php include VIEWDIR . 'head.view.php'; ?>

<h2><?php echo $data['from_acct_name']; ?></h2>

<h3>Reconciliation failed. Your work has been saved.
<br/>
Final balances were off by <?php echo int2dec($data['difference']); ?>.
<br/>
Here are figures to assist in determining why.<h3>
   
<table>

<tr>
<td>
<label>Computer Start Balance</label>
</td>
<td align="right">
<?php echo int2dec($data['comp_start_bal']); ?>
</td>
</tr>

<tr>
<td>
<label>Total Transactions</label>
</td>
<td align="right">
<?php echo int2dec($data['comp_all_txns']); ?>
</td>
</tr>

<tr>
<td>
<label>Computer Ending Balance</label>
</td>
<td align="right">
<?php echo int2dec($data['comp_end_bal']); ?>
</td>
</tr>

<tr>
<td>
<label>Statement Ending Balance</label>
</td>
<td align="right">
<?php echo $data['stmt_end_bal']; ?>
</td>
</tr>

<tr>
<td>
<label>Uncleared Transactions</label>
</td>
<td align="right">
<?php echo int2dec($data['comp_uncleared_txns']); ?>
</td>
</tr>

<tr>
<td>
<label>Check Balance</label>
</td>
<td align="right">
<?php echo int2dec($data['check_bal']); ?>
</td>
</tr>

<tr>
<td>
<label>Difference</label>
</td>
<td align="right">
<?php echo int2dec($data['difference']); ?>
</td>
</tr>

</table>

<?php include VIEWDIR . 'footer.view.php'; ?>
