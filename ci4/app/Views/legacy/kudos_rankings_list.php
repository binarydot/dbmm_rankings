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
<div id="main">
	<div>
	<p>Kudos is an experimental ranking-like system that I am experimenting with to bring the objectivity of the Glicko
	numbers and the excitement of the rankings system together. Glikco is very clever, but because it changes quite
	slowly it can also get a bit boring. Rankings are open to arguments about how much a given competition is worth.</p>
	<p>The Kudos system works very similarly to the BHGS Rankings system. The only external parameter is the number of
	competitions that is deemed to be a 'full hand'. The weighting of each competition depends on the number and ability
	of the players attending and the number of rounds in the competition. The ability of the players is determined by 
	using their Glicko rating at the start of the competition. Crucially it is also slightly different for
	each person in the competiton because it doesn't include the player in question.
	The weighting is thus the square root of the sum of the ratings all the other players in the competition
	multiplied by the number of rounds.
	The square root has been introduced to try and stop Britcon, with  lots of players and 6 rounds, completely 
	dominating the ratings, at the expense of other smaller competitions.  
	</div>
<div class="">Date range for this ranking:
		<?=$rankingsData["startDate"];?> - <?=$rankingsData["endDate"];?>
	</div>
	<table>
		<tr class="table_header_row">
			<td class="table_header">#</td>
			<td class="table_header">Name</td>
			<td class="table_header">Score</td>
			<td class="table_header">Comps</td>
<?php foreach ($rankingsData['competitions'] as $competition): ?>
			<td class="table_cell"><?=$competition->competition_name?></td>
<?php endforeach; ?>
		</tr>
		<tr class="table_header_row">
			<td class="table_header"></td>
			<td class="table_header"></td>
			<td class="table_header"></td>
			<td class="table_header"></td>
<?php foreach ($rankingsData['competitions'] as $competition): ?>
			<td class="table_cell">
				<?=$rankingsData['totalRating'][$competition->competition_name]?>
			</td>
<?php endforeach; ?>
		</tr>
				<tr class="table_header_row">
			<td class="table_header"></td>
			<td class="table_header"></td>
			<td class="table_header"></td>
			<td class="table_header"></td>
<?php foreach ($rankingsData['competitions'] as $competition): ?>
			<td class="table_cell">
				<?=$rankingsData['rounds'][$competition->competition_name]?>
			</td>
<?php endforeach; ?>
		</tr>
		<?php $n = 1; ?>
<?php foreach ($rankingsData["rankings"] as $ranking): ?>
	<?php if ($n%2 == 0): ?>
		<tr class="evenRow">
	<?php else: ?>
		<tr class="oddRow">
	<?php endif; ?>
			<td class="table_cell_right"><?=$n;?></td>
			<td class="table_cell">
				<a href="<?=site_url('player/details').'/'.$ranking["playerId"];?>">
					<?=$ranking["playerName"];?>
				</a>
			</td>
			<td class="table_cell_right">
				<?=number_format($ranking["playerData"]["ranking"],2);?>
			</td>
			<td class="table_cell_center"><?=$ranking["playerData"]["numComps"];?></td>
	<?php foreach ($ranking["playerData"]["compData"] as $comp): ?>
			<td class="table_cell rankingCell">
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
	<div class="sub_header">Settings for this ranking:</div>
	<div>Maximum Competitions: <?=$rankingsData["minComps"];?></div>
</div>
<div class="sub_header">Competitions included in this ranking:</div>
	<table>
		<tr class="table_header_row">
			<td class="table_header">Date</td>
			<td class="table_header">Name</td>
			<td class="table_header">Weighting</td>
			<td class="table_header">Protected</td>
		</tr>
<?php foreach ($rankingsData["competitions"] as $competition): ?>
		<tr>
			<td class="table_cell"><?=$competition->competition_date;?></td>
			<td class="table_cell"><?=$competition->competition_name;?></td>
			<td class="table_cell"><?=$competition->weighting;?></td>
			<td class="table_cell">
	<?php if ($competition->protected): ?>
				Yes
	<?php else: ?>
				No
	<?php endif; ?>
			</td>
		</tr>
<?php endforeach; ?>
	</table>
</div>
