<script type="text/javascript">
<!--
	function reloadPage() {
		$year = document.getElementById('year').value;
		window.location = "<?=site_url('matches/matchesDetails')."/"?>" + $year;
	}
-->
</script>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
<?php if ($subMenuItem == "Match Results"): ?>
				<li class="active">
					<a href="<?=site_url('matches') ?>">Match Results</a>
				</li>
<?php else: ?>
				<li class="">
					<a href="<?=site_url('matches') ?>">Match Results</a>
				</li>
<?php endif; ?>
<?php if ($subMenuItem == "All Matches"): ?>
				<li class="active">
					<a href="<?=site_url('matches/matchesDetails').'/'.$yearUsed; ?>">Match Details</a>
				</li>
				<span>
					Year:
					<select name="year" id="year" onChange='reloadPage();'>
<? 	if ($yearUsed == date("Y") || $yearUsed == -1 || is_null($yearUsed)): ?>
						<option value="<?=date("Y")?>" selected><?=date("Y")?></option>
<? 	else: ?>
						<option value="<?=date("Y")?>"><?=date("Y")?></option>
<? 	endif; ?>
<? 	for($year = date("Y")-1; $year >= 2007; $year--): ?>
<? 		if ($yearUsed == $year): ?>
						<option value="<?=$year?>" selected><?=$year?></option>
<? 		else: ?>
						<option value="<?=$year?>"><?=$year?></option>
<?		endif; ?>
<?	endfor; ?>
					</select>
				</span>
				<span class="">
					<a class="btn btn-default navbar-btn" href="<?=site_url('matches/matchesDetailsCSV').'/'.$yearUsed;?>">
						Download CSV of data
					</a>
				</span>
<?php else: ?>
				<li class="">
					<a href="<?=site_url('matches/matchesDetails').'/'.date('Y'); ?>">Match Details</a>
				</li>
<?php endif; ?>
<?php if ($subMenuItem == "Annual Trends"): ?>
				<li class="active">
					<a href="<?=site_url('matches/annualTrends') ?>">Annual Trends</a>
				</li>
<?php else: ?>
				<li class="">
					<a href="<?=site_url('matches/annualTrends') ?>">Annual Trends</a>
				</li>
<?php endif; ?>
			</ul>
		</div>
	</div>
</nav>