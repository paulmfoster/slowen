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

<dt>List Accounts</dt>
<dd>This allows you to show, edit or delete individual accounts.</dd>

<dt>Search By Account</dt>
<dd>
Here you can search through all the transactions for a given expense category.
</dd> 
</dl>

<hr/>

<h3>Budget Menu</h3>

<dl>

<dt>Show Budget</dt>
<dd>This shows the entire budget laid out.</dd>

<dt>Add Account</dt>
<dd>Add an account to the budget.</dd>

<dt>Edit Account</dt>
<dd>Change the basic parameters of a budget account.</dd>

<dt>Delete Account</dt>
<dd>Delete a budget account and its metadata.</dd>

<dt>New Week</dt>
<dd>Budgets are designed to be done once a week, at the end of the week. This choice adds a week to the date,
and adds any payments and additional setasides, etc.</dd>

<dt>Print Budget</dt>
<dd>Print out the current budget for your records at any time.</dd>

<dt>Help/Design</dt>
<dd>This explains how the budget module works and how to use it.</dd>

<hr/>


<h3>Payees Menu</h3>

<dl>

<dt>Add Payee</dt>
<dd>This function allows you to add payees.
</dd>

<dt>List Accounts</dt>
<dd>
This allows you to delete or edit any payee.
</dd>

<dt>Search By Payee</dt>
<dd>
Search through transactions by payee here.
</dd>

</dl>

<hr/>

<h3>Transactions Menu</h3>

<dl>

<dt>Enter Transaction</dt>
<dd>
Here you enter new transactions. Whether it's a deposit, check, credit card charge,
inter-account transfer, you enter it here. This includes "splits", where there are
multiple payees, or depositors.
</dd>

</dl>

<hr/>

<h3>Scheduled Menu</h3>

Scheduled transactions are those which you create, but which don't actually become
real transactions until you tell the system to "activate" them. For example, if Netflix
charges your credit card once a month on the 5th of the month, you can set up a scheduled
or "recurring" transaction which will only become a real transaction when you tell it
to "activate".

<dl>
<dt>Add Transaction</dt>
<dd>

This creates a transaction which can be activated on some future date. You specify what 
all the details of the transaction, how often it recurs (e.g. monthly), and what the last
date for that transaction was. When "activated", it will then recur on that date in the
next month, year, etc.
</dd>

<dt>Delete Transaction</dt>
<dd>
This allows you to delete a scheduled transaction which is no longer needed.
</dd>

<dt>List Transactions</dt>
<dd>
This shows all the transactions which can be scheduled to happen later.
</dd>

<dt>Activate Transaction</dt>
<dd>
This allows you to convert a "scheduled" transaction into a real one. This screen shows
you all the scheduled transactions, and allows you to check off each one you want to
turn into a real transaction for this month. You would normally run this once at the beginning
of the month. Once a transaction has been "scheduled" and "activated" it puts a copy of that
transaction into the appropriate account on the appropriate date. If you accidentally run
"activate" more than once in a month, it should have no effect. Once a transaction has been
created for a given month, the system won't add the same transaction again for that month.
</dd>

</dl>
<hr/>

<h3>Search Menu</h3>

<dl>
<dt>Accounts/Categories</dt>
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

</dl>

</div>

<?php include VIEWDIR . 'footer.view.php'; ?>

