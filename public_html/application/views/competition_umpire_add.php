<div class="inner_main">
	<script type="text/javascript">
	<!--
	function validateInsertData() {
		if (document.getElementById('competitorId').selectedIndex > 0) {
			document.forms[0].submit();
		} else {
			alert('You must fill in all details in order to insert an umpire');
		}
	}
	-->
	</script>
	<form name="addresult" action="<?=site_url('competition/insertUmpire').'/'.$competitionId; ?>" method="post">
	<table>
		<tr>
			<td class="table_cell">Umpire</td>
			<td class="table_cell">
<?php if (count($playerList) > 0): ?>
				<select name="competitorId" id="competitorId">
					<option value="-1">Select a player:</option>
<?php 	foreach ($playerList as $player): ?>
					<option value="<?=$player->competitor_id; ?>"><?=$player->first_name." ".$player->last_name; ?></option>
<?php 	endforeach; ?>
				</select>
<?php endif; ?>
			</td>
		</tr>
	<table class="button_table">
		<tr class="action_bar">
			<td class="buttons">
				<span class="button">
					<a href="javascript:validateInsertData();">Insert</a>
				</span>
			</td>
		</tr>
	</table>
</form>

