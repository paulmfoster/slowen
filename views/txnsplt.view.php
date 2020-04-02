
<form action="<?php echo $base_url . 'txnsplt.php'; ?>" method="post">

<?php $form->hidden('max_splits', $_SESSION['form_data']['max_splits']); ?>

<table>

<?php for ($j = 0; $j < $_SESSION['form_data']['max_splits']; $j++): ?>

<td>

<!-- payee_id -->
Payee&nbsp;
<?php $form->select('split_payee_id'); ?>

<br/>

<!-- to_acct -->
Category&nbsp;
<?php $form->select('split_to_acct'); ?>

<br/>

<!-- memo -->
Memo&nbsp;
<?php $form->text('split_memo'); ?>

<br/>

<!-- amount -->
Debit&nbsp;
<?php $form->text('split_dr_amount'); ?>
&nbsp;
Credit&nbsp;
<?php $form->text('split_cr_amount'); ?>

</td>

</tr>

<?php endfor; ?>

</table>

<p>
<?php $form->submit('s1'); ?>
&nbsp;
<?php form::abandon('sltxnadd.php'); ?>
</p>

</form>

