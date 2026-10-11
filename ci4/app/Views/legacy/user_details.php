<div class="container-fluid">
<?php  		if (isset($legacySession->userdata['permissions']['User Menu'])
				&& $legacySession->userdata['permissions']['User Menu'] == 1): ?>
	<form name="updateuser" class="form-horizontal" 
		action="<?=site_url("admin/updateUser")."/".$userDetails->user_id ?>" method="post">
<?php  endif; ?>
		<div class="row">
			<div class="col-md-6">
				<h2>Personal Details</h2>
				<div class="form-group">
						<label for="userid">ID</label>
						<input type="text" class="form-control" id="userid" disabled value="<?=$userDetails->user_id?>" />
				</div>
				<div class="form-group">
						<label for="username">User Name</label>
						<input type="text" class="form-control" id="username" value="<?=$userDetails->user_name?>" />
				</div>
				<div class="form-group">
						<label for="firstname">First Name</label>
						<input type="text" class="form-control" id="firstname" value="<?=$userDetails->first_name?>" />
				</div>
				<div class="form-group">
						<label for="lastname">Last Name</label>
						<input type="text" class="form-control" id="lastname" value="<?=$userDetails->last_name?>" />
				</div>
				<div class="form-group">
						<label for="creationdate">Creation Date</label>
						<input type="text" class="form-control" id="creationdate" disabled value="<?=$userDetails->cr_date?>" />
				</div>
				<div><input name="id" id="id" value="<?=$userId; ?>" type="hidden"></div>
				<h2>System Permission</h2>
				<table class="table table-striped">
					<tr>
						<th>Permission</th>
						<th>No</th>
						<th>Yes</th>
					</tr>
		<?php  foreach ($userPerms as $row): ?>
					<tr>
						<td>
							<label for="perm<?=$row->permission_id?>"><?=$row->name?></label>
						</td>
						<td>
		<?php    if ($row->value == 0): ?>
							<input class="form-control" name="perm<?=$row->permission_id?>" value="0" checked type="radio">
						</td>
						<td>
							<input class="form-control" name="perm<?=$row->permission_id?>" value="1" type="radio">
		<?php    else: ?>
							<input class="form-control" name="perm<?=$row->permission_id?>" value="0" type="radio">
						</td>
						<td>
							<input class="form-control" name="perm<?=$row->permission_id?>" value="1" checked type="radio">
		<?php  endif; ?>
						</td>
					</tr>
		<?php  endforeach; ?>
				</table>
			</div>
			<div class="col-md-6">		
				<h2>Administer Competition Series</h2>
				<table class="table table-striped">
					<tr>
						<th>Competition Series</th>
						<th>No</th>
						<th>Yes</th>
					</tr>
<?php  foreach ($seriesPerms as $row): ?>
					<tr>
						<td>
							<label><?=$row->id?></label>
							<label><?=$row->name?></label>
						</td>
						<td>
<?php    if ($row->value == 0): ?>
							<input class="form-control" name="series<?=$row->id?>" value="0" checked type="radio">
						</td>
						<td>
							<input class="form-control" name="series<?=$row->id?>" value="1" type="radio">
<?php    else: ?>
							<input class="form-control" name="series<?=$row->id?>" value="0" type="radio">
						</td>
						<td>
							<input class="form-control" name="series<?=$row->id?>" value="1" checked type="radio">
<?php  endif; ?>
						</td>
					</tr>
<?php  endforeach; ?>
				</table>
			</div>
		</div>
		<div class="btn-group btn-group-justified">
						<a class="btn btn-primary" role="button" href="javascript:document.forms[0].submit();">Update</a>
						<a class="btn btn-primary" role="button" href="<?=site_url("admin/setPassword")."/".$userDetails->user_id?>">Set Password</a>
						<a class="btn btn-danger" role="button" href="<?=site_url("admin/listUsers")?>">Cancel</a>
		</div>
	</form>
</div>

