<div class="container-fluid">
	<form class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">ID</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="" type="text" disabled value="<?=$competitionDetails->competition_id?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Name</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->competition_name?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Series Name</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->competition_series_name?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Location</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->location?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Competiton Date</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->competition_date?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Type</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->type?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Protected</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value=
<?php if (0 == $competitionDetails->protected): ?>
					"No"
<?php else: ?>
					"Yes"
<?php endif; ?>
				>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Weighting</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->weighting?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Eligible for Glicko</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value=
<?php if (1 == $competitionDetails->glicko_eligible): ?>
					"Yes"
<?php else: ?>
					"No"
<?php endif; ?>
				>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Country/Region</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->rar_name?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Number of Competitors/Teams</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->num_competitors?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Rounds</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->rounds?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">System</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->system_name." ".$competitionDetails->version?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Scale</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->scale_desc?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Army Points</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->army_points?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Number of Lists Allowed</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="col-sm-2 form-control" name="" type="text" disabled value="<?=$competitionDetails->lists_allowed?>">
			</div>
			<label class="col-sm-8 control-label" for="">(fractional if player is allowed to substitute part of the list)</label>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Scoring System</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->scoring_system_desc?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Format</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->format_desc?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Theme</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->theme_desc?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="">Notes</label>
			<div class="col-sm-6" style="display:inline-block;">
				<input class="col-sm-6 form-control" name="" type="text" disabled value="<?=$competitionDetails->notes?>">
			</div>
		</div>
	</form>
</div>
			