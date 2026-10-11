<div id="container">
	<h1>
		Add Player
	</h1>
	<script type="text/javascript">
	<!--
	function validateInsertData() {
		if (document.getElementById('rar').selectedIndex > 0
			&& document.getElementById('first_name').value != ""
			&& document.getElementById('last_name').value != "") {
			document.forms[0].submit();
		} else {
			alert('You must fill in all details in order to insert a result');
		}
	}
	-->
	</script>
	<form action="<?=site_url("player/insert")?>" method="post"
		name="player_add" class="form-horizontal">
		<span><?=$errorMessage?></span>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="iwf_id">IWF ID</label>
			<div class="col-sm-4">
				<input class="form-control" id="iwf_id" name="iwf_id" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="first_name">First Name</label>
			<div class="col-sm-4">
				<input class="form-control" value="" id="first_name" name="first_name" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="last_name">Last Name</label>
			<div class="col-sm-4">
				<input class="form-control" value="" id="last_name" name="last_name" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="rar">Region</label>
			<div class="col-sm-4">
				<select class="form-control" id="rar" name="rar">
					<option>Select a region...</option>
						<?php foreach ($rars as $rarinst): ?>
					<option value="<?=$rarinst->rar_id?>">
						<?=$rarinst->rar_name?>
					</option>
						<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="anonymous">Anonymous</label>
			<div class="col-sm-4">
				<select class="form-control" id="anonymous" name="anonymous">
					<option value="0" selected>No</option>
					<option value="1">Yes</option>
				</select>
			</div>
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-danger" href="<?=site_url('player/playerList')?>">
				Cancel
			</a>
			<a class="btn btn-success" href="javascript:validateInsertData();">
				Save
			</a>
		</div>
	</form>
</div>
