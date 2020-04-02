Click on the desired account to view its register
<br/>
<br/>
<?php foreach ($accounts as $acct): ?>
<?php form::button($acct['name'], 'register.php?acct_id=' . $acct['acct_id']); ?>
<br/>
<?php endforeach; ?>

