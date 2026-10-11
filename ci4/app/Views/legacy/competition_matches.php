<div class="inner_main container-fluid">
	<h1>
		<a href="<?=site_url('competition/listBySeries').'/'.$competitionMatches['competitionSeriesID'];?>">
			<?=$competitionMatches['competitionSeriesName'];?>
		</a>
	</h1>
	<h2>
		<a href="<?=site_url('competition/details').'/'.$competitionID;?>">
			<?=$competitionMatches['competitionName'];?>
		</a>
	</h2>
<?php if(isset($competitionMatches['rounds'])): ?>
<?php 	foreach($competitionMatches['rounds'] as $roundNumber => $round): ?>
	<div class="panel panel-default">
		<div class="panel-heading collapsed" data-toggle="collapse" data-target="#round<?=$roundNumber;?>">
			<h3><i class="chevron fa fa-fw"></i> Round <?=$roundNumber;?></h3>
		</div>
		<div id="round<?=$roundNumber;?>" class="collapse">
		<table class="table table-striped table-condensed" id="matchesRound<?=$roundNumber;?>">
<?php 		if(count($round) > 0): ?>
<?php 			foreach($round as $key => $match): ?>
<?php 				$numWinners = count($match['winners']); ?>
<?php 				$numLosers = count($match['losers']); ?>
			<tr>
<?php 				if (count($match['winners']) > 0): ?>
				<td class="table_cell">
					<a href="<?=site_url('player/details').'/'.$match['winners'][0]['ID']; ?>">
						<?=$match['winners'][0]['name']; ?>
					</a>
				</td>
				<td class="table_cell">
					<a href="<?=site_url('army/details').'/'.$match['winnerArmy']['ID']; ?>">
						(<?=$match['winnerArmy']['name']; ?>)
					</a>
				</td>
<?php 				else: ?>
				<td></td>
				<td></td>
<?php 				endif; ?>
				<td class="table_cell_center">
<?php if($match['errorMessage'] != ""): ?>
					<span style="color:red;">
<?php else: ?>
					<span>
<?php endif; ?>
							<?=$match['score']; ?>
					</span>
				</td>
<?php 				if (count($match['losers']) > 0): ?>
				<td class="table_cell">
					<a href="<?=site_url('player/details').'/'.$match['losers'][0]['ID']; ?>">
						<?=$match['losers'][0]['name']; ?>
					</a>
				</td>
				<td class="table_cell">
					<a href="<?=site_url('army/details').'/'.$match['loserArmy']['ID']; ?>">
						(<?=$match['loserArmy']['name']; ?>)
					</a>
				</td>
<?php 				else: ?>
				<td></td>
				<td></td>
<?php 				endif; ?>
<?php 				if (isset($legacySession->userdata['permissions']['Add Result'])
							&& $legacySession->userdata['permissions']['Add Result'] == 1
							 && isset($legacySession->userdata['seriesPermissions'][$competitionMatches['competitionSeriesID']])): ?>
				<td>
					<a class="btn btn-default" href="<?=site_url('competition/editMatch').'/'.$competitionID.'/'.$key;?>">
						<i class="fa fa-pencil-square-o fa-lg"></i> Edit Match
					</a>
					<a class="btn btn-danger" href="<?=site_url('competition/deleteMatch').'/'.$competitionID.'/'.$key;?>">
						<i class="fa fa-trash-o fa-lg"></i> Delete Match
					</a>
				</td>
<?php 				endif; ?>
				<!--<td class="table_cell"><?=$match['errorMessage']; ?></td>-->
			</tr>
<?php 				if ($numWinners > 1 || $numLosers > 1) :?>
<?php 					if($numWinners > $numLosers) {
								$maxRows = $numWinners;
							} else {
								$maxRows = $numLosers;
							} 
?>
<?php 					for ($a = 1; $a < $maxRows; $a++): ?>
			<tr>
				<td class="table_cell">	
<?php 						if ($a < $numWinners): ?>
					<a href="<?=site_url('player/details').'/'.$match['winners'][$a]['ID']; ?>">
						<?=$match['winners'][$a]['name']; ?>
					</a>
<?php 						endif; ?>
				</td>
				<td></td>
				<td></td>
				<td class="table_cell">	
<?php 						if ($a < $numLosers): ?>
					<a href="<?=site_url('player/details').'/'.$match['losers'][$a]['ID']; ?>">
						<?=$match['losers'][$a]['name']; ?>
					</a>
<?php 						endif; ?>
				</td>
				<td></td>
				<td></td>
			</tr>
<?php 					endfor; ?>
<?php 				endif; ?>
<?php 			endforeach; ?>
<?php 		endif; ?>
		</table>
		</div>
	</div>
<?php 	endforeach; ?>
<?php endif; ?>
				
</div>