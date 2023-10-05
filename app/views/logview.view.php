<?php include 'head.view.php'; ?>

<?php extract($data); ?>

<?php if ($recs === FALSE): ?>
<h3>No log records to display.</h3>
<?php else: ?>

<?php $row = 0; ?>

<table class="border-rules">

<tr>
<th>Timestamp</th>
<th>Query Type</th>
<th>Table</th>
<th>Fields</th>
<th>Where</th>
</tr>

<?php foreach ($recs as $rec): ?>

<tr class="row<?php echo $row++ & 1; ?>">
<td><?php echo $rec['timestamp']; ?></td>
<td><?php echo $rec['ltype']; ?></td>
<td><?php echo $rec['ltable']; ?></td>
<td><?php echo $rec['lfields']; ?></td>
<td><?php echo $rec['lwhere']; ?></td>
</tr>

<?php endforeach; ?>

</table>

<p>
<strong>Purging the log removes all log entries older than 30 days. </strong>
<?php form::button('Purge Log', 'index.php?c=dblog&m=purge'); ?>
</p>

<?php endif; ?>

<?php include 'footer.view.php'; ?>

