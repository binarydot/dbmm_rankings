<form name="editmatch" action="<?=site_url('competition/updateMatch').'/'.$competitionId.'/'.$matchId?>" method="post">
	<script type="text/javascript">
	<!--
		playerDefaultArmies = new Array();
<? foreach ($compPlayerList as $player): ?>
		playerDefaultArmies[<?=$player->competitor_id?>] = <?=$player->army_id?>;
<? endforeach; ?>

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
	<table class="action_table">
		<tr>
			<td class="table_cell">Competition</td>
			<td class="table_cell">
	<?=$compData->competition_name;?>
			</td>
		</tr>
		<tr>
			<td class="table_cell">Match Date</td>
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
			<td class="table_cell">
				<input name="matchdate" id="matchdate" value="<?=$matchDetails['matchDate']?>">
			</td>
		</tr>
		<tr>
			<td class="table_cell">Eligible for Glicko</td>
			<td class="table_cell">
				<select name="glicko_eligible" id="glicko_eligible">
<?	if (0 == $matchDetails['glickoEligible']): ?>
					<option value="0" selected>No</option>
					<option value="1">Yes</option>
<?	else: ?>
					<option value="0">No</option>
					<option value="1" selected>Yes</option>
<?	endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="table_cell">Round</td>
			<td class="table_cell">
				<input type="text" name="round" id="round" value="<?=$matchDetails['round']?>" />
			</td>
		</tr>
		<tr>
			<td class="table_cell">Winner(s)</td>
			<td class="table_cell">
				<select name="winnerid0" id="winnerid0"  onChange="updateWinnerArmy();">
<?			foreach ($compPlayerList as $player): ?>
<?				if ($player->competitor_id == $matchDetails['winners'][0]['ID']): ?>
					<option value="<?=$player->competitor_id?>" selected>
						<?=$player->first_name?> <?=$player->last_name?>
					</option>
<?				else: ?>
					<option value="<?=$player->competitor_id?>">
						<?=$player->first_name?> <?=$player->last_name?>
					</option>
<?				endif; ?>
<?			endforeach; ?>
<?			foreach ($standIns as $standIn): ?>
<?				if ($standIn->competitor_id == $matchDetails['winners'][0]['ID']): ?>
					<option value="<?=$standIn->competitor_id?>">
						<?=$standIn->first_name?> <?=$standIn->last_name?>
					</option>
<?				else: ?>
					<option value="<?=$standIn->competitor_id?>">
						<?=$standIn->first_name?> <?=$standIn->last_name?>
					</option>
<?				endif; ?>
<?			endforeach; ?>
<?			if ($matchDetails['winners'][0]['ID'] == -1): ?>
					<option value="-1" selected>-- BYE --</option>
<?			else: ?>
					<option value="-1">-- BYE --</option>
<?			endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="table_cell"></td>
			<td class="table_cell">
				<select name="winnerid1" id="winnerid1"  onChange="updateWinnerArmy();">
<?			foreach ($compPlayerList as $player): ?>
<?				if ($player->competitor_id == $matchDetails['winners'][1]['ID']): ?>
					<option value="<?=$player->competitor_id?>" selected>
						<?=$player->first_name?> <?=$player->last_name?>
					</option>
<?				else: ?>
					<option value="<?=$player->competitor_id?>">
						<?=$player->first_name?> <?=$player->last_name?>
					</option>
<?				endif; ?>
<?			endforeach; ?>
<?			foreach ($standIns as $standIn): ?>
<?				if ($standIn->competitor_id == $matchDetails['winners'][1]['ID']): ?>
					<option value="<?=$standIn->competitor_id?>">
						<?=$standIn->first_name?> <?=$standIn->last_name?>
					</option>
<?				else: ?>
					<option value="<?=$standIn->competitor_id?>">
						<?=$standIn->first_name?> <?=$standIn->last_name?>
					</option>
<?				endif; ?>
<?			endforeach; ?>
<?			if ($matchDetails['winners'][1]['ID'] == -1
				|| !isset($matchDetails['winners'][1]['ID'])): ?>
					<option value="-1" selected>None</option>
<?			else: ?>
					<option value="-1">None</option>
<?			endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="table_cell">Army</td>
			<td class="table_cell">
				<select name="winnerarmy" id="winnerarmy">
<? foreach ($armyList as $army): ?>
<?   if ($matchDetails['winnerArmy']['ID'] == $army->army_id): ?>
					<option id="winnerarmy<?=$army->army_id?>" value="<?=$army->army_id?>" selected>
						<?=$army->name?>
					</option>
