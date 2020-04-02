<form method="post" action="<?php echo $base_url . 'reconcile.php'; ?>">


<?php if ($screen == 2): ?>

<!-- Listing screen -->

<?php $row = 0; ?>

<!-- data from prior screen -->
<?php $form2->hidden('stmt_begin_bal', $stmt_begin_bal); ?>
<?php $form2->hidden('int_stmt_begin_bal', $int_stmt_begin_bal); ?>
<?php $form2->hidden('stmt_end_bal', $stmt_end_bal); ?>
<?php $form2->hidden('int_stmt_end_bal', $int_stmt_end_bal); ?>
<?php $form2->hidden('from_acct', $from_acct); ?>
<?php $form2->hidden('from_acct_name', $from_acct_name); ?>
<?php $form2->hidden('stmt_close_date', $stmt_close_date); ?>

<h2><?php echo $from_acct_name; ?></h2>

<h3>Mark the status of all transactions which you statement says have cleared.</h3>

<table>
<tr>
<th>Date</th>
<th>Check #</th>
<th>Payee/Memo/Category</th>
<th>Status</th>
<th>Debit</th>
<th>Credit</th>
<th>Balance</th>
</tr>

<tr class="row<?php echo ($row++ & 1); ?>">
<td colspan="6"></td>
<td class="align-right"><?php echo $hr_open_bal; ?></td>
</tr>

<!-- start of records -->
<?php $max_txns = count($txns); ?>
<?php for ($j = 0; $j < $max_txns; $j++): ?>

<tr class="row<?php echo ($row++ & 1); ?>">

<td><?php echo $txns[$j]['x_txn_dt']; ?></td>
<td><?php echo $txns[$j]['checkno']; ?></td>

<td>
<?php echo $txns[$j]['payee_name']; ?><br/>
<?php echo $txns[$j]['memo']; ?><br/>
<?php echo $txns[$j]['to_acct_name']; ?>
</td>

<!-- status field -->
<?php if ($txns[$j]['status'] == ' '): ?>
<td><input type="checkbox" name="status[]" value="<?php echo $txns[$j]['txnid']; ?>"/>
<?php else: ?>
<td><?php echo $txns[$j]['status']; ?></td>
<?php endif; ?>

<td class="align-right"><?php echo $txns[$j]['debit']; ?></td>
<td class="align-right"><?php echo $txns[$j]['credit']; ?></td>
<td class="align-right"><?php echo $txns[$j]['balance']; ?></td>
</tr>
<?php endfor; /* transaction loop */ ?>

</table>
<?php $form2->submit('s3'); ?>
&nbsp;
<?php form::abandon('reconcile.php'); ?>

<!-- Final screen -->

<?php elseif ($screen == 3): ?>

<h2><?php echo $data['from_acct_name']; ?></h2>

<h3>Reconciliation failed. Here are figures to assist in determining why.<h3>

<table>

<tr>
<td>Opening<br/>Balance</td>
<td class="align-right"><?php echo $data['open_bal']; ?></td>
<td>Statement<br/>Ending Bal</td>
<td class="align-right"><?php echo $data['stmt_end_bal']; ?></td>
</tr>

<tr>
<td>Total<br/>Transactions</td>
<td class="align-right">+<?php echo $data['all_txns']; ?></td>
<td>Uncleared<br/>Transactions</td>
<td class="align-right">+<?php echo $data['total_uncleared']; ?></td>
</tr>

<tr>
<td></td>
<td></td>
<td>Statement<br/>Cleared Txns</td>
<td class="align-right">-<?php echo $data['stmt_cleared']; ?> </td>
</tr>

<tr>
<td>Reference<br/>Balance</td>
<td class="align-right">=<?php echo $data['ref_bal']; ?></td>
<td>Check<br/>Balance</td>
<td class="align-right">=<?php echo $data['check_bal']; ?></td>
</tr>

</table>

<?php endif; ?>
</form>


