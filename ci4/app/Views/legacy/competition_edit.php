<div class="container-fluid">
<script type="text/JavaScript" src="<?=base_url()?>/js/addCompSeries.js">
<!--

-->
</script>
	<div id="addCompSeriesHolder"></div>
	<form class="form-horizontal" name="editcompetition" action="<?=site_url('competition/update')."/".$competitionDetails->competition_id?>" method="post">
		<div class="form-group">
			<label class="col-sm-4 control-label">ID</label>
			<div class="col-sm-4" style="display:inline-block;">
				<input class="form-control" disabled value="<?=$competitionDetails->competition_id?>" >
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="name">Name</label>
			<div class="col-sm-8" style="display:inline-block;">
				<input class="form-control" name="name" id="name" value="<?=$competitionDetails->competition_name?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="series">Series Name</label>
			<div class="col-sm-4" style="display:inline-block;">
				<select class="form-control" name="series" id="series">
<?php  foreach ($competitionSeries as $series): ?>
<?php 	if ($series->id == $competitionDetails->competition_series_id): ?>
					<option value="<?=$series->id?>" selected><?=$series->name?></option>
<?php 	else: ?>	
					<option value="<?=$series->id?>"><?=$series->name?></option>
<?php 	endif; ?>
<?php  endforeach; ?>
				</select>
			</div>
			<a class="btn btn-primary col-sm-4" href="javascript:addCompetitionSeries();">Add competition series</a>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="location">Location</label>
			<div class="col-sm-4" style="display:inline-block;">
				<input class="form-control" name="location" id="location" value="<?=$competitionDetails->location?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="date">Competiton Date</label>
					<script type="text/javascript">
					<!--
						/*window.onload = function(){
							new JsDatePick({
								useMode:2,
								dateFormat:"%Y-%m-%d",
								target:"date"
							});
						};*/
					-->
					</script>
			<div class="col-sm-4" style="display:inline-block;">
				<input class="form-control" type="date" name="date" id="date" value="<?=$competitionDetails->competition_date?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="type">Type</label>
			<div class="col-sm-4" style="display:inline-block;">
				<select class="form-control" name="type" id="type">
<?php  if ($competitionDetails->type == "Singles"): ?>
					<option value="Singles" selected>Singles</option>
<?php  else: ?>
					<option value="Singles">Singles</option>
<?php  endif; ?>
<?php  if ($competitionDetails->type == "Mixed"): ?>
					<option value="Mixed" selected>Mixed</option>
<?php  else: ?>
					<option value="Mixed">Mixed</option>
<?php  endif; ?>
<?php  if ($competitionDetails->type == "Doubles"): ?>
					<option value="Doubles" selected>Doubles</option>
<?php  else: ?>
					<option value="Doubles">Doubles</option>
<?php  endif; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="protected">Protected</label>
			<div class="col-sm-2" style="display:inline-block;">
				<select class="form-control" name="protected" id="protected">
<?php  if ($competitionDetails->protected == 0): ?>
					<option value="0" selected>No</option>
					<option value="1">Yes</option>
<?php  else: ?>
					<option value="0">No</option>
					<option value="1" selected>Yes</option>
<?php  endif; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="weighting">Weighting</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="weighting" id="weighting" value="<?=$competitionDetails->weighting?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="glicko_eligible">Eligible for Glicko</label>
			<div class="col-sm-2" style="display:inline-block;">
				<select class="form-control" name="glicko_eligible" id="glicko_eligible">
<?php  if ($competitionDetails->glicko_eligible == 1): ?>
					<option value="0">No</option>
					<option value="1" selected>Yes</option>
<?php  else: ?>
					<option value="0" selected>No</option>
					<option value="1">Yes</option>
<?php  endif; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="rar">Country/Region</label>
			<div class="col-sm-4" style="display:inline-block;">
				<select class="form-control" name="rar" id="rar">
