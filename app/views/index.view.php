<?php include VIEWDIR . 'head.view.php'; ?>

<h1>Slowen Functions</h1>

<div class="box">

<h3>Accounts Menu</h3>

"Accounts" are your checking, savings and credit card or loan accounts.
They are also your "expense" and "income" categories. For our purposes,
they are all called "accounts".

<dl>
<dt>Register</dt>

<dd>

This function allows you to show a register for any account. In the
register view, you may edit, void or call for a detailed view of any
transaction.

</dd>

<dt>Reconcile</dt>
<dd>

This allows you do reconcile any account with your statement from the
bank or credit card company.

</dd>

<dt>Add Account</dt>
<dd>

Here you may add checking, savings, credit card, loan, income or expense accounts.
</dd>

<dt>Delete Account</dt>
<dd>Here you can delete any account so long as it isn't connected with any transactions.
</dd>

<dt>Edit Account</dt>
<dd>Here you may edit the details of an account, such as its name.
</dd>

<dt>Search By Account</dt>
<dd>
Here you can search through all the transactions for a given expense category.
</dd> 
</dl>

<hr/>

<h3>Payees Menu</h3>

<dl>

<dt>Add Payee</dt>
<dd>This function allows you to add payees.
</dd>

<dt>Edit Payee</dt>
<dd>Here you may edit the details of a given payee.
</dd>

<dt>Delete Payee</dt>
<dd>This allows you to delete a payee, so long as it is not used in any transactions.
</dd>

<dt>Search By Payee</dt>
<dd>
Search through transactions by payee here.
</dd>

</dl>

<hr/>

<h3>Transactions Menu</h3>

Here you may enter the various types of transactions. Every deposit, check, charge
or inter-account transfer is a "transaction". Some transactions you enter will
create more than one actual "transaction" in the system. See "Transfers" below.

<dl>

<dt>Check</dt>
<dd>

Checks are entered only from a bank account. Other transaction similar
to checks can be entered without a check number here, but it's primarily
intended for recording thecks.
</dd>

<dt>Deposit</dt>
<dd>

This selection is for entering deposits. This is money coming <em>in</em> to a
bank account. This can be used to enter credits to other accounts. But payments
from a bank account to a credit card should be entered as transfers.
</dd>

<dt>Credit Card</dt>
<dd>

Here you enter credit card charges.
</dd>

<dt>Transfer</dt>
<dd>

This choice is for inter-account transfers. An example of this would be
where you write a check to pay a credit card bill. For inter-account
transfers, two entries are generated from the user's data entry.
One entry shows the money going out of the checking account, and the
other shows the money coming into the credit card account (in the example above).
</dd>

<dt>Other/Split</dt>
<dd>

This is for anything not covered above, though any of the above
transactions can be entered here. In particular, this covers <strong>splits</strong>.
Splits are transaction where there is one or more payee or account
involved. For example, this could be a business deposit where you have
multiple checks from multiple customers for multiple purposes. Or any other
transaction of this type. You will enter the number of "splits", and be
shown a screen for entering the details for each one.

</dd>

</dl>

<hr/>

<h3>Scheduled Manu</h3>

Scheduled transactions are those which you create, but which don't actually become
real transactions until you tell the system to "activate" them. For example, if Netflix
charges your credit card once a month on the 5th of the month, you can set up a scheduled
or "recurring" transaction which will only become a real transaction when you tell it
to "activate".

<dl>
<dt>Add Transaction</dt>
<dd>

This creates a transaction which can be activated at some future time.
</dd>

<dt>Delete Transaction</dt>
<dd>
This allows you to delete a scheduled transaction which is no longer needed.
</dd>

<dt>Activate Transaction</dt>
<dd>
This allows you to convert a "scheduled" transaction into a real one. This screen shows
you all the scheduled transactions, and allows you to check off each one you want to
turn into a real transaction for this month. Normally, you would run this option at the
beginning of every month. <strong>Caution:</strong> Nothing prevents you from running
this option repeatedly in a month, possibly giving you duplicate transactions.
Pay attention.
</dd>

</dl>
<hr/>

<h3>Search Menu</h3>

<dl>
<dt>Categories</dt>
<dd>

This choice allows you to search transactions by account or category. These
are really the same thing; the word "category" applies mostly to income or
expense transactions.
</dd>

<dt>
<dt>Payees</dt>
<dd>

This choice allows you to search transactions by payee, or recipient.
</dd>

</dl>

<hr/>

<h3>Reports</h3>

<dl>

<dt>Balances</dt>
<dd>

This will give you the balances of all the accounts as of the date you enter.
</dd>

<dt>Register</dt>
<dd>
This is another way of getting a register for an account.
</dd>

<dt>Budget</dt>
<dd>
This allows you to see what you've spent on a certain category over a specified span of time.
You specify the time period and the account/category or payee.
</dd>

<dt>Weekly Expenses</dt>
<dd>

This will give you the totals for each expense category for a given span
of dates. Generally, you will use this to determine the expenses for a
week, but any span of dates will work.
</dd>

<dt>Monthly Audit</dt>
<dd>

This would typically be used at the end of a month to determine what has
been spent, and what has been earned, broken down and checked to
determine if everything balances.
</dd>

<dt>Yearly Audit</dt>
<dd>

This is a report which shows everything needed to fill out your tax
returns, as above, but for a whole year.
</dd>

</dl>

<hr/>

<h3>Help Menu</h3>

<dl>
<dt>Introduction</dt>
<dd>This screen.</dd>

<dt>History</dt>
<dd>

This shows the history and some technical details for this software.
</dd>

<dt>Bug Report</dt>
<dd>
Bug? Are you serious? Okay, fine, report it here. If you <em>have</em> to.
</dd>

<dt>Feature Request</dt>
<dd>
You want even <em>more</em> features? Ask for them here.
</dd>

</dl>

</div>

<?php include VIEWDIR . 'footer.view.php'; ?>

