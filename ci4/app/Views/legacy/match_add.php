<div class="container">
	<form class="form-horizontal" name="addmatch" action="<?=site_url('competition/insertMatch').'/'.$competitionId?>" method="post">
		<script type="text/javascript">
		<!--
			playerDefaultArmies = new Array();
<?php  foreach ($compPlayerList as $player): ?>
			playerDefaultArmies[<?=$player->competitor_id?>] = <?=$player->army_id?>;
<?php  endforeach; ?>

			function doSubmit() {
				if (isNaN(document.getElementById('round').value) ||
						parseInt(document.getElementById('round').value) < 1) {
					alert('Must specify the round as a number');
					// check scores are numeric
				} else if (isNaN(document.getElementById('winnerscore').value) ||
						isNaN(document.getElementById('loserscore').value)) {
					alert('Scores must be numeric');
					// check winner scored more than loser
				} else if (parseInt(document.getElementById('winnerscore').value) < 
						parseInt(document.getElementById('loserscore').value)) {
					alert('Winner score should be more than loser score');
				} else {
					document.forms[0].submit();
				}
			}
	
			function updateWinnerArmy() {
				selectedWinnerIndex = document.getElementById('winnerid0').selectedIndex;
				selectedWinnerId = document.getElementById('winnerid0').options.item(selectedWinnerIndex).value;
				if (selectedWinnerId == -1) {
					document.getElementById('winnerarmyna').selected = true;
				} else {
					playerDefaultArmy = playerDefaultArmies[selectedWinnerId];
					document.getElementById('winnerarmy' + playerDefaultArmy).selected = true;
				}
			}
	
			function updateLoserArmy() {
				selectedLoserIndex = document.getElementById('loserid0').selectedIndex;
				selectedLoserId = document.getElementById('loserid0').options.item(selectedLoserIndex).value;
				if (selectedLoserId == -1) {
					document.getElementById('loserarmyna').selected = true;
				} else {
					playerDefaultArmy = playerDefaultArmies[selectedLoserId];
					document.getElementById('loserarmy' + playerDefaultArmy).selected = true;
				}
			}
		-->
		</script>
		<h2>
			<?=$compData->competition_name?>
		</h2>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="matchdate">Match Date</label>
			<div class="col-sm-4">
					<script type="text/javascript">
					<!--
						window.onload = function(){
							new JsDatePick({
								useMode:2,
								dateFormat:"%Y-%m-%d",
								target:"matchdate"
							});
						};
					-->
					</script>
				<input class="form-control" name="matchdate" id="matchdate" value="<?=$compData->competition_date?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="glicko_eligible">Eligible for Glicko</label>
			<div class="col-sm-4">
				<select class="form-control" name="glicko_eligible" id="glicko_eligible">
<?php  if (0 == $compData->glicko_eligible): ?>
					<option value="0" selected>No</option>
<?php  else: ?>
					<option value="0">No</option>
<?php  endif; ?>
<?php  if (1 == $compData->glicko_eligible): ?>
					<option value="1" selected>Yes</option>
<?php  else: ?>
					<option value="1">Yes</option>
<?php  endif; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="round">Round</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="round" id="round" value="" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Winner(s)</label>
			<div class="col-sm-4">
				<select class="form-control" name="winnerid0" id="winnerid0" onChange="updateWinnerArmy();">
<?php  foreach ($compPlayerList as $player): ?>
					<option value="<?=$player->competitor_id?>"><?=$player->first_name?> <?=$player->last_name?></option>
<?php  endforeach; ?>
					<option value="-1">-- BYE --</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"></label>
			<div class="col-sm-4">
				<select class="form-control" name="winnerid1" id="winnerid1">
					<option value="-1">None</option>
<?php  foreach ($compPlayerList as $player): ?>
					<option value="<?=$player->competitor_id?>"><?=$player->first_name?> <?=$player->last_name?></option>
<?php  endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Army</label>
			<div class="col-sm-4">
				<select class="form-control" name="winnerarmy" id="winnerarmy">
<?php  foreach ($armyList as $army): ?>
<?php    if ($compPlayerList[0]->army_id == $army->army_id): ?>
					<option id="winnerarmy<?=$army->army_id?>" value="<?=$army->army_id?>" selected>
						<?=$army->name?>
					</option>
<?php    else: ?>
					<option id="winnerarmy<?=$army->army_id?>" value="<?=$army->army_id?>">
						<?=$army->name?>
					</option>
<?php    endif; ?>
<?php  endforeach; ?>
					<option id="winnerarmyna" value="-1">N/A</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Winner Score</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="winnerscore" id="winnerscore" value="" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Loser Score</label>
			<div class="col-sm-4">
				<input class="form-control" type="text" name="loserscore" id="loserscore" value="" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Loser(s)</label>
			<div class="col-sm-4">
				<select class="form-control" name="loserid0" id="loserid0" onChange="updateLoserArmy();">
<?php  foreach ($compPlayerList as $player): ?>
					<option value="<?=$player->competitor_id?>"><?=$player->first_name?> <?=$player->last_name?></option>
<?php  endforeach; ?>
					<option value="-1">-- BYE --</option>
				</select>
			</div>
			<div class="col-sm-6"><span id="loserPlayerErrorMessage"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"></label>
			<div class="col-sm-4">
				<select class="form-control" name="loserid1" id="loserid1">
					<option value="-1">None</option>
<?php  foreach ($compPlayerList as $player): ?>
					<option value="<?=$player->competitor_id?>"><?=$player->first_name?> <?=$player->last_name?></option>
<?php  endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Army</label>
			<div class="col-sm-4">
				<select class="form-control" name="loserarmy" id="loserarmy">
<?php  foreach ($armyList as $army): ?>
<?php    if ($compPlayerList[0]->army_id == $army->army_id): ?>
					<option id="loserarmy<?=$army->army_id?>" value="<?=$army->army_id?>" selected>
						<?=$army->name?>
					</option>
<?php    else: ?>
					<option id="loserarmy<?=$army->army_id?>" value="<?=$army->army_id?>">
						<?=$army->name?>
					</option>
<?php    endif; ?>
<?php  endforeach; ?>
					<option id="loserarmyna" value="-1">N/A</option>
				</select>
			</div>
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-success" href="javascript:doSubmit()">Add</a>
		</tr>
	</form>
</div>
