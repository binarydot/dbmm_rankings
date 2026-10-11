<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
			<form class="navbar-form" name="listglicko" action="<?=site_url("rankings/glicko")?>" method="post">
			<span>
				<?=lang("blurbs_Glicko1")?>:
				<select class="form-control" name="reliability" id="reliability" onChange='form.submit();'>
<?php
	$relArray = array("A","B","C","D","E","All");
	foreach ($relArray as $relName) {
		if ($rankingsData["reliabilityUsed"] == $relName) {
			echo "<option value=\"".$relName."\" selected>".$relName."</option>";
		} else {
			echo "<option value=\"".$relName."\">".$relName."</option>";
		}
	}
?>
				</select>
			</span>
			<span>
				<?=lang("title_Country_Region")?>:
				<select class="form-control" name="rar" id="rar" onChange='form.submit();'>
<?	if ($rankingsData["rarUsed"] == 0): ?>
					<option value="" selected><?=lang("title_All")?></option>
<?	else: ?>
					<option value=""><?=lang("title_All")?></option>
<?	endif; ?>
<?	foreach ($rars as $rarinst): ?>
<?		if ($rankingsData["rarUsed"] == $rarinst->rar_id): ?>
					<option value="<?=$rarinst->rar_id?>" selected><?=$rarinst->rar_name?></option>
<?		else: ?>
					<option value="<?=$rarinst->rar_id?>"><?=$rarinst->rar_name?></option>
<?		endif; ?>
<?	endforeach; ?>
				</select>
			</span>
			</form>
		</div>
	</div>	
</nav>
<div id="main" class="container-fluid">
	<div>
		<table class="action_table">
			<thead>
				<tr class="table_header_row">
					<td class="table_header">#</td>
					<td class="table_header"><?=lang("title_Player")?></td>
					<td class="table_header"><?=lang("title_Region")?></td>
					<!--<td class="table_header_center" colspan="1"><?=lang("title_Rating")?></td>-->
<?php if ($this->session->userdata('See All Glicko') == TRUE): ?>
					<td class="table_header_center" colspan="2"><?=lang("title_Reliability")?></td>
<?php else: ?>
					<td class="table_header_center" colspan="1"><?=lang("title_Reliability")?></td>
<?php endif; ?>
					<td class="table_header"><?=lang("title_Rating Graph")?></td>
				</tr>
			</thead>
			<tbody>
<?php foreach ($rankingsData["glickoRankings"] as $ranking): ?>
				<tr>
					<td class="table_cell"><?=$ranking["rank"];?></td>
					<td class="table_cell">
						<a href="<?=site_url("player/glicko")."/".$ranking["playerId"];?>">
							<?=$ranking["playerName"]?>
						</a>
					</td>
					<td class="table_cell">
						<?=getImg($ranking['rarName'])?>
						<?=$ranking["rarName"];?>
					</td>
<!--					<td class="table_cell_right"><?=$ranking["ranking"];?></td>   -->
					<td class="table_cell"><!--<?=$ranking["reliabilityName"];?>-->
	<?php if($ranking["reliabilityName"]=="AAAAA"): ?>
						<span style="color:MidnightBlue;"><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i></span>
	<?php elseif($ranking["reliabilityName"] == "BBBB"): ?>
						<span style="color:Blue;"><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i></span>
	<?php elseif($ranking["reliabilityName"] == "CCC"): ?>
						<span style="color:CornflowerBlue;"><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i></span>
	<?php elseif($ranking["reliabilityName"] == "DD"): ?>
						<span style="color:LightSkyBlue;"><i class="fa fa-star fa-lg"></i><i class="fa fa-star fa-lg"></i></span>
	<?php elseif($ranking["reliabilityName"] == "E"): ?>
						<span style="color:PowderBlue;"><i class="fa fa-star fa-lg"></i></span>
	<?php endif; ?>
	
					</td>
	<?php if ($this->session->userdata('See All Glicko') == TRUE): ?>
					<td class="table_cell_right">
						<?php echo number_format($ranking["reliability"],2); ?>
					</td>
	<?php endif; ?>
					<td class="table_cell">
						<!--
						<img src="<?=base_url()?>images/blue_bar.gif"
							width="<?php echo (($ranking['ranking']-1600)/800*400);?>px"
							height="16px"
						/>	
						-->
						<span style="
							width: <?php echo (($ranking["ranking"]-1600)/800*400); ?>px; 
							height: 20px; 
							border: 1px solid red; 
							background-color: red;
							color: white;
							display: inline-block;
							padding: 1px;
							margin: 1px;
						">
						<?=$ranking["ranking"];?>
						</span>
						
					</td>
				</tr>
<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div>
		<table>
			<tr>
				<th><?=lang("title_Category")?></th>
				<th><?=lang("title_Reliability")?></th>
			</tr>
			<tr>
				<td>A</td>
				<td>&lt;<?=$rankingsData["reliabilityThresholdList"]->reliability_a;?><td>
			</tr>
			<tr>
				<td>B</td>
				<td>&lt;<?=$rankingsData["reliabilityThresholdList"]->reliability_b;?><td>
			</tr>
			<tr>
				<td>C</td>
				<td>&lt;<?=$rankingsData["reliabilityThresholdList"]->reliability_c;?><td>
			</tr>
			<tr>
				<td>D</td>
				<td>&lt;<?=$rankingsData["reliabilityThresholdList"]->reliability_d;?><td>
			</tr>
			<tr>
				<td>E</td>
				<td>&lt;<?=$rankingsData["reliabilityThresholdList"]->reliability_e;?><td>
			</tr>
		</table>
	</div>
</div>
