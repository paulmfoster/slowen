<form method="post" action="balances2.php">
Select a date; balances shown will be as of the end of that date<br/>
Date&nbsp;
<?php $form->date('last_dt', $today); ?>
<?php $form->submit('s1'); ?>
</form>

