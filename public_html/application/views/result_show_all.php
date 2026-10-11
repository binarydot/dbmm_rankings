<script type="text/javascript">
	<!--
	function saveData(id) {
		var allOK = true;
		for (a = 0; a < <?=count($results['results'])?>; a++) {
			if (document.getElementById('nameval' + a).innerHTML.trim() == "OK" && document.getElementById('armyval' + a).innerHTML.trim() == "OK") {
				// this line of data is OK
			} else {
				allOK = false;
			}
		}
		if (allOK == false) {
			alert("There are still errors in the data");
		} else {
			alert("This will delete all data for this competition and replace it with the data you are uploading.");
			//updateResultsRequest(id);
			document.getElementById('allResultsForm').submit();
		}
	}
	function setName(row) {
		if (document.getElementById('nameSelect' + row).selectedIndex > 0) {
			var competitorID = document.getElementById('nameSelect' + row).value;
			if (competitorID != "-1") {
				var name = document.getElementById('nameSelect' + row).options[document.getElementById('nameSelect' + row).selectedIndex].text;
				document.getElementById('nameval' + row).innerHTML = "OK";
				document.getElementById('nameval' + row).style.color = "#0f0";
				document.getElementById('namecell' + row).innerHTML = "<input name=\"p" + row + "\" id=\"p" + row + "\" value=\"" + competitorID + "\" hidden /><span class=\"green small\">" + name + "</span>";
				document.getElementById('namecell' + row).style.color = "#0f0";
			} else {
				document.getElementById('nameval' + row).innerHTML = "Error";
				document.getElementById('nameval' + row).style.color = "#f00";
			}
		} else {
			document.getElementById('nameval' + row).innerHTML = "Error";
			document.getElementById('nameval' + row).style.color = "#f00";
		}
	}
	
	function setArmy(row) {
		if (document.getElementById('armySelect' + row).selectedIndex > 0) {
			var armyID = document.getElementById('armySelect' + row).value;
			if (armyID != "-1") {
				var name = document.getElementById('armySelect' + row).options[document.getElementById('armySelect' + row).selectedIndex].text;
				document.getElementById('armyval' + row).innerHTML = "OK";
				document.getElementById('armyval' + row).style.color = "#0f0";
				document.getElementById('armycell' + row).innerHTML = "<input name=\"a" + row + "\" id=\"a" + row + "\" value=\"" + armyID + "\" hidden />";
				document.getElementById('armycell' + row).style.color = "#0f0";
			} else {
				document.getElementById('armyval' + row).innerHTML = "Error";
				document.getElementById('armyval' + row).style.color = "#f00";
			}
		} else {
			document.getElementById('armyval' + row).innerHTML = "Error";
			document.getElementById('armyval' + row).style.color = "#f00";
		}
	}
	-->
</script>
<form id="allResultsForm" action="<?=site_url('competition/insertAllResults').'/'.$competitionId?>" method="POST">
	<input id="c" name="c" value="<?=count($results['results'])?>" hidden />
	<input id="r" name="r" value="<?=$results['rounds']?>" hidden />
	<input id="competition_date" name="competition_date" value="<?=$competitionDetails->competition_date?>" hidden />
	
	<fieldset>
		<legend>Competition Result Data </legend>
		<div id="compTableHolder">
			<table>
		<tr>
		<th colspan="1">Position</td>
		<th colspan="1">Status</td>
		<th colspan="1">IWF ID & Name in file</td>
		<th colspan="1">Status</td>
		<th colspan="1">Army</td>
		<th colspan="1">Score</td>
		</tr>
<?	foreach ($results['results'] as $key => $result): ?>
			<tr>
			<td>
				<?=$result["position"]?>
				<input name="o<?=$key?>" id="o<?=$key?>" value="<?=$result['position']?>" hidden />
				<input name="f<?=$key?>" id="f<?=$key?>" value="<?=$result['id']?>" hidden />
			</td>
<?		if ($result['nameValidation'] == "Duplicates Found"): ?>
			<td id="nameval<?=$key?>" class="red small">
				<?=$result['nameValidation']?>
			</td>
			<td id="namecell<?=$key?>">
				<select name="nameSelect<?=$key?>" id="nameSelect<?=$key?>" onChange="setName(<?=$key?>)">
					<option value = "-1">Select one of the duplicates</option>
<?			foreach ($result["nameOptions"] as $b => $nameOption): ?>
					<option value="<?=$nameOption['competitorId']?>">
						<?=$nameOption['iwfId']." ".$nameOption['name']?>
					</option>
<? 			endforeach; ?>
				</select>
			</td>
<?		 elseif ($result['nameValidation'] == "OK"): ?>
			<td id="nameval<?=$key?>" class="green small">
				<?=$result['nameValidation']?>
			</td>
			<td id="namecell<?=$key?>">
				<input name="p<?=$key?>" id="p<?=$key?>" value="<?=$result['competitorId']?>" hidden />
				<span class="green small">
					<?=$result['iwfId']?>
				</span>
				<span class="green small"><?=$result['name']?></span>
			</td>
