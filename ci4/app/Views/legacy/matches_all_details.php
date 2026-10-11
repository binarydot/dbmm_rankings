<div class="container-fluid">
<?php  if (isset($matches)): ?>
<table class="table table-striped table-condensed">
<thead>
	<tr class="">
		<th class="">System</th>
		<th class="">Date</th>
		<th class="">Competition</th>
		<th class="">Round</th>
		<th class="">Player</th>
		<th class="">Army Name</th>
		<th class="">Score</th>
	</tr>
<thead>
<tbody>
<?php  foreach($matches AS $match): ?>
	<tr>
		<td class="">
			<?=$match->system_name;?> v<?=$match->version;?>
		</td>
		<td class="">
			<?=$match->competition_date;?>
		</td>
		<td class="">
			<a href="<?=site_url('competition/matches').'/'.$match->competition_id;?>">
				<?=$match->competition_name;?>
			</a>
		</td>
		<td class="">
			<?=$match->round;?>
		</td>
		<td class="">
			<a href="<?=site_url('player/details').'/'.$match->competitor_id;?>">
				<?=$match->first_name;?> <?=$match->last_name;?>
			</a>
		</td>
		<td class="">
			<a href="<?=site_url('army/listMatches').'/'.$match->army_id;?>">
				<?=$match->army_name;?> <?=$match->army_system; ?> v<?=$match->army_version;?>
			</a>
		</td>
	<?php  if ($match->side == "W"): ?>
		<td class="">
			<?=number_format($match->winner_score);?> - <?=number_format($match->loser_score);?>
		</td>
	<?php  else: ?>
		<td class="">
			<?=number_format($match->loser_score);?> - <?=number_format($match->winner_score);?>
		</td>
	<?php  endif; ?>
	</tr>
<?php  endforeach; ?>
</tbody>
</table>
<?php  else: ?>
<p>No matches found</p>
<?php  endif; ?>
</div>