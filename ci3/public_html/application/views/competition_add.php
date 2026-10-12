<div class="container-fluid">
<script type="text/JavaScript" src="<?=base_url()?>/js/addCompSeries.js">
<!--

-->
</script>
	<div id="addCompSeriesHolder"></div>
	<form name="addcompetition" action="<?=site_url('competition/insert')?>" method="post" class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-2 control-label" for="name">Name</label>
			<div class="col-sm-4">
				<input class="form-control" name="name" id="name">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="series">Series Name</label>
			<div class="col-sm-4">
				<div id="seriesPicker">
					<select class="form-control" name="series" id="series">
<? foreach ($competitionSeries as $series): ?>
							<option value="<?=$series->id?>"><?=$series->name?></option>
<? endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col-sm-2">
				<a class="btn btn-default" href="javascript:addCompetitionSeries();">Add competition series</a>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="location">Location</label>
			<div class="col-sm-4">
				<input class="form-control" name="location" id="location">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Competiton Date</label>
			<div class="col-sm-4">
				<!-- 
					<input type="text" name="dateyyyy" id="dateyyyy" value="yyyy" width="4" maxlength="4" >
					<input type="text" name="datemm" id="datemmm" value="mm" width="2" maxlength="2" >
					<input type="text" name="datedd" id="datedd" value="dd" width="2" maxlength="2" >
					-->
					<script type="text/javascript">
					<!--
						window.onload = function(){
							new JsDatePick({
								useMode:2,
								dateFormat:"%Y-%m-%d",
								target:"date"
							});
						};
					-->
					</script>
					<input class="form-control" type="text" name="date" id="date">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="type">Type</label>
			<div class="col-sm-4">
				<select class="form-control" name="type" id="type">
					<option value="Singles">Singles</option>
					<option value="Mixed">Mixed</option>
					<option value="Doubles">Doubles</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="protected">Protected</label>
			<div class="col-sm-4">
				<select class="form-control" name="protected" id="protected">
					<option value="0" selected>No</option>
					<option value="1">Yes</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="weighting">Weighting</label>
			<div class="col-sm-4">
				<input class="form-control" name="weighting" id="weighting">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="glicko-eligible">Eligible for Glicko</label>
			<div class="col-sm-4">
				<select class="form-control" name="glicko_eligible" id="glicko_eligible">
					<option value="0">No</option>
					<option value="1" selected>Yes</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="rar">Country/Region</label>
			<div class="col-sm-4">
					<select class="form-control" name="rar" id="rar">
<? foreach ($rars as $rar): ?>
						<option value="<?=$rar->rar_id?>"><?=$rar->rar_name?></option>
<? endforeach; ?>
					</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="num_competitors">Number of Competitors/Teams</label>
			<div class="col-sm-4">
				<input class="form-control" name="num_competitors" id="num_competitors">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="rounds">Rounds</label>
			<div class="col-sm-4">
				<input class="form-control" name="rounds" id="rounds" value="4">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="system">System</label>
			<div class="col-sm-4">
				<select class="form-control" name="system" id="system" >
<? foreach ($systems as $system): ?>
					<option value="<?=$system->system_id?>"><?=$system->system_name?> <?=$system->version?></option>
<? endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="scale">Scale</label>
			<div class="col-sm-4">
				<select class="form-control" name="scale" id="scale" >
<? foreach ($scales as $scale): ?>
					<option value="<?=$scale->scale_id?>"><?=$scale->desc?></option>
<? endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="army_points">Army Points</label>
			<div class="col-sm-4">
				<input class="form-control" name="army_points" id="army_points" value="400">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="liast_allowed">Number of Lists Allowed</label>
			<div class="col-sm-4">
				<input class="form-control" name="lists_allowed" id="lists_allowed" value="1">
			</div>
			<div class="col-sm-6">
				(fractional if the player is allowed to substitute part of the list)
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="scoring_system">Scoring System</label>
			<div class="col-sm-4">
				<select class="form-control" name="scoring_system_id" id="scoring_system_id"  >
<? foreach ($scoringSystems as $scoring_system): ?>
					<option value="<?=$scoring_system->scoring_system_id?>"><?=$scoring_system->desc?></option>
<? endforeach; ?>
				</select>
			</div>
			<div class="col-sm-6"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="format_id">Format</label>
			<div class="col-sm-4">
				<select class="form-control" name="format_id" id="format_id"  >
<? foreach ($formats as $format): ?>
					<option value="<?=$format->format_id?>"><?=$format->desc?></option>
<? endforeach; ?>
				</select>
			</div>
			<div class="col-sm-6"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="theme_id">Theme</label>
			<div class="col-sm-4">
				<select class="form-control" name="theme_id" id="theme_id"  >
<? foreach ($themes as $theme): ?>
					<option value="<?=$theme->theme_id?>"><?=$theme->desc?></option>
<? endforeach; ?>
				</select>
			</div>
			<div class="col-sm-6"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="notes">Notes</label>
			<div class="col-sm-4">
				<input class="form-control" name="notes" id="notes">
			</div>
			<div class="col-sm-6"></div>
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-default btn-success" href="javascript:document.forms[0].submit();">Create</a>
			<a class="btn btn-default btn-danger" href="<?=site_url('competition')?>">Cancel</a>
		</div>
	</form>
</div>
