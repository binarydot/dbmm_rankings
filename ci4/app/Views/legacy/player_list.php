<div id="main">
	<table class="table table-striped" id="allPlayerTable">
		<thead>
			<tr class="">
				<th class="" colspan="1">IWF ID</th>
				<th class=""><?=legacy_lang("title_First_Name")?></th>
				<th class=""><?=legacy_lang("title_Last_Name")?></th>
				<th class=""><?=legacy_lang("title_RAR")?></th>
				<th class=""><?=legacy_lang("title_Comps")?></th>
				<th class=""><?=legacy_lang("title_Best_Place")?></th>
				<th class=""><?=legacy_lang("title_Worst_Place")?></th>
				<th class=""><?=legacy_lang("title_Armies_Used")?></th>
				<!--<th class=""><?=legacy_lang("title_Average_Score")?></th>-->
<?php if ($legacySession->userdata('Update Player') == 1): ?>
				<th class="" colspan="4"><?=legacy_lang("title_Actions")?></th>
<?php else: ?>
				<th class="" colspan="2"><?=legacy_lang("title_Actions")?></th>
<?php endif; ?>
			</tr>
		</thead>
		<tbody>

<?php foreach ($playerList as $player): ?>
			<tr id="playerRow<?=$player['competitorID']?>">
				<td class=""><?=$player['iwfID']?>
				</td>
				<td class=""><a
					href="<?=site_url('player/details').'/'.$player['competitorID']?>">
						<?=$player['firstName']?>
				</a></td>
				<td class=""><a
					href="<?=site_url('player/details').'/'.$player['competitorID']?>">
						<?=$player['lastName']?>
				</a></td>
				<td class="">
					<?=getImg($player['rarName'])?>
					<?=$player['rarName']?>
				</td>
				<td class="text-right"><?=$player['numComps']?>
				</td>
<?php	if ($player['numComps'] > 0): ?>
				<td class="text-right">
					<?=$player['bestPlace']?> out of <?=$player['bestPlaceFrom']?>
				</td>
				<td class="text-right">
					<?=$player['worstPlace']?> out of <?=$player['worstPlaceFrom']?>
				</td>
<?php 	else: ?>
				<td class="text-right">
					N/A
				</td>
				<td class="text-right">
					N/A
				</td>
<?php 	endif; ?>
				<td class="text-right"><?=$player['numArmies']?>
				</td>
				<!--<td class="table_cell"></td>-->
<?php 	if (isset($legacySession->userdata['permissions']['Update Player']) &&
		$legacySession->userdata['permissions']['Update Player'] == 1): ?>
				<td>
					<span class="">
						<!--<a href="javascript:editPlayerNextAction = 'listPlayers';editPlayerPopup(<?=$player['competitorID']?>);">-->
						<a class="btn btn-default" href="<?=site_url('player/edit').'/'.$player['competitorID']?>">
							<i class="fa fa-pencil-square-o fa-lg"></i> <?=legacy_lang("title_Update")?>
						</a>
					</span>
				</td>
				<td>
					<span class="">
						<!--<a href="javascript:deletePlayerThenShowPlayers(<?=$player['competitorID']?>);">-->
						<a class="btn btn-default btn-danger" href="<?=site_url('player/delete').'/'.$player['competitorID']?>">
							<i class="fa fa-trash-o fa-lg"></i> <?=legacy_lang("title_Delete")?>
						</a>
					</span>
				</td>
<?php 	endif; ?>
				<td>
					<span class="">
						<a class="btn btn-default" href="index.php?action=dolistresultsbyplayer&id=<?=$player['competitorID']?>">
							<i class="fa fa-trophy fa-lg"></i> <?=legacy_lang("title_Results")?>
						</a>
					</span>
				</td>
				<td>
					<span class="">
						<a class="btn btn-default" href="index.php?action=dolistmatchesbyplayer&id=<?=$player['competitorID']?>">
							<i class="fa fa-handshake-o fa-lg"></i> <?=legacy_lang("title_Matches")?>
						</a>
					</span>
				</td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
	<script type="text/javascript">
		// now we need to make the table sortable, since this is normally on page load and the table isn't present when the page is loaded
		sorttable.makeSortable(document.getElementById('allPlayerTable'));
	</script>

</div>