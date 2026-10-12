<div class="row container">
	<h1 class="">
		<a href="<?=site_url('competition/listBySeries').'/'.$competitionResults['seriesID'];?>">
			<?=$competitionResults['seriesName'];?>
		</a>
	</h1>
	<h2 class="">
		<a href="<?=site_url('competition/list').'/'.$competitionResults['competitionID'];?>">
			<?=$competitionResults['competitionName'];?>
		</a>
	</h2>
	<h3>Results</h3>
	<?php if($competitionResults['resultsAvailable']): ?>
		<table class="table table-striped table-condensed" id="resultsTable">
			<tr class="">
				<th class="" colspan="1">Competitor</th>
				<th></th>
				<th class="">Army</th>
	<?php if ($competitionResults['fullResults']): ?>
				<th class="">Position</th>
				<th class="">Score</th>
				<th class="">Ranking</th>
	<?php endif; ?>
				<th class="">Rating</th>
	<?php if (isset($legacySession->userdata['permissions']['Update Result'])
			&& $legacySession->userdata['permissions']['Update Result'] == 1 
			&& isset($legacySession->userdata['seriesPermissions'][$competitionResults['seriesID']])): ?>
	<?php 	if ($competitionResults['fullResults']): ?>
				<th class="" colspan="2">Actions</th>
	<?php	else: ?>
				<th class="" colspan="3">Actions</th>
	<?php 	endif; ?>
	<?php endif; ?>
	<?php if ($competitionResults['fullResults']): ?>
				<th class=""></th>
	<?php endif; ?>
			</tr>
	<?php foreach($competitionResults['results'] AS $row ): ?>
			<tr>
				<td class="">
					<a href="<?=site_url('player/details').'/'.$row['competitorID'];?>">
						<?=$row['competitorName'];?>
					</a>
				</td>
				<td><?=getImg($row['rarName'])?></td>
				<td class="">
					<a href="<?=site_url('army/listMatches').'/'.$row['armyID'];?>">
						<?=$row['armyName'];?>
					</a>
				</td>
	<?php 	if ($competitionResults['fullResults']): ?>
				<td class=""><?=$row['position'];?></td>
	<?php 		if ($row['bonus'] == 0): ?>
				<td class=""><?=$row['score'];?></td>
	<?php 		elseif ($row['bonus'] > 0): ?>
				<td class=""><?=$row['score'];?>(+<?=$row['bonus'];?>)</td>
	<?php 		else: ?>
				<td class=""><?=$row['score'];?>(<?=$row['bonus'];?>)</td>
	<?php 		endif; ?>
				<td class=""><?=number_format($row['rankingPoints'],2);?></td>
	<?php 	endif; ?>
				<td class=""><?=number_format($row['glickoRating'],0);?></td>
	<?php 	if (isset($legacySession->userdata['permissions']['Update Result'])
			&& $legacySession->userdata['permissions']['Update Result'] == 1 
			&& isset($legacySession->userdata['seriesPermissions'][$competitionResults['seriesID']])): ?>
	<?php 		if (!$competitionResults['fullResults']): ?>
				<td><span>
					<a class="btn btn-default" role="button" href="<?=site_url('competition/editRunner').'/'.$competitionId.'/'.$row['positionID'];?>">
						<i class="fa fa-pencil-square-o fa-lg"></i> Update Runner Data
					</a></span>
				</td>
	<?php 		endif; ?>
				<td><span>
					<a class="btn btn-default" role="button" href="<?=site_url('competition/editResult').'/'.$competitionId.'/'.$row['positionID'];?>">
						<i class="fa fa-pencil-square-o fa-lg"></i> Update
					</a></span>
				</td>
				<td><span>
					<a class="btn btn-danger" role="button" href="<?=site_url('competition/deleteResult').'/'.$competitionId.'/'.$row['positionID'];?>">
						<i class="fa fa-trash-o fa-lg"></i> Delete
					</a></span>
				</td>
				<td class="error"><?=$row['errorMessage'];?></td>
	<?php 	endif; ?>
			</tr>
	<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p>No results for this competition yet</p>
	<?php endif; ?>
	<br />
	<h3>Umpires</h3>
	<?php if (isset($competitionResults['umpireList'])): ?>
		<table class="table table-striped table-condensed">
	<?php foreach ($competitionResults['umpireList'] as $umpire): ?>
			<tr>
				<td class="">
					<a href="<?=site_url('player/details').'/'.$umpire['competitorID'];?>">
						<?=$umpire["name"];?>
					</a>
				</td>
	<?php 	if (isset($legacySession->userdata['permissions']['Update Result'])
			&& $legacySession->userdata['permissions']['Update Result'] == 1 
			&& isset($legacySession->userdata['seriesPermissions'][$competitionResults['seriesID']])): ?>
				<td>
					<span class="">
						<a class="btn btn-default btn-danger" role="button" href="<?=site_url('competition/deleteUmpire').'/'.$umpire['umpireID'];?>">
							Delete
						</a>
					</span>
				</td>
	<?php 	endif; ?>
			</tr>
	<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p>No umpires</p>
	<?php endif; ?>
	<br />
	<h3>Stand-Ins</h3>
	<?php if (isset($competitionResults['standinList'])): ?>
		<table class="table table-striped table-condensed">
	<?php foreach ($competitionResults['standinList'] as $standin): ?>
			<tr>
				<td class="">
					<a href="<?=site_url('player/details').'/'.$standin['competitorID'];?>">
						<?=$standin["name"];?>
					</a>
					for
					<a href="<?=site_url('player/details').'/'.$standin['stoodInForID'];?>">
						<?=$standin["stoodInFor"];?>
					</a>
				</td>
	<?php 	if (isset($legacySession->userdata['permissions']['Update Result'])
			&& $legacySession->userdata['permissions']['Update Result'] == 1 
			&& isset($legacySession->userdata['seriesPermissions'][$competitionResults['seriesID']])): ?>
				<td>
					<span class="">
						<a class="btn btn-default btn-danger" role="button" href="<?=site_url('competition/deleteStandin').'/'.$standin['standinID'];?>">
							Delete
						</a>
					</span>
				</td>
	<?php 	endif; ?>
			</tr>
	<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p>No stand-ins</p>
	<?php endif; ?>
	<p></p>
	<p>Rating is the initial Glicko rating of each player at the start of the competition.</p>
	<p>Total rating of all players in this competition was <?=$competitionResults['compRating'];?></p>
	<p></p>
</div>