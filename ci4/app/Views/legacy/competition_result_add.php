<div class="inner_main container-fluid">
	<script type="text/javascript">
	<!--
	function validateInsertData() {
		if (document.getElementById('competitorId').selectedIndex > 0
			&& document.getElementById('position').value != ""
			&& document.getElementById('score').value != "") {
			document.forms[0].submit();
		} else {
			alert('You must fill in all details in order to insert a result');
		}
	}
	-->
	</script>
	<form class="form-horizontal" name="addresult" action="<?=site_url('competition/insertResult').'/'.$competitionId; ?>" method="post">
		<div id="addNewPlayerHolder"></div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="competitorId">Competitor</label>
			<div class="col-sm-4" style="display:inline-block;">
<?php if (count($playerList) > 0): ?>
				<select class="form-control" name="competitorId" id="competitorId">
					<option value="-1">Select a player:</option>
<?php 	foreach ($playerList as $player): ?>
					<option value="<?=$player->competitor_id; ?>"><?=$player->first_name." ".$player->last_name; ?></option>
<?php 	endforeach; ?>
				</select>
<?php endif; ?>
			</div>
			<a class="btn btn-primary" href="javascript:newPlayerPopup();" onClick="newPlayerNextAction = 'addResult';">
				New Player
			</a>
		</div>
<?php if ($competitionResults['type'] == "Mixed" || $competitionResults['type'] == "Doubles"): ?>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="competitorId2">Competitor (if doubles team)</label>
			<div class="col-sm-4" style="display:inline-block;">
<?php   if (count($playerList) > 0): ?>
				<select class="form-control" name="competitorId2" id="competitorId2">
					<option value="-1">Select a player:</option>
<?php 	  foreach ($playerList as $player): ?>
					<option value="<?=$player->competitor_id; ?>"><?=$player->first_name." ".$player->last_name; ?></option>
<?php 	  endforeach; ?>
				</select>
<?php   endif; ?>
			</div>
		</div>
<?php else: ?>
		<div><input class="form-control" type="hidden" name="competitorId2" id="competitorId2" value="-1" /></div>
<?php endif; ?>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="army">Army</label>
			<div class="col-sm-4" style="display:inline-block;">
<?php if (count($armyList) > 0): ?>
				<select class="form-control" name="army" id="army">
					<option value="-1">Select an army:</option>
<?php 	foreach ($armyList as $army): ?>
					<option value="<?=$army->army_id;?>">
						<?=$army->name." - ".$army->system_name." v".$army->version;?>
					</option>
<?php 	endforeach; ?>
				</select>
<?php endif; ?>
		   	 </div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="position">Position</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="position" id="position">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="score">Score</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="score" id="score">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="bonus">Bonus</label>
			<div class="col-sm-2" style="display:inline-block;">
				<input class="form-control" name="bonus" id="bonus">
			</div>
			<span class="col-sm-8">
				Bonus is a discrepancy between match scores and overall score, e.g. a penalty for late list.
			</span>
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-success" href="javascript:validateInsertData();">Insert</a>
		</div>
	</form>
</div>

