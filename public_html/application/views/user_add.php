<div class="container">
	<script language="JavaScript">
	<!--
	function doSubmit() {
		if (document.getElementById('password1').value != document.getElementById('password2').value) {
			alert("Passwords do not match");
			return;
		}
		document.forms[0].submit();
	}
	-->
	</script>
<? 		if (isset($this->session->userdata['permissions']['User Menu'])
				&& $this->session->userdata['permissions']['User Menu'] == 1): ?>
    <script>
        function addUser() {
            username = document.forms[0].username.value;
            firstname = document.forms[0].firstname.value;
            lastname = document.forms[0].lastname.value;
            password1 = document.forms[0].password1.value;
            password2 = document.forms[0].password2.value;
            errorMsg = "";
            if (username == null || username == "") {
                errorMsg += "Username needs to be entered. /n";
            }
            if (firstname == null || firstname == "") {
                errorMsg += "First Name needs to be entered. /n";
            }
            if (lastname == null || lastname == "") {
                errorMsg += "Last Name needs to be entered. /n";
            }
            if (password1 == null || password1 == "") {
                errorMsg += "Password needs to be entered./n";
            } else {
                password1Hash = md5(password1);
                document.forms[0].password1.value = passwordHash1;
            }
            if (password2 == null || password2 == "") {
                errorMsg += "Password confirmation needs to be entered./n";
            } else {
                password2Hash = md5(password2);
                document.forms[0].password2.value = passwordHash2;
            }
            if (password1 != password2) {
                errorMsg += "Passwords do not match./n";
            if (errorMsg != "") {
                alert(errorMsg);
            } else {
                //alert(userid + " " + passwordHash);
                document.forms[0].submit();
            }
        }
    </script>
	<form name="updateuser" class="form-horizontal" 
		action="<?=site_url("admin/insertUser") ?>" method="post">
<? endif; ?>
		<div class="row">
			<div class="col-sm-6">	
				<h2>Personal Details</h2>
				<div class="form-group">
					<label for="username">User Name</label>
					<input class="form-control" name="username" id="username" value="" />
				</div>
				<div class="form-group">
					<label for="firstname">First Name</label>
					<input class="form-control" name="firstname" id="firstname" value="" />
				</div>
				<div class="form-group">
					<label for="lastname">Last Name</label>
					<input class="form-control" name="lastname" id="lastname" value="" />
				</div>
				<div class="form-group">
					<label for="password1">Password</label>
					<input class="form-control" name="password1" id="password1" value="" type="password">
				</div>
				<div class="form-group">
					<label for="password2">Password (repeat)</label>
					<input class="form-control" name="password2" id="password2" value="" type="password">
				</div>
				<h2>General Permissions</h2>
				<table class="table table-striped">
					<tr>
						<th>System Permission</th>
						<th>No</th>
						<th>Yes</th>
						<th>Description</th>
					</tr>
<? foreach ($userPerms as $row): ?>
					<tr>
						<td class=""><?=$row->name?></td>
						<td class="">
							<input class="form-control" id="perm<?=$row->permission_id?>" value="0" checked type="radio">
						</td>
						<td class="">
							<input name="perm<?=$row->permission_id?>" id="perm<?=$row->permission_id?>" value="1" type="radio">
						</td>
						<td class=""><?=$row->description?></td>
					</tr>
<? endforeach; ?>
				</table>
			</div>
			<div class="col-sm-6">
				<h2>Competion Series Permissions</h2>
				<table class="table table-striped">
					<tr>
						<th colspan="2">Administer Competition Series</th>
						<th>No</th>
						<th>Yes</th>
					</tr>
<? foreach ($seriesPerms as $row): ?>
					<tr>
						<td class=""><?=$row->id?></td>
						<td class=""><?=$row->name?></td>
						<td class="">
							<input class="form-control" id="series<?=$row->id?>" value="0" checked type="radio">
						</td>
						<td class="">
							<input class="form-control" id="series<?=$row->id?>" value="1" type="radio">
						</td>
					</tr>
<? endforeach; ?>
				</table>
			</div>
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-danger" role="button" href="<?=site_url("admin/listUsers")?>">Cancel</a>
			<a class="btn btn-success" role="button" href="javascript:document.forms[0].submit();">Create</a>
		</div>
	</form>
</div>

