<?php include VIEWDIR . 'head.view.php'; ?> 

<?php if ($cells === FALSE): ?> 
<h2>There are no records. You must enter data first.</h2>
<?php else: ?>

<h2>Budget for week ending <?php echo $hr_wedate; ?></h2>

<table>

<!-- titles -->
<tr>
<th>Acct Name</th>
<th>Typ Due</th>
<th>Period</th>
<th>Wkly S/A</th>
<th>Prior S/A</th>
<th>Addl S/A</th>
<th>Paid</th>
<th>New S/A</th>
</tr>
<!-- end title -->

<?php $z = 0; ?>
<?php $count = count($cells); ?>
<?php for ($j = 0; $j < $count; $j++): ?>

<tr class="row<?php echo $z++ & 1; ?>">

<td class="align-right">
<?php echo $cells[$j]['acctname']; ?>
</td>

<td class="align-right">
<?php echo int2dec($cells[$j]['typdue']); ?>
</td>

<td class="align-right">
<?php echo $cells[$j]['period']; ?>
</td>

<td class="align-right">
<?php echo int2dec($cells[$j]['wklysa']); ?>
</td>

<td class="align-right">
<?php echo int2dec($cells[$j]['priorsa']); ?>
</td>

<td class="align-right">
<?php echo int2dec($cells[$j]['addlsa']); ?>
</td>

<td class="align-right">
<?php echo int2dec($cells[$j]['paid']); ?>
</td>

<td class="align-right">
<?php if ($cells[$j]['red']): ?>
<span class="red">
<?php echo int2dec($cells[$j]['newsa']); ?>
</span>
<?php else: ?>
<?php echo int2dec($cells[$j]['newsa']); ?>
<?php endif; ?>
</td>

</tr>

<?php endfor; ?>

<!-- totals row -->

<tr>

<td>
<!-- acctname -->
TOTALS
</td>
<td>
<!-- typdue -->
</td>
<td>
<!-- period -->
</td>
<td class="align-right">
<!-- wklysa -->
<?php echo int2dec($totals['wklysa']); ?>
</td>
<td class="align-right">
<!-- priorsa -->
<?php echo int2dec($totals['priorsa']); ?>
</td>
<td class="align-right">
<!-- addlsa -->
<?php echo int2dec($totals['addlsa']); ?>
</td>
<td class="align-right">
<!-- paid -->
<?php echo int2dec($totals['paid']); ?>
</td>
<td class="align-right">
<!-- newsa -->
<?php echo int2dec($totals['newsa']); ?>
</td>

</tr>

</table>

<?php endif; ?>

<?php include VIEWDIR . 'footer.view.php'; ?> 

