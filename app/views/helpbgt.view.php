<?php include VIEWDIR . 'head.view.php'; ?>
<div class="textbox">
<h2>Why This Application?</h2>
<p>
Budgeting (as I use it) is a matter of, for every expense, having an
amount set aside for that thing (like 3 weeks' worth of rent money),
adding a week's worth of set aside money for it, and then subtracting
whatever you paid that week. This gives you the amount you currently
owe.
</p>
<table>
<tr>
<td>
Example: Rent, date 08/08/20
</td>
<td></td>
</tr>
<tr>
<td>Set aside as of last week</td>
<td style="text-align: right">$600.00</td>
</tr>
<tr>
<td>Added set aside this week</td>
<td style="text-align: right">+ $200.00</td>
</tr>
<tr>
<td>Paid this week</td>
<td style="text-align: right">- $800.00</td>
</tr>
<tr>
<td>Total now set aside</td>
<td style="text-align: right">0.00</td>
</tr>
</table>
<p>
But there is a problem with doing week-to-week budgets like this. The
current set aside amount must become the prior set aside when you do
next week's budget. So if you have columns like this:
</p>
<table>
<tr>
<th>Prior S/A</th><th>Add'l S/A</th><th>Paid</th><th>New S/A</th>
</tr>
</table>
<p>
The "New S/A" column must be copied to the "Prior S/A" column when
you start your new budget. Spreadsheets can't really do this easily.
Hence this program.
</p>
<h2>Basic Operation</h2>
<p>
Think of the budget as a spreadsheet, with various columns for
important values, and a number of rows, one for each budget account.
</p>
<p>
There are some important columns in a budget.
<dl>
<dt>Account Name</dt>
<dd>The name of this account. Like "Rent".</dd>
<dt>Typical Due</dt>
<dd>This is the amount you expect to be due for this item. If your rent
is $800 per month, then this will be $800. This is the amount which will
be set aside each month (4 weeks).</dd>
<dt>Period</dt>
<dd>This is the "period" for your "Typical Due". You have the following
choices: Year, Semi-Annual (6 months), Quarter, Month and Week. So for
your rent, your Period is "Month". Another example: let's say you
spend $6000 per year on groceries. You could set up a "Groceries"
account with a Typical Due of $6000 and a period of Year.</dd>
<dt>Weekly Setaside</dt>
<dd>This is the amount you would have set aside each week on an
account. If we're talking about our Grocery account, that would be
$6000/52 (52 weeks) = $115.39.</dd>
<dt>Prior Setaside</dt>
<dd>This is the amount which was set aside as of last week. Let's say we're
in the third week of the month. Your rent is $800. Going into the third
week, you should have two weeks' worth of setasides for rent, or $400.</dd>
<dt>Additional Setaside</dt>
<dd>This is how much is getting set aside this week. For your rent,
that's $200.</dd>
<dt>Paid</dt>
<dd>If any money was paid on a budget account, it shows up here. When you
pay that $800 rent bill, it shows up in the column.</dd>
<dt>New Setasides</dt>
<dd>This is the result of all this week's activity on this budget account.
The "formula" for this column is:<br/>
<strong>Prior Setaside + Additional Setaside - Paid = New Setaside</strong>
</dd>
</p>

<p>
Each week, you go to the menu under "Budget" and select "New Week".
This causes the system to advance the date to the next week ending.
It swaps the "Prior Setaside" and "New Setaside" columns, as mentioned
before. Then it zeroes out the "Paid" column. Then it replaces the
contents of the "Additional Setasides" column with the contents of the
"Weekly Setaside" column. Lastly, it fetches all the week's transactions
from Slowen, all of them. It uses these to fill in any payments in the
"Paid" column, and any expenses are added to the "Additional Setasides"
column.
</p>

<p>
You can edit all the columns from Weekly Setaside through Paid while you're
editing the budget.'
</p>

<h2>Setting Up The Accounts</h2>
<p>
Adding and editing a budget account are similar operations. There are several
fields which are important.
<dl>
<dt>Account Name</dt>
<dd>An easily identifiable name for the account.</dd>
<dt>Typical Due</dt>
<dd>How much is due each week, month, quarter, etc. for this account? If you pay $800
per month for rent, that's your typical due.</dd>
<dt>Period</dt>
<dd>There are several periods available: Day, Week, Month, Quarter, Semi-Annual and Year.
If we're talking about your rent (as above), the period would be "Month". Let's take a more
unusual example. Let's say that, over a year, you spend $6000 on groceries. You have a "Grocery"
budget account. In that case, your typical due would be $6000, and your period would be "Year".
So every week, the budget module would set aside $6000/52 (52 weeks).</dd>
<dt>From Account</dt>
<dd>Starting from here, the next three fields determine how this account is updated when
you start the budget each week. The user can pick one to use for this update. The first field
is a "From" account. This is mainly Slowen accounts from which bills are paid, like credit cards,
checking accounts and similar. If the user elects to use this field for updating this budget
account, then any time a transaction occurs with that From account, it will show up on this
budget item, either in "Additional Setaside" or "Paid" columns. As an example, let's assume you
have a Visa card budget account. When you set up the account, you select the From account for
that Visa card. Now, every time a transaction on that credit card happens, it will show up when
you start your budget each week. So if a charge is made on the card, it will show up in the
Additional Setaside column. If the card is paid off (in part or in full) by a check, that will
show up in the Paid column.</dd>
<dt>Payee ID</dt>
<dd>This is another column that monitors how your budget is updated. Select this option, and every
transaction for that payee will show in the budget for the week.</dd>
<dt>To Account</dt>
<dd>This is like categories. Like, rent. Every time you pay your rent, it shows on your
"Rent" budget account.</dd>
<dt>Currently Owed</dt>
<dd>When adding a new budget account, it's handy to be able to enter how much is owed before you
start actually using the budget account.</dd>
</dl>
</p>
<p>
<strong>Note:</strong> When you start the budget for the week, two operations take place. One is
that the Additional Setaside field is set to the contents of the Weekly Setaside field. The second
is that the budget updates from Slowen's transactions.
</p>

<h2>Editing the Budget</h2>
<p>
The documentation for editing the budget is on the budget editing
screen. But I'll summarize it here.
</p>
<p>
On the main edit screen, there's a table with the following columns:
</p>
<ul>
<li>Wkly S/A: (Weekly Setaside) This is the amount which will be copied
to the Additional Setaside column.</li>
<li>Prior S/A: (Prior Setaside) This is the amount setaside last week
for that account</li>
<li>Add'l S/A: (Additional Setaside) This is the amount you're adding
to the setasides this week. Normally, this will be the same as the weekly
setasides, but you can change this column to whatever you want.
</li>
<li>Paid: How much did you pay on this bill this week?</li>
<li>New S/A: (New Setaside) This is the result of taking your prior setasides,
adding your additional setaside and subtracting how much you paid this
week. This column is calculated; you can't directly edit it.
</li>
</ul>
<p>
There are four buttons above and below the edit table. They are:
</p>
<ul>
<li>Restart: if you make a mistake and just want to start over, this will do it. It
will set all your figures back to what they were when you started the edit.
</li>
<li>Recalculate: in the process of editing the different fields in the budget,
you will want to see the impact of those changes on the overall budget. This
application is not like a spreadsheet, where it recalculates every time you
leave a field. You have to manually recalculate things. Make sure you do this
before you save.
</li>
<li>Save: this does just what it says. If you need to postpone finishing the budget
or you're about to complete your work, this is the button you press. This saves
your work in a temporary place. Notice, I said a <em>temporary</em> place. This
is not the final save.</li>
<li>Complete: press this when you're done. Your work will be saved permanently,
and you will be returned to the home screen. <strong>Note</strong>: This option
does <em>not</em> save what's on screen. It finalizes the budget based on your
last save.
</li>
</ul>
<h2>Other Operations</h2>
<p>
You may delete accounts or add accounts, or edit accounts. For example, you may want
to change the name of an account. Accounts show up in alphabetical order.
</p>
<h2>Design</h2>
<p>
This part is for programmers and those interested in how this application
works internally.
</p>
<p>
This module uses four tables:
</p>
<ul>
<li>history-- all budgets going back forever</li>
<li>cells-- the current completed budget</li>
<li>staging-- the budget you're working on</li>
<li>blines-- the definition/basic parameters of a budget account</li>
</ul>
<p>
When you start a new budget, the system checks to see if you already
have a budget in progress (the staging table). If so, it pulls the
figures from there. If there's nothing in the staging table, it pulls
last week's figures from the cells table. Data entry and
recalculations all occur in memory. When you hit "Save", the budget
is saved to the staging table. That way, if you have to go off and do
something else, it is saved. The next time you resume your budget
work, it will pull the figures from the staging table. When you're
done (you select "Complete"), four things happen:
<ol>
<li>The figures are pulled from the staging table.</li>
<li>The staging table is blanked.</li>
<li>The staging figures are copied to the cells table.</li>
<li>The staging figures are copied to the history table.</li>
</ol>
</p>
<p>
When you delete a budget account, that row is deleted from the blines and
the cells table. It is not deleted from the history table.</p>
<p>
Also, the history table does not have the same structure as the cells and
staging tables. The fields which determine how the budget accounts operate
aren't there. The history fields allow you to look at what happened in a given
week, but there is insufficient information to use history for anything other
that viewing. Unfortunately, there is no current way to view the history
table in Slowen. I just haven't felt the need and written the code for it.
<p>
At any moment, history and cells contain the current completed
budget. History also contains all prior budgets. Staging will contain
your in-progress figures, if you're working on a budget.
</p>
<p>
A note here about editing, adding and deleting accounts. This operation affects
only the data in the "cells" table. It will propagate to the history and
staging tables when you edit and complete a new budget.
</p>
</div>
<?php include VIEWDIR . 'footer.view.php'; ?>