<?   else: ?>
					<option id="winnerarmy<?=$army->army_id?>" value="<?=$army->army_id?>">
						<?=$army->name?>
					</option>
<?   endif; ?>
<? endforeach; ?>
					<option id="winnerarmyna" value="-1">N/A</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="table_cell">Winner Score</td>
			<td class="table_cell">
				<input type="text" name="winnerscore" id="winnerscore" value="<?=$matchDetails['winnerScore']?>" />
			</td>
		</tr>
		<tr>
			<td class="table_cell">Loser Score</td>
			<td class="table_cell">
				<input type="text" name="loserscore" id="loserscore" value="<?=$matchDetails['loserScore']?>" />
			</td>
		</tr>
		<tr>
			<td class="table_cell">Loser(s)</td>
			<td class="table_cell">
				<select name="loserid0" id="loserid0"  onChange="updateLoserArmy();">
<?			foreach ($compPlayerList as $player): ?>
<?				if ($player->competitor_id == $matchDetails['losers'][0]['ID']): ?>
					<option value="<?=$player->competitor_id?>" selected>
						<?=$player->first_name?> <?=$player->last_name?>
					</option>
<?				else: ?>
					<option value="<?=$player->competitor_id?>">
						<?=$player->first_name?> <?=$player->last_name?>
					</option>
<?				endif; ?>
<?			endforeach; ?>
<?			foreach ($standIns as $standIn): ?>
<?				if ($standIn->competitor_id == $matchDetails['losers'][0]['ID']): ?>
					<option value="<?=$standIn->competitor_id?>">
						<?=$standIn->first_name?> <?=$standIn->last_name?>
					</option>
<?				else: ?>
					<option value="<?=$standIn->competitor_id?>">
						<?=$standIn->first_name?> <?=$standIn->last_name?>
					</option>
<?				endif; ?>
<?			endforeach; ?>
<?			if ($matchDetails['losers'][0]['ID'] == -1): ?>
					<option value="-1" selected>-- BYE --</option>
<?			else: ?>
					<option value="-1">-- BYE --</option>
<?			endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="table_cell"></td>
			<td class="table_cell">
				<select name="loserid1" id="loserid1"  onChange="updateLoserArmy();">
<?			foreach ($compPlayerList as $player): ?>
<?				if ($player->competitor_id == $matchDetails['losers'][1]['ID']): ?>
					<option value="<?=$player->competitor_id?>" selected>
						<?=$player->first_name?> <?=$player->last_name?>
					</option>
<?				else: ?>
					<option value="<?=$player->competitor_id?>">
						<?=$player->first_name?> <?=$player->last_name?>
					</option>
<?				endif; ?>
<?			endforeach; ?>
<?			foreach ($standIns as $standIn): ?>
<?				if ($standIn->competitor_id == $matchDetails['losers'][1]['ID']): ?>
					<option value="<?=$standIn->competitor_id?>">
						<?=$standIn->first_name?> <?=$standIn->last_name?>
					</option>
<?				else: ?>
					<option value="<?=$standIn->competitor_id?>">
						<?=$standIn->first_name?> <?=$standIn->last_name?>
					</option>
<?				endif; ?>
<?			endforeach; ?>
<?			if ($matchDetails['losers'][1]['ID'] == -1 
				|| !isset($matchDetails['losers'][1]['ID'])): ?>
					<option value="-1" selected>None</option>
<?			else: ?>
					<option value="-1">None</option>
<?			endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="table_cell">Army</td>
			<td class="table_cell">
				<select name="loserarmy" id="loserarmy">
<? foreach ($armyList as $army): ?>
<?   if ($matchDetails['loserArmy']['ID'] == $army->army_id): ?>
					<option id="loserarmy<?=$army->army_id?>" value="<?=$army->army_id?>" selected>
						<?=$army->name?>
					</option>
<?   else: ?>
					<option id="loserarmy<?=$army->army_id?>" value="<?=$army->army_id?>">
						<?=$army->name?>
					</option>
<?   endif; ?>
<? endforeach; ?>
					<option id="loserarmyna" value="-1">N/A</option>
				</select>
			</td>
		</tr>
		</table>
	<table>
		<tr class="action_bar">
			<td colspan="2" class="buttons">
			<td><span class="button"><a href="javascript:doSubmit();">Update</a></span></td>
		</tr>
	</table>
	</form>
	</div>

