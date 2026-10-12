<div class="container">
	<h2><?=$ratings['playerName']; ?></h2>
	<table class="table table-striped">
		<tr class="">
			<th class="" colspan="3"></td>
			<th class="center" colspan="2">Post Match</td>
			<th class="center" colspan="1"></td>
			<th class="">Opponent(s)</td>
			<th class="center" colspan="2">Rating</td>
			<th class="center" colspan="2">Reliability</td>
		</tr>
		<tr class="">
			<th class="">Date</td>
			<th class="">Competition</td>
			<th class="">Round</td>
			<th class="center" colspan="1">Rating</td>
			<th class="center" colspan="1">Reliability</td>
			<th class="center" colspan="1">Score</td>
			<th class="">Name</td>
			<th class="center" colspan="1">New</td>
			<th class="center" colspan="1">Previous</td>
			<th class="center" colspan="1">New</td>
			<th class="center" colspan="1">Previous</td>
		</tr>
<?php foreach ($ratings['record'] as $record): ?>
		<tr>
		<td class="table_cell" rowspan="<?=$record['numOpponents'];?>">
			<?=$record['date'];?>
		</td>
		<td class="table_cell" rowspan="<?=$record['numOpponents'];?>">
			<a href="<?=site_url('competition/matches')."/".$record['competitionID'];?>">
				<?=$record['competitionName'];?>
			</a></td>
		<td class="table_cell" rowspan="<?=$record['numOpponents'];?>">
			<?=$record['round'];?>
		</td>
		<td class="table_cell_right" rowspan="<?=$record['numOpponents'];?>">
			<?=$record['rating'];?>
		</td>
		<td class="table_cell_right" rowspan="<?=$record['numOpponents'];?>">
			<?=number_format($record['reliability'],2);?>
		</td>
	<?php  if($record['myScore'] > 15): ?>
		<td class="table_cell_center green" rowspan="<?=$record['numOpponents'];?>">
	<?php  elseif($record['myScore'] > 9): ?>
		<td class="table_cell_center" style="color:goldenrod;" rowspan="<?=$record['numOpponents'];?>">
	<?php  else: ?>
		<td class="table_cell_center red" rowspan="<?=$record['numOpponents'];?>">
	<?php  endif; ?>
			<?=$record['myScore']." - ".$record['yourScore'];?>
		</td>
		<td class="table_cell">
			<a href="<?=site_url('player/glicko')."/".$record['opponents'][0]['ID'];?>">
				<?=$record['opponents'][0]['playerName'];?>
			</a>
		</td>
		<td class="table_cell"><?=$record['opponents'][0]['rating'];?></td>
		<td class="table_cell"><?=$record['opponents'][0]['previousRating'];?></td>
		<td class="table_cell"><?=$record['opponents'][0]['reliability'];?></td>
		<td class="table_cell"><?=$record['opponents'][0]['previousReliability'];?></td>
	</tr>
	<?php if ($record['numOpponents'] > 1): ?>
		<?php for ($a = 1; $a < count($record['opponents']); $a++): ?>
	<tr>
		<td class="table_cell">
			<a href="<?=site_url('player/glicko')."/".$record['opponents'][$a]['ID'];?>">
				<?=$record['opponents'][$a]['playerName'];?>
			</a>
		</td>
		<td class="table_cell"><?=$record['opponents'][$a]['rating'];?></td>
		<td class="table_cell"><?=$record['opponents'][$a]['previousRating'];?></td>
		<td class="table_cell"><?=$record['opponents'][$a]['reliability'];?></td>
		<td class="table_cell"><?=$record['opponents'][$a]['previousReliability'];?></td>
	</tr>
		<?php endfor; ?>
	<?php endif; ?>
<?php endforeach; ?>
	<tr>
	<td class="table_cell">Starting</td>
	<td class="table_cell"></td>
	<td class="table_cell"></td>
	<td class="table_cell_right">2000</td>
	<td class="table_cell_right">300.00</td>
	</tr>
</table>
</div>