<div class="inner_main container-fluid">
    <script>
        function login() {
            userid = document.forms[0].username.value;
            password = document.forms[0].password.value;
            errorMsg = "";
            if (userid == null || userid == "") {
                errorMsg += "User ID needs to be entered. /n";
            }
            if (password == null || password == "") {
                errorMsg += "Password needs to be entered./n";
            } else {
                passwordHash = md5(password);
                document.forms[0].password.value = passwordHash;
            }
            if (errorMsg != "") {
                alert(errorMsg);
            } else {
                //alert(userid + " " + passwordHash);
                document.forms[0].submit();
            }
        }
    </script>
	<form class="form" name="login_form" action="<?=site_url('login/submit');?>" method="post">
		<div class="form-group">
		    	<label>Username</label>
			<input class="form-control" name="username" type="text" id="username" size="10" />
	    	</div>
	    	<div class="form-group">
	    		<label>Password</label>
			<input class="form-control" name="password" type="password" id="password" size="10" />
		</div>
		<div class="form-group">
			<a class="btn btn-default" href="#" onClick="login(); return false;">Login</a>
		</div>
	</form>
	<div class="container-fluid">
		<?=$loginMessage?>
	</div>
</div>