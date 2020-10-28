
<h2>Transaction Types:</h2>
<h3>
("Splits" are transactions with multiple payees or recipients)
</h3>
<?php form::button('Check', 'chkadd.php'); ?>
&nbsp;Simple check to a random vendor (no splits).
<br/>
<?php form::button('Deposit', 'depadd.php'); ?>
&nbsp;Deposit into a checking or savings account (no splits).
<br/>
<?php form::button('Credit Card Charge', 'ccardadd.php'); ?>
&nbsp;Buy something on a credit card (no splits).
<br/>
<?php form::button('Inter-Account Transfer', 'xferadd.php'); ?>
&nbsp;Transfer money between tracked accounts, including checks to credit card companies.
<br/>
<?php form::button('Other/Split Transactions', 'othadd.php'); ?>
&nbsp;All other transactions, including splits.

