<?php 
	// we now use a mega return array of:
	// $rankingsData
	//   ["rankings"]
	//     ["Id".playerId]
	//       ["playerId"]
	//       ["playerName"]
	//       ["playerData"]
	//         [ranking] => ranking value
	//         [numComps] => number of competitions
	//         [compData] => list of ranking scores for each competiton
	//           [] => the comp number in order
	//             [score]
	//             [type] => protected or unprotected
	//             [notes] => for a hover over or pop-up 
	//   ["competitions"] => a query results object
	//   ["minComps"]
	//   ["startDate"]
	//   ["endDate"]
	//   ["maxScore"]

?>
<script type="text/javascript">
<!--
	function reloadPage() {
		$rar = document.getElementById('rar').value;
		$year = document.getElementById('year').value;
		window.location = "<?=site_url('rankings/comps')."/"?>" + $rar + "/" + $year;
	}
-->
</script>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
			<form class="navbar-form">
			<span>
				<?=lang("title_Country_Region")?>:
				<select class="form-control" name="rar" id="rar" onChange='reloadPage();'>
<? if ($rarUsed < 1): ?>
		<option value="-1" selected><?=lang("title_All")?></option>
		<? else: ?>
		<option value="-1"><?=lang("title_All")?></option>
<? endif; ?>
<? 	foreach ($rars as $rarinst): ?>
<? 		if ($rarUsed == $rarinst->rar_id): ?>
			<option value="<?=$rarinst->rar_id?>" selected><?=$rarinst->rar_name?></option>
<? 		else: ?>
			<option value="<?=$rarinst->rar_id?>"><?=$rarinst->rar_name?></option>
<? endif; ?>
			<? 	endforeach; ?>

				</select>
			</span>
			<span>
				<?=lang("title_Year")?>:
				<select class="form-control" name="year" id="year" onChange='reloadPage();'>
<? if ($yearUsed == "CURRENT" || $yearUsed == -1): ?>
		<option value="-1" selected><?=lang("title_Current")?></option>
		<? else: ?>
		<option value="-1"><?=lang("title_Current")?></option>
<? endif; ?>
<? 	for($year = 2006; $year <= date("Y"); $year++) { ?>
<? 		if ($yearUsed == $year): ?>
			<option value="<?=$year?>" selected><?=$year?></option>
<? 		else: ?>
			<option value="<?=$year?>"><?=$year?></option>
<? endif; ?>
			<? 	} ?>

				</select>
			</span>
			</form>
		</div>
	</div>	
</nav>
<div id="main" class="container-fluid">
	<h1><?=lang("blurbs_Rankings1")?>:
		<?=$rankingsData["startDate"];?> - <?=$rankingsData["endDate"];?>
	</h1>
	<table class="table table-striped table-condensed">
		<tr class="">
			<th class="" colspan="2"><?=lang("title_Position")?></th>
			<th class=""><?=lang("title_Name")?></th>
			<th class=""><?=lang("title_Score")?></th>
			<th class=""><?=lang("title_Competitions")?></th>
			<th class="" colspan="20"><?=lang("title_Contributing_Rankings")?></th>
		</tr>
<?php $n = 1; ?>
<?php foreach ($rankingsData["rankings"] as $ranking): ?>
		<tr class="">
			<td class="text-right"><?=$n;?></td>
			<td class="text-right"><?=getImg($ranking["rarName"])?></td>
			<td class="">
				<a href="<?=site_url('player/details').'/'.$ranking["playerId"];?>">
					<?=$ranking["playerName"];?>
				</a>
			</td>
			<td class="text-right">
				<?=number_format($ranking["playerData"]["ranking"],2);?>
			</td>
			<td class="text-center"><?=$ranking["playerData"]["numComps"];?></td>
	<?php foreach ($ranking["playerData"]["compData"] as $comp): ?>
			<td class="rankingCell">
		<?php if ($comp["type"] == "Protected/Used"): ?>
				<span class="purple helpy" title="<?=$comp["notes"];?>">
		<?php elseif ($comp["type"] == "Protected/Unused"): ?>
				<span class="red helpy" title="<?=$comp["notes"];?>">
		<?php elseif ($comp["type"] == "Unprotected/Used"): ?>
				<span class="green helpy" title="<?=$comp["notes"];?>">
		<?php elseif ($comp["type"] == "Unprotected/Unused"): ?>
				<span class="red helpy" title="<?=$comp["notes"];?>">
		<?php elseif ($comp["type"] == "Unprotected/Average"): ?>
				<span class="blue helpy" title="<?=$comp["notes"];?>">
		<?php endif; ?>
					<?=$comp["score"];?>
				</span>
			</td>
	<?php endforeach; ?>
		</tr>
	<?php $n++; ?>
<?php endforeach; ?>
	</table>
	<h2><?=lang("blurbs_Rankings2")?>:</h2>
	<div><?=lang("title_Maximum_Competitions")?>: <?=$rankingsData["minComps"];?></div>
	<div><?=lang("title_Maximum_Score")?>: <?=$rankingsData["maxScore"];?></div>
</div>
<div class="container-fluid">
	<h3><?=lang("blurbs_Rankings3")?>:</h3>
	<table class="table table-striped table-condensed">
		<tr class="">
			<th class=""><?=lang("title_Date")?></th>
			<th class=""><?=lang("title_Name")?></th>
			<th class=""><?=lang("title_Weighting")?></th>
			<th class=""><?=lang("title_Protected")?></th>
		</tr>
<?php foreach ($rankingsData["competitions"] as $competition): ?>
		<tr>
			<td class=""><?=$competition->competition_date;?></td>
			<td class=""><?=$competition->competition_name;?></td>
			<td class=""><?=$competition->weighting;?></td>
			<td class="">
	<?php if ($competition->protected): ?>
				<?=lang("title_Yes")?>
	<?php else: ?>
				<?=lang("title_No")?>
	<?php endif; ?>
			</td>
		</tr>
<?php endforeach; ?>
	</table>
</div>