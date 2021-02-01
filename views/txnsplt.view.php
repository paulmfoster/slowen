
<form action="index.php?c=addtxn&m=verify" method="post">

<?php $form->hidden('from_split'); ?>
<?php $form->hidden('max_splits'); ?>

<table>

<?php for ($j = 0; $j < $txn['max_splits']; $j++): ?>

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
<?php form::abandon('index.php?c=addtxn&m=add'); ?>
</p>

</form>

