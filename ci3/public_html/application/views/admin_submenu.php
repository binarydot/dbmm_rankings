<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li>
					<a href="<?=site_url('admin/changePassword').'/'.$userId ?>">
						Change Password
					</a>
				</li>
<?php if (array_key_exists('permissions',$this->session->userdata)): ?>
<?php if (array_key_exists('Create User',$this->session->userdata['permissions']) && 
            $this->session->userdata['permissions']['Create User'] == 1): ?>
				<li>
					<a href="<?=site_url('admin/addUser')?>">
						Add a new user
					</a>
				</li>
<?php endif; ?>
<?php if (array_key_exists('User Menu',$this->session->userdata['permissions']) && 
            $this->session->userdata['permissions']['User Menu'] == 1): ?>
				<li>
					<a href="<?=site_url('admin/listUsers')?>">
						User List
					</a>
				</li>
<?php endif; ?>
<?php if (array_key_exists('Run Glicko',$this->session->userdata['permissions']) && 
            $this->session->userdata['permissions']['Run Glicko'] == 1): ?>
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