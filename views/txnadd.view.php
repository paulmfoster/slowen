
<h2>Transaction Types:</h2>
<h3>
("Splits" are transactions with multiple payees or recipients)
</h3>
<?php form::button('Check', 'index.php?c=addtxn&m=check'); ?>
&nbsp;Simple check to a random vendor (no splits).
<br/>
<?php form::button('Deposit', 'index.php?c=addtxn&m=deposit'); ?>
&nbsp;Deposit into a checking or savings account (no splits).
<br/>
<?php form::button('Credit Card Charge', 'index.php?c=addtxn&m=ccard'); ?>
&nbsp;Buy something on a credit card (no splits).
<br/>
<?php form::button('Inter-Account Transfer', 'index.php?c=addtxn&m=xfer'); ?>
&nbsp;Transfer money between tracked accounts, including checks to credit card companies.
<br/>
<?php form::button('Other/Split Transactions', 'index.php?c=addtxn&m=other'); ?>
&nbsp;All other transactions, including splits.

