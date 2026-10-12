<div id="container">
	<h1>
		<?=$playerData['first_name']?> <?=$playerData['last_name']?>
	</h1>
	<table class="table table-striped">
		<tr>
			<td class="table_cell">ID</td>
			<td class="table_cell"><?=$playerData['competitor_id']?></td>
		</tr>
		<tr>
			<td class="table_cell">IWF ID</td>
			<td class="table_cell"><?=$playerData['iwf_id']?></td>
		</tr>
		<tr>
			<td class="table_cell">First Name</td>
			<td class="table_cell"><?=$playerData['first_name']?></td>
		</tr>
		<tr>
			<td class="table_cell">Last Name</td>
			<td class="table_cell"><?=$playerData['last_name']?></td>
		</tr>
		<tr>
			<td class="table_cell">Region</td>
			<td class="table_cell"><?=$playerData['rar_name']?></td>
		</tr>
		<tr>
			<td class="table_cell">Creation Date</td>
			<td class="table_cell"><?=$playerData['cr_date']?></td>
		</tr>
	</table>
	<table class="table table-striped">
		<tr>
			<td colspan="2" class="table_cell">Armies used:</td>
		</tr>
	<?php if ($armyData): ?>
	<?php   foreach ($armyData as $eachArmy): ?>
		<tr>
			<td class="table_cell">
				<a href="<?=site_url('army/listMatches').'/'.$eachArmy->army_id?>">
					<?=$eachArmy->name?>
				</a>
			</td>
	<?php     if ($eachArmy->uses == 1): ?>
			<td class="table_cell"> used in <?=$eachArmy->uses?> competition</td>
	<?php     else: ?>
			<td class="table_cell"> used in <?=$eachArmy->uses?> competitions</td>
	<?php     endif; ?>
		</tr>
	<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="2" class="table_cell">No matches recorded</td>
		</tr>
	<?php endif; ?>
	</table>
</div>