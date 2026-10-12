<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
<?php if ($subMenuItem == "List"): ?>
<?php else: ?>
<ul class="nav navbar-nav">
	<li class="">
		<a class="navbar-brand" href="<?=site_url('player/playerList') ?>">List</a>
	</li>
</ul>
<?php endif; ?>
<?php if ($competitorId > 0): ?>
<ul class="nav navbar-nav">
<?php 	if ($subMenuItem == "Details"): ?>
	<li class="active">
		<a href="<?=site_url('player/details')."/".$competitorId; ?>"><i class="fa fa-id-card-o fa-lg"></i> Details</a>
	</li>
<?php 	else: ?>
	<li class="">
		<a href="<?=site_url('player/details')."/".$competitorId; ?>"><i class="fa fa-id-card-o fa-lg"></i> Details</a>
	</li>
<?php 	endif; ?>
<?php 	if ($subMenuItem == "Results"): ?>
	<li class="active">
		<a href="<?=site_url('player/results')."/".$competitorId; ?>"><i class="fa fa-trophy fa-lg"></i> Results</a>
	</li>
<?php 	else: ?>
	<li class="">
		<a href="<?=site_url('player/results')."/".$competitorId; ?>"><i class="fa fa-trophy fa-lg"></i> Results</a>
	</li>
<?php 	endif; ?>
<?php 	if ($subMenuItem == "Matches"): ?>
	<li class="active">
		<a href="<?=site_url('player/matches')."/".$competitorId; ?>"><i class="fa fa-handshake-o fa-lg"></i> Matches</a>
	</li>
<?php 	else: ?>
	<li class="">
		<a href="<?=site_url('player/matches')."/".$competitorId; ?>"><i class="fa fa-handshake-o fa-lg"></i> Matches</a>
	</li>
<?php 	endif; ?>
<?php 	if ($subMenuItem == "Glicko"): ?>
	<li class="active">
		<a href="<?=site_url('player/glicko')."/".$competitorId; ?>"><i class="fa fa-line-chart fa-lg"></i> Glicko</a>
	</li>
<?php 	else: ?>
	<li class="">
		<a href="<?=site_url('player/glicko')."/".$competitorId; ?>"><i class="fa fa-line-chart fa-lg"></i> Glicko</a>
	</li>
<?php 	endif; ?>
</ul>
<span>&nbsp;</span>
<?php endif; ?>
<?php if (isset($legacySession->userdata['permissions']['Add Player']) &&
		$legacySession->userdata['permissions']['Add Player'] == TRUE): ?>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url("player/add")?>">
			<i class="fa fa-plus-circle fa-lg"></i> Add Player
		</a>
	</span>
<?php endif; ?>
<?php if ($competitorId > 0): ?>
<?php   if (isset($legacySession->userdata['permissions']['Update Player']) &&
		$legacySession->userdata['permissions']['Update Player'] == TRUE): ?>
	<span class="">
		<a class="btn btn-default navbar-btn" role="button" href="<?=site_url("player/edit").'/'.$competitorId?>">
			<i class="fa fa-pencil-square-o fa-lg"></i> Edit Player
		</a>
	</span>
	<span class="">
		<a class="btn btn-default navbar-btn btn-danger" role="button" href="<?=site_url("player/delete").'/'.$competitorId?>">
			<i class="fa fa-trash-o fa-lg"></i> Delete Player
		</a>
	</span>
<?php   endif; ?>
<?php endif; ?>
</div>
</div>
</nav>
<?php if ($subMenuItem == "List"): ?>
<nav>
<ul class="pagination">
<?php 	foreach ($initialList as $initial): ?>
<?php 		if ($currentInitial == $initial->initial): ?>
	<li class="active">
		<a href="#"><?=$initial->initial?></a>
	</li>
<?php 		else: ?>
	<li class="">
		<a href="<?=site_url('player/playerList').'/'.$initial->initial?>">
			<?=$initial->initial?>
		</a>
	</li>
<?php 		endif;?>
<?php 	endforeach; ?>
</ul>
<?php endif; ?>
