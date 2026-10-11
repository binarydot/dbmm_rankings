<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li>
					<a href="<?=site_url('admin/changePassword').'/'.$userId ?>">
						Change Password
					</a>
				</li>
<?php if (array_key_exists('permissions',$legacySession->userdata)): ?>
<?php if (array_key_exists('Create User',$legacySession->userdata['permissions']) && 
            $legacySession->userdata['permissions']['Create User'] == 1): ?>
				<li>
					<a href="<?=site_url('admin/addUser')?>">
						Add a new user
					</a>
				</li>
<?php endif; ?>
<?php if (array_key_exists('User Menu',$legacySession->userdata['permissions']) && 
            $legacySession->userdata['permissions']['User Menu'] == 1): ?>
				<li>
					<a href="<?=site_url('admin/listUsers')?>">
						User List
					</a>
				</li>
<?php endif; ?>
<?php if (array_key_exists('Run Glicko',$legacySession->userdata['permissions']) && 
            $legacySession->userdata['permissions']['Run Glicko'] == 1): ?>
				<li>
					<a href="<?=site_url('glickocli/calc')?>">
						Run Glicko
					</a>
				</li>
<?php endif; ?>
<?php endif; ?>
			</ul>
		</div>
	</div>
</nav>