<?		elseif ($result['nameValidation'] == "Not Found"): ?>
			<td id="nameval<?=$key?>" class="red small">
				<?=$result['nameValidation']?>
			</td>
			<td  id="namecell<?=$key?>">
				<select name="nameSelect<?=$key?>" id="nameSelect<?=$key?>" onChange="setName(<?=$key?>)">
					<option value="-1">Select a player</option>
<?			foreach ($playerList as $player): ?>
					<option value="<?=$player->competitor_id?>">
						<?=$player->first_name?> <?=$player->last_name?> (<?=$player->rar_name?>)
					</option>
<?			endforeach; ?>
				</select>
				<span id="name<?=$key?>" class="red small"><?=$result['name']?></span>
			</td>
<?		else: ?>
			<td id="nameval<?=$key?>" class="red small">
				<?=$result['nameValidation']?>
			</td>
			<td  id="namecell<?=$key?>">
				<?=$result['nameValidation']?>
			</td>
<?		endif; ?>
<?		if ($result['armyValidation'] == "Duplicates Found"): ?>
			<td id="armyval<?=$key?>" class="red small">
				<?=$result['armyValidation']?>
			</td>
			<td  id="armycell<?=$key?>">
				<span id="army<?=$key?>" class="red small">Duplicate Found -></span>
				<select name="armySelect<?=$key?>" id="armySelect<?=$key?>" onChange="setArmy(<?=$key?>)">
					<option value = "-1">Select one of the duplicates</option>
<?			foreach ($result['armyOptions'] as $army): ?>
					<option value="<?=$army['armyId']?>"><?=$result['army']?> <?=$army['version']?></option>
<?			endforeach; ?>
				</select>
			</td>
<?		elseif ($result['armyValidation'] == "OK"): ?>
			<td id="armyval<?=$key?>" class="green small">
				<?=$result['armyValidation']?>
			</td>
			<td  id="armycell<?=$key?>">
				<input name="a<?=$key?>" id="a<?=$key?>" value="<?=$result['army_id']?>" hidden />
			</td>
<?		elseif ($result['armyValidation'] == "Not Found"): ?>
			<td id="armyval<?=$key?>" class="red small">
				<?=$result['armyValidation']?>
			</td>
			<td id="armycell<?=$key?>">
				<span id="army<?=$key?>" class="red small"><?=$result['army']?></span>
				<select name="armySelect<?=$key?>" id="armySelect<?=$key?>" onChange="setArmy(<?=$key?>)">
					<option value = "-1">Select an army</option>
<?			foreach ($armyList as $army): ?>
					<option value="<?=$army->army_id?>"><?=$army->name?> <?=$army->system_name?> <?=$army->version?></option>
<?			endforeach; ?>
				</select>
			</td>
<?		else: ?>
			<td id="armyval<?=$key?>" class="red small">
				<?=$result['armyValidation']?>
			</td>
			<td id="armycell<?=$key?>">
				<?=$result['armyValidation']?>
			</td>
<?		endif; ?>
			<td>
				<?=$result['score']?>
				<input name="s<?=$key?>" id="s<?=$key?>" value="<?=$result['score']?>" hidden />
			<td>
				<span class="button"><a href="javascript:deleteRow();">Delete</a></span>
			</td>
		</tr>
<?	endforeach; ?>
	</table>
		
</div>
</fieldset>
	<fieldset>
		<legend>Round by Round Match Data</legend>
		<div id="roundTableHolder">
			<table>
<? foreach ($results['matches'] as $round): ?>
				<tr>
					<td>Round <?=$round['number']?></td>
				</tr>
<?	$matches = 0; ?>
<? 	foreach ($round['matches'] as $match): ?>
				<tr>
					<td>
						<?=$match['winnerName']?>
						<input name="r<?=$round['number']?>w<?=$matches?>" id="r<?=$round['number']?>w<?=$matches?>" value="<?=$match['winnerId']?>" hidden />
					</td>
					<td>
						<?=$match['winningScore']?> - <?=$match['losingScore']?>
						<input name="r<?=$round['number']?>ws<?=$matches?>" id="r<?=$round['number']?>ws<?=$matches?>" value="<?=$match['winningScore']?>" hidden />
						<input name="r<?=$round['number']?>ls<?=$matches?>" id="r<?=$round['number']?>ls<?=$matches?>" value="<?=$match['losingScore']?>" hidden />
					</td>
					<td>
						<?=$match['loserName']?>
						<input name="r<?=$round['number']?>l<?=$matches?>" id="r<?=$round['number']?>l<?=$matches?>" value="<?=$match['loserId']?>" hidden />
					</td>
				</tr>
<?		$matches++; ?>
<? 	endforeach; ?>
				<input name="r<?=$round['number']?>c" id="r<?=$round['number']?>c" value="<?=$matches?>" hidden />
<? endforeach; ?>
			</table>
		</div>
	</fieldset>
</form>

<table class="button_table">
	<tr class="action_bar">
		<td>
			<span class="button">
				<a href="javascript:saveData(<?=$competitionId; ?>);">Save Data</a>
			</span>
		</td>
	</tr>
</table>