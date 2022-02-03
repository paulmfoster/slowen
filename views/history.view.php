
<h2>Welcome to Slowen</h2>

<p>
If you're relatively young, you probably track your checking and credit
card account balances online. You use the bank software. You may keep a
rough balance in your head for each of your accounts, and you check
bank statements for any weird transactions. You don't use any
off-the-shelf software to manage your accounts and their balances.
</p>
<p>
If the above describes you, you probably have no business running this
application. You'll likely find it tedious and annoying. It doesn't 
work the way you do. Fair enough. Pass Slowen by. It's not for you,
unless you have an excess of morbid curiosity.
</p>
<p>
For the rest of you, this is <em>Slowen</em>.
It is a web application designed to help
you manage your finances. It more or less deals in transactions and what
could be called "check registers", one for each account. It allows you
to input new transactions (checks written, deposits made, items
charged), check the resulting balances, reconcile accounts when
statements come in, etc.
</p>
<p>
For those of you born a little while ago, this concept may be foreign.
For the rest of us, we remember check registers, checks and the like.
</p>
<h2>Why I Wrote This</h2>
<p>
Originally, I used <em>Quicken for DOS</em> to do this, from version 3
to about version 8. Somewhere in that time, I switched from Windows to
Linux for my main operating system. While under Linux, I had to run a
DOS simulation program in order to run <em>Quicken</em> under Linux. At
some point, the emulator failed to allow me to run <em>Quicken</em> any
more, so I was forced to find something else to use under Linux.
</p>
<p>
After a long survey, I chose <em>KMyMoney</em>, an "open source"
program. It was fine (with some annoyances), and used XML files to
store transactions. This was a bad design decision in my opinion, and
lead to <em>KMyMoney</em> being slow to do certain things, etc.
Eventually a new "back end" using <em>SQLite</em> (a true database)
was added as an option. This version of <em>KMyMoney</em> was considered
"experimental", but the programmers continued working on it to improve
how it worked. However, at one point after an upgrade, I could no longer
get the program to work with my <em>SQLite</em> files. Being unwilling
to tolerate this sort of interruption any longer, I started running my
finances in a spreadsheet. This isn't a great solution, but worked as a stop-gap.
</p>
<p>
For years I had threatened to write my own "checkbook manager"
program. And at this point, I finally sat down to do so. It needed to be
"multi-platform", meaning it had to run under Windows, MacOS and Linux.
The simplest choice for doing this was to make it a web application,
particularly since I program in <em>PHP</em>, which is primarily a web
language. It also needed to be multi-user which a web application using
an SQL database would be. I chose <em>SQLite</em> for the database
component primarily for one reason. <em>SQLite</em> is not a great choice
for a multi-user program. However, when it comes to having to move, back
up and/or relocate the code, no SQL database is simpler than <em>SQLite</em>.
</p>
<h2>Requirements</h2>
<p>
There aren't many requirements to run this application. You need a web
server which understands PHP, and a version of PHP which understands SQLite.
That's about it. You must create a directory in the web root to hold the files,
and then simply copy them to that directory. Go to the index.php file in that
directory, and you're there.
</p>
<h2>Double-Entry Bookkeeping</h2>
<p>
"Double-Entry" bookkeeping is a method of bookkeeping devised by accountants,
in my opinion, to keep themselves employed. (Yes, I'm aware the system could
be as much as 1000 or so years old.) It's complicated, and despite assurances
to the contrary, it can be gamed and cheated. As a result of my opinion, I do
not use it with Slowen. This is a single-entry, cash-based system.
</p>
<h2>Signs</h2>
<p>
Double entry accounting tends to be counter-intuitive regarding the "signs" of various
transactions, regardless of what accountants will tell you. According to the
original Latin definition of "debit", it is an amount owed. "Credit" is
the reverse-- an amount you have received. By these definitions, a debit
would be a negative and a credit would be positive. All this is in relation to
your cash or your net worth. This is not the way accountants see it, but
it is the way Slowen treats it. If you get paid, your checking account shows
a positive or credit transaction. If you pay a bill, your checking account
shows a negative, since you paid money out. If you pay your credit card bill
your checking account will show a reduction in cash or a debit. Your credit
card account will show a credit, because you reduced the amount you owed.
</p>
<p>
For example, your checking and savings account should
always run with a positive balance unless you overdraw them. Conversely, your
credit card should always run at a negative balance, because the figures on
your statements are amounts you <em>owe</em>. When entering transactions into
the system, and looking at balances, remember this. Where feasible, I give you
fields labeled "debit" and "credit" to represent negative and positive amounts,
respectively. For consistency's sake, in Slowen, a debit is always a negative,
or decrease in your cash or increase in how much you owe. A credit is always a 
positive, or an increase in cash or a decrease in how much you owe.
</p>
<p>
"Signs" become very important in reconciling accounts. Your
credit card statements will show you a positive balance, but the balance I show
in Slowen will be the same amount, but negative, because your credit card
balance is how much money you <em>owe</em>, not how much cash you have. If it
helps, think of it this way: you can spend the amounts in your checking and
savings accounts. You can't "spend" the amount charged on your credit cards
(the amounts that, added together, make up your credit card balance).
</p>
<p>
Thus, when running Slowen, always keep the idea of "signs" in mind.
</p>
<p>
<h2>Security</h2>
<p>
<strong>DO NOT RUN THIS PROGRAM ON THE INTERNET!</strong> I assume that you
will be running this on your desktop, and you will probably be the only person
running it. As a result, I have not built any security into the application. A
reasonably talented programmer could add security, but I haven't. Anyone using
this application who is relatively computer savvy could hack it, particularly
copying your transaction database and/or altering it without your consent.
Therefore, I'd suggest only allowing access to this application by people
you seriously trust, preferably those who aren't particularly computer-savvy.
</p>
<p>
Generally, applications running on a LAN behind a router/firewall are immune
to hackers. However, should you attempt to run this application on a web
server to which the general Internet has access, you might as well hang it up.
You're asking for someone to hack your site, and they will.
</p>
<h2>Versions, Features and Bugs</h2>
<p>
Obviously, I'm not a perfect programmer. So there are probably bugs. If you can
solve whatever the problem is with the software, do so and let me know how you
did it, preferably with a copy of the code changes involved. If the code works
I may add it to the program and give you credit for the fix. If you would like
new features, you can write to me. I can't promise I will include the feature
you want. But if I like the idea and have the time, I may add it. If not, you're
still welcome to scrounge up a PHP programmer somewhere who will code it for
you. I cannot know what version of <em>Slowen</em> you will use, so I cannot
know what feature set is included. For early versions, I have left off many
features, intending to come back later and add them. So if you urge me to add
a feature, I may already have it on my "to do" list. When I will get to it is
another matter.
</p>
<p>
<h2>Do You Use This Program?</h2>
Absolutely! I use it daily, and have since I got enough of it together to be
be able to run my finances without using spreadsheets any more. It satisfies all
my needs for an accounting program, including summarizing my transactions and
subtotals for yearly taxes. None of this means it will work flawlessly for you.
But it does mean that I have enough confidence in the code to actually use it
for my personal and business finances. In the coding business, we call this
'eating your own dog food'.
</p>
<h2>Licenses</h2>
<p>
This code is provided <strong>as is</strong> to you for your use. I'm not liable
for your use of the program or for anything that happens as a result of your
using it. On the other hand, I'm also not charging you for its use. Moreover
you're welcome to sell it, alter it, and take credit for any modifications you
make to the code. If you do modify the code, I'd prefer you share the modifications
with me, but that's your business. I can't force you to do so. If you do modify
the code, I'd prefer you still call it "Slowen" and give me credit as the primary
author, since it took me an awful long time to write the original code.
</p>
<p>
<strong>Paul M. Foster, programmer at large</strong>
</p>

