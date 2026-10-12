<div class="container-fluid">
	<script type="text/javascript">
	<!--
	function validateInsertData() {
		if (document.getElementById('competitorId').selectedIndex > 0
			&& document.getElementById('position').value != ""
			&& document.getElementById('score').value != "") {
			document.forms[0].submit();
		} else {
			alert('You must fill in all details in order to update a result');
		}
	}
	-->
	</script>
	<form class="form-horizontal" name="addresult" action="<?=site_url('competition/updateResult').'/'.$competitionId.'/'.$resultId; ?>" method="post">
		<div id="addNewPlayerHolder"></div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="competitorId">Competitor</label>
			<div class="col-sm-4" style="display:inline-block;">
<?php if (count($playerList) > 0): ?>
				<select class="form-control" name="competitorId" id="competitorId">
<?php 	foreach ($playerList as $player): ?>
<?php     if ($player->competitor_id == $resultData->competitor_id): ?>
					<option value="<?=$player->competitor_id; ?>" selected><?=$player->first_name." ".$player->last_name; ?></option>
<?php     else: ?>
					<option value="<?=$player->competitor_id; ?>"><?=$player->first_name." ".$player->last_name; ?></option>
<?php     endif;?>
<?php 	endforeach; ?>
				</select>
<?php endif; ?>
			</div>
			<a class="btn btn-primary" href="javascript:newPlayerPopup();" onClick="newPlayerNextAction = 'addResult';">
				New Player
			</a>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="army">Army</label>
			<div class="col-sm-4" style="display:inline-block;">
<?php if (count($armyList) > 0): ?>
				<select class="form-control" name="army" id="army">
<?php 	foreach ($armyList as $army): ?>
<?php     if ($army->army_id == $resultData->army_id): ?>
					<option value="<?=$army->army_id;?>" selected>
						<?=$army->name." - ".$army->system_name." v".$army->version;?>
					</option>
<?php     else: ?>
					<option value="<?=$army->army_id;?>">
						<?=$army->name." - ".$army->system_name." v".$army->version;?>
					</option>
<?php     endif;?>
<?php 	endforeach; ?>
				</select>
<?php endif; ?>
		    	</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="position">Position</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="position" id="position" value="<?=$resultData->position?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="score">Score</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="score" id="score" value="<?=$resultData->score?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="bonus">Bonus</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="bonus" id="bonus" value="<?=$resultData->bonus?>" />
			</div>
			<span class="col-sm-8">
				Bonus is a discrepancy between match scores and overall score, e.g. a penalty for late list.
			</span>
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-danger" href="<?=site_url('competition/details').'/'.$competitionId?>">Cancel</a>
			<a class="btn btn-success" href="javascript:validateInsertData();">Update</a>
		</div>
	</form>
</div>

