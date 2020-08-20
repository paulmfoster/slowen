
<form action="bugs.php" method="post">

<?php $form->hidden('app_title'); ?>

Indicate what you'd like to communicate below.<br/>
Be as clear as possible; your programmer does not read minds.
<br/>
<table>
<tr>
<td><label>Name</label></td>
<td><?php $form->text('name'); ?></td>
</tr>

<tr>
<td><label>Email</label></td>
<td><?php $form->text('email'); ?><td>
</tr>

<tr>
<td><label>Remarks</label></td>
<td><?php $form->textarea('remark'); ?></td>
</tr>

<tr>
<td></td>
<td><?php $form->submit('s1'); ?></td>
</tr>
</table>

</form>

