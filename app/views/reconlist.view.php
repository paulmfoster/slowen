<?php include VIEWDIR . 'head.view.php'; ?>
<?php extract($data); ?>
<form method="post" action="<?php echo $this->return; ?>">

<!-- Listing screen -->

<?php $row = 0; ?>

<!-- data from prior screen -->
<?php $this->form->hidden('stmt_start_bal'); ?>
<?php $this->form->hidden('stmt_close_date'); ?>
<?php $this->form->hidden('stmt_end_bal'); ?>
<?php $this->form->hidden('from_acct'); ?>
<?php $this->form->hidden('from_acct_name'); ?>

<h2><?php echo $from_acct_name; ?></h2>

<h3>Mark the status of all transactions which you statement says have cleared.</h3>

<table>
<tr>
<th>Date</th>
<th>Check #</th>
<th>Split</th>
<th>Payee/Memo/Category</th>
<th>Status</th>
<th>Debit</th>
<th>Credit</th>
</tr>

<!-- start of records -->
<?php foreach ($txns as $txn): ?>

<tr class="row<?php echo ($row++ & 1); ?>">

<td><?php echo pdate::iso2am($txn['txn_dt']); ?></td>
<td><?php echo $txn['checkno']; ?></td>

<td><?php echo $txn['split'] ? 'Yes' : 'No'; ?></td>

<td>
<?php echo $txn['payee_name']; ?><br/>
<?php echo $txn['memo']; ?><br/>
<?php echo $txn['to_acct_name']; ?>
</td>

<!-- status field -->
<td><input type="checkbox" name="status[]" value="<?php echo $txn['id']; ?>" <?php echo ($txn['status'] == 'C') ? 'checked' : ''; ?>/>

<td class="align-right"><?php echo $txn['debit']; ?></td>
<td class="align-right"><?php echo $txn['credit']; ?></td>
</tr>
<?php endforeach; /* transaction loop */ ?>

</table>
<p>
<?php $this->form->submit('s3'); ?>
&nbsp;
<?php form::abandon(url('recon', 'prelim')); ?>
</p>
</form>

<?php include VIEWDIR . 'footer.view.php'; ?>
