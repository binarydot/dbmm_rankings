<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="collapse navbar-collapse">
<ul class="nav navbar-nav">
<?php if ($subMenuItem == "List"): ?>
	<li class="active">
	<a href="<?=site_url('competition') ?>">List</a>
	</li>
<?php else: ?>
	<li class="">
	<a href="<?=site_url('competition') ?>">List</a>
	</li>
<?php endif; ?>
<?php if ($competitionId > 0): ?>
<?php 	if ($subMenuItem == "Details"): ?>
	<li class="active">
	<a href="<?=site_url('competition/details')."/".$competitionId; ?>">Details</a>
	</li>
<?php 	else: ?>
	<li class="">
	<a href="<?=site_url('competition/details')."/".$competitionId; ?>">Details</a>
	</li>
<?php 	endif; ?>
<?php 	if ($subMenuItem == "Results"): ?>
	<li class="active">
	<a href="<?=site_url('competition/results')."/".$competitionId; ?>">Results</a>
	</li>
<?php 	else: ?>
	<li class="">
	<a href="<?=site_url('competition/results')."/".$competitionId; ?>">Results</a>
	</li>
<?php 	endif; ?>
<?php 	if ($subMenuItem == "Matches"): ?>
	<li class="active">
	<a href="<?=site_url('competition/matches')."/".$competitionId; ?>">Matches</a>
	</li>
<?php 	else: ?>
	<li class="">
	<a href="<?=site_url('competition/matches')."/".$competitionId; ?>">Matches</a>
	</li>
<?php 	endif; ?>
<?php endif; ?>
</ul>
<span>&nbsp;</span>
<?php if ($subMenuItem == "Details"): ?>
<?php	if (isset($legacySession->userdata['permissions']['Update Competition'])
		&& $legacySession->userdata['permissions']['Update Competition'] == 1 
		&& isset($legacySession->userdata['seriesPermissions'][$competitionDetails->competition_series_id])): ?>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url('competition/edit').'/'.$competitionDetails->competition_id;?>">
			Edit
		</a>
	</span>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url('competition/delete').'/'.$competitionDetails->competition_id;?>">
			Delete
		</a>
	</span>
<?php	endif; ?>
<?php endif; ?>
<?php if ($subMenuItem == "Results"): ?>
<?php   if (isset($legacySession->userdata['permissions']['Add Result'])
		&& $legacySession->userdata['permissions']['Add Result'] == 1 
		&& isset($legacySession->userdata['seriesPermissions'][$competitionResults['seriesID']])): ?>
	<?php 	if (!$competitionResults['fullResults']): ?>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url('competition/addRunner').'/'.$competitionResults['competitionID'];?>">
			Add Runner
		</a>
	</span>
	<?php 	endif; ?>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url('competition/addResult').'/'.$competitionResults['competitionID'];?>">
			Add Result
		</a>
	</span>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url('competition/addAllResults').'/'.$competitionResults['competitionID'];?>">
			Add All Results
		</a>
	</span>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url('competition/addUmpire').'/'.$competitionResults['competitionID']; ?>">
			Add Umpire
		</a>
	</span>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url('competition/addStandin').'/'.$competitionResults['competitionID']; ?>">
			Add Stand-In
		</a>
	</span>
	<?php endif; ?>
<?php endif; ?>
<?php if ($subMenuItem == "Matches"): ?>
<?php   if (isset($legacySession->userdata['permissions']['Add Matches'])
		&& $legacySession->userdata['permissions']['Add Matches'] == 1
		 && isset($legacySession->userdata['seriesPermissions'][$competitionMatches['competitionSeriesID']])): ?>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url('competition/addMatch').'/'.$competitionId;?>">
			Add Match
		</a>
	</span>
<?php   endif; ?>
<?php endif; ?>
</div>
</div>
</nav>