<?php  foreach ($rars as $rar): ?>
<?php 	if ($rar->rar_id == $competitionDetails->rar_id): ?>
					<option value="<?=$rar->rar_id?>" selected><?=$rar->rar_name?></option>
<?php  	else: ?>
					<option value="<?=$rar->rar_id?>"><?=$rar->rar_name?></option>
<?php  	endif; ?>
<?php  endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="num_competitors">Number of Competitors/Teams</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="num_competitors" id="num_competitors" value="<?=$competitionDetails->num_competitors?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="rounds">Rounds</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="rounds" id="rounds" value="<?=$competitionDetails->rounds?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="system">System</label>
			<div class="col-sm-4" style="display:inline-block;">
				<select class="form-control" name="system" id="system" >
<?php  foreach ($systems as $system): ?>
<?php 	if ($system->system_id == $competitionDetails->system_id): ?>
					<option value="<?=$system->system_id?>" selected><?=$system->system_name?> <?=$system->version?></option>
<?php  	else: ?>
					<option value="<?=$system->system_id?>"><?=$system->system_name?> <?=$system->version?></option>
<?php  	endif; ?>
<?php  endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="scale">Scale</label>
			<div class="col-sm-2" style="display:inline-block;">
				<select class="form-control" name="scale" id="scale" >
<?php  foreach ($scales as $scale): ?>
<?php 	if ($scale->scale_id == $competitionDetails->scale_id): ?>
					<option value="<?=$scale->scale_id?>" selected><?=$scale->desc?></option>
<?php  	else: ?>
					<option value="<?=$scale->scale_id?>"><?=$scale->desc?></option>
<?php  	endif; ?>
<?php  endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="army_points">Army Points</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="army_points" id="army_points" value="<?=$competitionDetails->army_points?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="lists_allowed">Number of Lists Allowed</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="lists_allowed" id="lists_allowed" value="<?=$competitionDetails->lists_allowed?>">
			</div>
			<span class="col-sm-6">(fractional if the player is allowed to substitute part of the list)</span>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="scoring_system_id">Scoring System</label>
			<div class="col-sm-4" style="display:inline-block;">
				<select class="form-control" name="scoring_system_id" id="scoring_system_id"  >
<?php  foreach ($scoringSystems as $scoring_system): ?>
<?php 	if ($scoring_system->scoring_system_id == $competitionDetails->scoring_system_id): ?>
					<option value="<?=$scoring_system->scoring_system_id?>" selected><?=$scoring_system->desc?></option>
<?php  	else: ?>
					<option value="<?=$scoring_system->scoring_system_id?>"><?=$scoring_system->desc?></option>
<?php  	endif; ?>
<?php  endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="format_id">Format</label>
			<div class="col-sm-4" style="display:inline-block;">
				<select class="form-control" name="format_id" id="format_id"  >
<?php  foreach ($formats as $format): ?>
<?php 	if ($format->format_id == $competitionDetails->format_id): ?>
					<option value="<?=$format->format_id?>" selected><?=$format->desc?></option>
<?php  	else: ?>
					<option value="<?=$format->format_id?>"><?=$format->desc?></option>
<?php  	endif; ?>
<?php  endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="theme_id">Theme</label>
			<div class="col-sm-4" style="display:inline-block;">
				<select class="form-control" name="theme_id" id="theme_id"  >
<?php  foreach ($themes as $theme): ?>
<?php 	if ($theme->theme_id == $competitionDetails->theme_id): ?>
					<option value="<?=$theme->theme_id?>"><?=$theme->desc?></option>
<?php  	else: ?>
<?php  	endif; ?>
<?php  endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label" for="notes">Notes</label>
			<div class="col-sm-8" style="display:inline-block;">
				<input class="form-control" name="notes" id="notes" value="<?=$competitionDetails->notes?>">
			</div>
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-danger" href="<?=site_url('competition/details')."/".$competitionDetails->competition_id?>">Cancel</a>
			<a class="btn btn-success" href="javascript:document.forms[0].submit();">Update</a>
		</div>
	</form>
</div>