<div class="container">
	<script type="text/javascript">
	<!--
	function validateInsertData() {
		if (document.getElementById('standInId').selectedIndex > 0
			&& document.getElementById('stoodInForId').selectedIndex > 0) {
			document.forms[0].submit();
		} else {
			alert('You must fill in all details in order to insert a stand-in');
		}
	}
	-->
	</script>
	<form class="form-horizontal" name="addresult" action="<?=site_url('competition/insertStandIn').'/'.$competitionId; ?>" method="post">
		<div class="form-group">
			<label class="col-sm-2 control-label" for="standInId">Stand In</label>
			<div class="col-sm-4">
<?php if (count($playerList) > 0): ?>
				<select class="form-control" name="standInId" id="standInId">
					<option value="-1">Select a player:</option>
<?php 	foreach ($playerList as $player): ?>
					<option value="<?=$player->competitor_id; ?>"><?=$player->first_name." ".$player->last_name; ?></option>
<?php 	endforeach; ?>
				</select>
<?php endif; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="stoodInFor">Stood In For</label>
			<div class="col-sm-4">
<?php if (count($compPlayerList) > 0): ?>
				<select class="form-control" name="stoodInForId" id="stoodInForId">
					<option value="-1">Select a player:</option>
<?php 	foreach ($compPlayerList as $player): ?>
					<option value="<?=$player->competitor_id; ?>"><?=$player->first_name." ".$player->last_name; ?></option>
<?php 	endforeach; ?>
				</select>
<?php endif; ?>
			</div>
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-success" href="javascript:validateInsertData();">Insert</a>
		</div>
	</form>
</div>

