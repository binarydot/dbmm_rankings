<div class="container-fluid">
	<script language="JavaScript">
	<!--
	function doSubmit() {
        userid = document.forms[0].username.value;
        oldPassword = document.forms[0].oldpassword.value;
        password1 = document.forms[0].password1.value;
        password2 = document.forms[0].password2.value;
        errorMsg = "";
        if (userid == null || userid == "") {
            errorMsg += "User ID needs to be entered. /n";
        }
        if (oldPassword == null || oldPassword == "") {
            errorMsg += "Old password needs to be entered./n";
        } else {
            oldPasswordHash = md5(oldPassword);
        }
        if (password1 == null || password1 == "") {
            errorMsg += "Password needs to be entered./n";
        } else {
            password1Hash = md5(password1);
        }
        if (password2 == null || password2 == "") {
            errorMsg += "Password confirmation needs to be entered./n";
        } else {
            password2Hash = md5(password2);
        }
		if (password1 != password2) {
			errorMsg += "Passwords do not match./n";
		}
        if (errorMsg != "") {
            alert(errorMsg);
        } else {
            //alert(userid + " " + passwordHash);
            document.forms[0].oldpassword.value = oldPasswordHash;
            document.forms[0].password1.value = password1Hash;
            document.forms[0].password2.value = password2Hash;
            document.forms[0].submit();
        }
			return;
		}
        document.forms[0].password.value = password1Hash;
		document.forms[0].submit();
	}
	-->
	</script>
	<form class="form" name="editpassword" action="<?=site_url("admin/updatePassword")?>" method="post">
		<input name="id" id="id" value="<?=$id?>" type="hidden">
		<div class="form-group">
			<label for="oldpassword">Old Password</label>
			<input class="form-control" name="oldpassword" id="oldpassword" value="" type="password">
		</div>
		<div class="form-group">
			<label for="password1">Password</label>
			<input class="form-control" name="password1" id="password1" value="" type="password">
		</div>
		<div class="form-group">
			<label for="password2">Password (repeat)</label>
			<input class="form-control" name="password2" id="password2" value="" type="password">
		</div>
		<div class="btn-group btn-group-justified">
			<a class="btn btn-danger" role="button" href="<?=site_url("admin/userDetails")."/".$id?>">Cancel</a>
			<a class="btn btn-success" role="button" href="javascript:doSubmit();">Set</a>
		</div>
	</form>
</div>

