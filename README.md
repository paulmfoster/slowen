# Slowen

**Slowen** is nominally a checkbook manager. But it can also manage credit
cards, debit cards, mortgage accounts, etc.

## History

Years ago, before computers, you wrote down transactions in a little
notebook called a "check register". At the end of the month, the bank would
mail you a statement listing all the transactions which had cleared. You'd
mark them down as "cleared" in your check register, and make sure
everything balanced.

Years later, computers came along. This process was more or less
computerized. The most popular program for doing this was called "Quicken &reg;".
Quicken ran under DOS and Windows. For many years, I used Quicken. But in
1996, I switched from DOS/Windows to Linux. I managed to run Quicken for a
few years under DOS emulation on Linux. But one day it stopped working.

I used spreadsheets and other finance programs for a number of years, after
Quicken stopped working for me. But I was never satisfied. The programs
required more work than I wanted to expend, or didn't do things the way I
wanted.

Being a PHP programmer, I finally decided to do what I'd been putting off
for years, and I wrote my own program to handle personal finances--
**Slowen**.

Slowen is not a "double-entry" or "accrual" system. While it's not built to
handle a business's finances, it can be used that way (I do). And it will
generate reports needed for your tax accountant.

Slowen will not connect or interface with your online bank. But if you're
not doing your transactions entirely online, Slowen could make your life
easier. 

## Security

Slowen is programmed in PHP and runs on a web server. This could be a local
web server on your local machine, or one on your network. But it is not
designed to run on the Internet. It does not have the security built in to
fight off hackers.

In addition, Slowen does not have users or logins. Anyone can fire up
Slowen and enter transactions, reconcile accounts, etc.

The point is that Slowen is best used on a local network with people you
trust.

## Installation

Simply copy the contents of the Slowen package to a subdirectory served by
your web server, like "slowen" or "checkbook". Then, from a browser, surf
to that directory on your server, like:

```
http://localhost/slowen/index.php
```

Slowen does not do secure HTTP, so there's no need for "https://" in front
of the URL.

Slowen has been tested on Linux. It might work on MacOS or Windows; I don't
know.

There are two important things Slowen needs to get any work done. First, it
needs payees, people you write checks to or who charge amounts to your
credit cards. Slowen doesn't have any of these. You must add them yourself.

The second important ingredient is accounts. There are several types of
these.

| Account Type | Examples               |
|--------------|------------------------|
| Asset        | Union Bank Checking    |
| Expense      | Groceries              |
| Income       | Exxon (where you work) |
| Liability    | Central Bank Visa      |
| Equity       | Universal Mortgage     |

Slowen comes with all of the main "account types" above, and a few common
"expense" accounts, and a "salary" income account. You're welcome to delete
these if they don't meet your needs. But you will want to add accounts for
your particular circumstances.

Really, that's all there is to installing Slowen.

## License

This software is licensed under the GNU Public License version 2 (GPLv2).
That means you can change this software if you want to, but if you
distribute it, you must provide the source code for it. That's an
oversimplification, but if it's really important to you, read the license.

