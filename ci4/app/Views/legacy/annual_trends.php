<div class="container-fluid">
<table class="table table-striped table-condensed">
<thead>
	<tr class="">
		<th class="">Year</th>
		<th class="">Players</th>
		<th class="">Matches</th>
		<th class="">New Players</th>
	</tr>
<thead>
<tbody>
<?php  foreach($trends AS $key => $year): ?>
	<tr>
		<td class="">
			<?=$key;?>
		</td>
		<td class="">
			<?=(isset($year['players'])) ? $year['players'] : 0;?>
		</td>
		<td class="">
			<?=(isset($year['matches'])) ? $year['matches'] : 0;?>
		</td>
		<td class="">
			<?=(isset($year['newbies'])) ? $year['newbies'] : 0;?>
		</td>
	</tr>
<?php  endforeach; ?>
</tbody>
</table>
</div>