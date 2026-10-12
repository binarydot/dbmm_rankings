<div class="container-fluid">
	<p style="font-size:medium;">Welcome <?=$firstName ?> to the DBMM Rankings website administration page.</p>
	<div class="row">
		<div class="col-sm-6">
			<div>You have the following global permissions:</div>
			<table class="table-striped">
			<?php foreach ($permissions as $key => $permission): ?>
				<tr>
					<td>
						<?=$key ?>
					</td>
				</tr>
			<?php endforeach; ?>	
			</table>
		</div>
		<div class="col-md-6">
			<div>You can administer the following competition series:</div>
			<table class="table-striped">
			<?php foreach ($seriesPermissions as $seriesPermission): ?>
				<tr>
					<td>
						<?=$seriesPermission->competition_series_name ?>
					</td>
				</tr>
			<?php endforeach; ?>	
			</table>
		</div>
	</div>
</div>