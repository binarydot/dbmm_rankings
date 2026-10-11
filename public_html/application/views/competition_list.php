<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
	<form class="navbar-form" id="listComps" name="listcomps" action="<?=site_url("competition/competitionList")?>" method="post">
		<script type="text/javascript">
			function filterComps(changer) {
				if (changer == "rar") {
					document.getElementById('series').value = 0;
				}
				if (changer == "series") {
					document.getElementById('year').value = 0;
					document.getElementById('rar').value = 0;
				}
				if (changer == "year") {
					document.getElementById('series').value = 0;
				}
				document.forms["listComps"].submit();
			}
		</script>
			<?=lang("title_Country_Region")?>: 
			<select class="form-control" name="rar" id="rar" onChange='filterComps("rar");'>
<?php if ($rar == 0): ?>
				<option value="0" selected><?=lang("title_All")?></option>
<?php else: ?>
				<option value="0"><?=lang("title_All")?></option>
<?php endif; ?>
<?php foreach ($rars as $rarinst): ?>
<?php 	if ($rar == $rarinst->rar_id): ?>
				<option value="<?=$rarinst->rar_id?>" selected><?=$rarinst->rar_name?></option>
<?php 	else: ?>
				<option value="<?=$rarinst->rar_id?>"><?=$rarinst->rar_name?></option>
<?php 	endif; ?>
<?php endforeach; ?>
			</select>
			<?=lang("title_Competition_Series")?>:
			<select class="form-control" name="series" id="series" onChange='filterComps("series");'>
<?php if ($series == 0): ?>
				<option value="0" selected><?=lang("title_All")?></option>
<?php else: ?>
				<option value="0"><?=lang("title_All")?></option>
<?php endif; ?>
<?php foreach ($competitionSeries as $seriesInst): ?>
<?php 	if ($series == $seriesInst->id): ?>
				<option value="<?=$seriesInst->id?>" selected><?=$seriesInst->name?></option>
<?php 	else: ?>
				<option value="<?=$seriesInst->id?>"><?=$seriesInst->name?></option>
<?php 	endif; ?>
<?php endforeach; ?>
			</select>
			<?=lang("title_Year")?>:
			<select class="form-control" name="year" id="year" onChange='filterComps("year");'>
<?php if ($year == -1): ?>
				<option value="-1" selected><?=lang("title_Current")?></option>
<?php else: ?>
				<option value="-1"><?=lang("title_Current")?></option>
<?php endif; ?>
<?php if ($year == 0): ?>
				<option value="0" selected><?=lang("title_All")?></option>
<?php else: ?>
				<option value="0"><?=lang("title_All")?></option>
<?php endif; ?>
<?php if ($year == -2): 
	$year = date("Y"); 
endif;
for ($yearOpt = 2007; $yearOpt <= date("Y"); $yearOpt++):
	if ($year == $yearOpt):
		?>
				<option value="<?=$yearOpt?>" selected><?=$yearOpt?></option>
<?php 	else: ?>
				<option value="<?=$yearOpt?>"><?=$yearOpt?></option>
<?php 	endif;
endfor;
?>
			</select>
<?php if (isset($this->session->userdata['permissions']['Add Competition'])
		&& $this->session->userdata['permissions']['Add Competition'] == 1): ?>
			<span class="">
				<a class="btn btn-default btn-navbar" role="button" href="<?=site_url("competition/add")?>">
					Add Competition
				</a>
			</span>
<?php endif; ?>
	</form>
		</div>
	</div>
</div>
<div class="container-fluid">	
	<table class="table table-striped table-condensed">
		<thead>
			<tr class="">
				<th class=""><?=lang("title_Name")?></th>
				<th class=""><?=lang("title_Date")?></th>
				<th class=""><?=lang("title_Wht")?></th>
				<th class="">Teams</th>
				<th class=""><?=lang("title_Plyrs")?></th>
				<th class=""><?=lang("title_Region")?></th>
