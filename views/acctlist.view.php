Click on the desired account to view its register
<br/>
<br/>
<?php foreach ($accounts as $acct): ?>
<?php form::button($acct['name'], 'index.php?c=transaction&m=register&acct_id=' . $acct['acct_id']); ?>
<br/>
<?php endforeach; ?>

