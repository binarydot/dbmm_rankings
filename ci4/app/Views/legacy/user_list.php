<div class="container">
	<table class="table table-striped">
		<tr class="">
			<th class="">ID</td>
			<th class="">First Name</td>
			<th class="">Last Name</td>
			<th class="">Created</td>
			<th class=""></td>
		</tr>
<?php  foreach($users AS $user): ?>
		<tr>
			<td class="table_cell"><?=$user->user_id?></td>
			<td class="table_cell"><?=$user->first_name?></td>
			<td class="table_cell"><?=$user->last_name?></td>
			<td class="table_cell"><?=$user->cr_date?></td>
			<td>
					<a class="btn btn-default" role="button" href="<?=site_url("admin/userDetails")."/".$user->user_id?>">
						View & Edit
					</a>
			</td>
		</tr>
<?php  endforeach; ?>
	</table>
</div>