<?php if (isset($this->session->userdata['permissions']['Update Competition']) &&
		$this->session->userdata['permissions']['Update Competition'] == 1): ?>
				<th class="" colspan="4"><?=lang("title_Actions")?></th>
<?php else: ?>
				<th class="" colspan="2"><?=lang("title_Actions")?></th>
<?php endif; ?>
<?php if ($this->session->userdata('LoggedIn')): ?>
				<th class=""><?=lang("title_Notes")?></th>
<?php endif; ?>
			</tr>
		</thead>
		<tbody>
<?php if(count ($competitions) > 0): ?>
<?php 	foreach($competitions AS $row ): ?>
			<tr>
				<td class="">
					<a href="<?=site_url('competition/details').'/'.$row['competition_id']?>">
						<?=$row['competition_name']?>
					</a>
				</td>
				<!-- <td class=""><?=$row['competition_series_name']?></td> -->
				<td class=""><?=$row['competition_date']?></td>
				<!-- <td class=""><?=$row['type']?></td>-->
				<td class="text-right"><?=$row['weighting']?></td>
				<!-- <td class="text-center"><?=$row['protectedname']?></td>-->
				<td class="text-right"><?=$row['entrants']?></td>
				<td class="text-right"><?=$row['players']?></td>
				<td class=""><?=$row['rar_name']?></td>
					<td>
						<span class="">
							<a class="btn btn-default" href="<?=site_url("competition/results")."/".$row['competition_id']?>">
<?php 		if ($row['runners']): ?>
									<?=lang("title_Runners")?>
<?php 		else: ?>
									<i class="fa fa-trophy fa-lg"></i> <?=lang("title_Results")?>
<?php 		endif; ?>
							</a>
						</span>
					</td>
					<td>
						<span class="">
							<a class="btn btn-default" href="<?=site_url("competition/matches")."/".$row['competition_id']?>">
								<i class="fa fa-handshake-o fa-lg"></i> <?=lang("title_Matches")?>
							</a>
						</span>
					</td>
<?php 		if (isset($this->session->userdata['permissions']['Update Competition']) &&
				$this->session->userdata['permissions']['Update Competition'] == 1) : ?>
<?php 			if (isset($this->session->userdata['seriesPermissions'][$row['series_id']])) : ?>
					<td>
						<span class="">
							<a class="btn btn-default" href="<?=site_url("competition/edit")."/".$row['competition_id']?>">
								<i class="fa fa-pencil-square-o fa-lg"></i> <?=lang("title_Update")?>
							</a>
						</span>
					</td>
<?php 			else: ?>
					<td>
						<span class="">
							<a class="btn btn-default disabled" href="#">
								<i class="fa fa-pencil-square-o fa-lg"></i> <?=lang("title_Update")?>
							</a>
						</span>
					</td>
<?php 			endif; ?>
<?php 		endif; ?>
					<?php 		if (isset($this->session->userdata['permissions']['Update Competition']) &&
							$this->session->userdata['permissions']['Update Competition'] == 1): ?>
<?php 			if (isset($this->session->userdata['seriesPermissions'][$row['series_id']])): ?>
					<td>
						<span class="">
							<a class="btn btn-default btn-danger" href="<?=site_url("competition/delete")."/".$row['competition_id']?>">
								<i class="fa fa-trash-o fa-lg"></i> <?=lang("title_Delete")?>
							</a>
						</span>
					</td>
<?php 			else: ?>
					<td>
						<span class="">
							<a class="btn btn-default btn-danger disabled">
								<i class="fa fa-trash-o fa-lg"></i> <?=lang("title_Delete")?>
							</a>
						</span>
					</td>
<?php 			endif; ?>
<?php 		endif; ?>
<?php 		if ($this->session->userdata('LoggedIn')): ?>
					<td class="table_cell error">".$notes."</td>
<?php 		endif; ?>
		</tr>
<?php 	endforeach; ?>
<?php else: ?>
		<tr>
			<td class="table_cell" colspan=9><?=lang("blurbs_Competition1")?></td>
		</tr>
<?php endif; ?>
	</tbody>
	</table>
</div>