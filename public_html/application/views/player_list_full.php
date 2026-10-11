<div id="main">
	<div class="settings_bar">
		Country/Region: <select name="rar" id="rar" onChange='reloadList();'>
<?php if ($rar == 0): ?>
			<option value="" selected>All</option>
<?php else: ?>
			<option value="">All</option>
<?php endif; ?>
<?php foreach ($rars as $rarinst): ?>
<?php 	if ($rar == $rarinst->rar_id): ?>
			<option value="<?=$rarinst->rar_id?>" selected>
				<?=$rarinst->rar_name?>
			</option>
<?php 	else: ?>
			<option value="<?=$rarinst->rar_id?>">
				<?=$rarinst->rar_name?>
			</option>
<?php 	endif;?>
<?php endforeach; ?>
		</select>
<?php if (isset($this->session->userdata['permissions']['Add Player']) &&
		$this->session->userdata['permissions']['Add Player'] == 1): ?>
		<span class="button">
			<a href="<?=site_url('player/add')?>">
				Add a new player
			</a>
		</span>
<?php endif; ?>
	</div>
	<table class="action_table sortable" id="allPlayerTable">
		<thead>
			<tr class="table_header_row">
				<td class="table_header" colspan="1">IWF ID</td>
				<td class="table_header">First Name</td>
				<td class="table_header">Last Name</td>
				<td class="table_header">RAR</td>
				<td class="table_header">Competitions</td>
				<td class="table_header">Best Place</td>
				<td class="table_header">Worst Place</td>
				<td class="table_header">Armies Used</td>
				<!--<td class="table_header">Average Score</td>-->
<?php if ($this->session->userdata('Update Player') == 1): ?>
				<td class="table_header" colspan="4">Actions</td>
<?php else: ?>
				<td class="table_header" colspan="2">Actions</td>
<?php endif; ?>
			</tr>
		</thead>
		<tbody>
<?php
$totalPages = ceil($totalPlayers / $pageSize);
if ($pageNum >= $totalPages) {
	$lastPage = TRUE;
	$nextPage = $pageNum;
} else {
	$lastPage = FALSE;
	$nextPage = $pageNum + 1;
}
if ($pageNum > 1) {
	$prevPage = $pageNum - 1;
} else {
	$prevPage = $pageNum;
}
?>
<?php foreach ($playerList as $player): ?>
			<tr id="playerRow<?=$player['competitorID']?>">
				<td class="table_cell"><?=$player['iwfID']?>
				</td>
				<td class="table_cell"><a
					href="<?=site_url('player/details').'/'.$player['competitorID']?>">
						<?=$player['firstName']?>
				</a></td>
				<td class="table_cell"><a
					href="<?=site_url('player/details').'/'.$player['competitorID']?>">
						<?=$player['lastName']?>
				</a></td>
				<td class="table_cell"><?=$player['rarName']?>
				</td>
				<td class="table_cell"><?=$player['numComps']?>
				</td>
				<td class="table_cell"><?=$player['bestPlace']?>
				</td>
				<td class="table_cell"><?=$player['worstPlace']?>
				</td>
				<td class="table_cell"><?=$player['numArmies']?>
				</td>
				<!--<td class="table_cell"></td>-->
<?php 	if (isset($this->session->userdata['permissions']['Update Player']) &&
		$this->session->userdata['permissions']['Update Player'] == 1): ?>
				<td>
					<span class="button">
						<!--<a href="javascript:editPlayerNextAction = 'listPlayers';editPlayerPopup(<?=$player['competitorID']?>);">-->
						<a href="<?=site_url('player/edit').'/'.$player['competitorID']?>">
							Update
						</a>
					</span>
				</td>
				<td>
					<span class="button">
						<!--<a href="javascript:deletePlayerThenShowPlayers(<?=$player['competitorID']?>);">-->
						<a href="<?=site_url('player/delete').'/'.$player['competitorID']?>">
							Delete
						</a>
					</span>
				</td>
<?php 	endif; ?>
				<td>
					<span class="button">
						<a href="index.php?action=dolistresultsbyplayer&id=<?=$player['competitorID']?>">
							Results
						</a>
					</span>
				</td>
				<td>
					<span class="button">
						<a href="index.php?action=dolistmatchesbyplayer&id=<?=$player['competitorID']?>">
							Matches
						</a>
					</span>
				</td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
	<table>
		<tr>
			<td>
<?php if ($pageNum > 1): ?>
				<span class="button">
					<a href="<?=site_url('player/playerList/0').'/'.$prevPage?>">
						Previous
					</a>
				</span> 
<?php else: ?>
				<span class="button disabled">
					<a href="">
						Previous
					</a>
				</span> 
<?php endif; ?></td>
<?php for($i = 1; $i <= $totalPages; $i++): ?>
			<td>
<?php 	if($i == $pageNum): ?>
				<span class="button disabled">
					<a href="">
						<?=$i?>
					</a>
<?php 	else: ?>
				<span class="button">
					<a href="<?=site_url('player/playerList/0').'/'.$i?>">
						<?=$i?>
					</a>
<?php 	endif; ?>
				</span>
			</td>
<?php endfor; ?>
			<td>
<?php if (!$lastPage): ?>
				<span class="button">
					<a href="<?=site_url('player/playerList/0').'/'.$nextPage?>">
						Next
					</a>
				</span> 
<?php else: ?>
				<span class="button disabled">
					<a href=""> 
						Next
					</a>
				</span> 
<?php endif; ?>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		// now we need to make the table sortable, since this is normally on page load and the table isn't present when the page is loaded
		sorttable.makeSortable(document.getElementById('allPlayerTable'));
	</script>

</div>

