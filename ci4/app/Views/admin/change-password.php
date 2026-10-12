<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<script src="<?= base_url('js/md5.min.js') ?>"></script>
<nav class="navbar navbar-default" aria-label="Administration">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li class="active"><a href="<?= site_url('admin/changePassword/' . $userId) ?>" aria-current="page">Change Password</a></li>
            <?php if (($permissions['Create User'] ?? 0) == 1): ?>
                <li><a href="<?= site_url('admin/addUser') ?>">Add a new user</a></li>
            <?php endif ?>
            <?php if (($permissions['User Menu'] ?? 0) == 1): ?>
                <li><a href="<?= site_url('admin/listUsers') ?>">User List</a></li>
            <?php endif ?>
            <?php if (($permissions['Run Glicko'] ?? 0) == 1): ?>
                <li><a href="<?= site_url('rankings/calc') ?>">Run Glicko</a></li>
            <?php endif ?>
        </ul>
    </div>
</nav>
<div class="container-fluid">
    <h2>Change Password</h2>
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <?php if ($message = session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc($message) ?></div>
    <?php endif ?>
    <form id="change-password-form" class="form-horizontal" action="<?= site_url('admin/updatePassword') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="oldPassword">Old Password</label>
            <div class="col-sm-9"><input class="form-control" name="oldPassword" id="oldPassword" type="password" required autocomplete="current-password"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="password1">New Password</label>
            <div class="col-sm-9"><input class="form-control" name="password1" id="password1" type="password" required autocomplete="new-password"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="password2">Repeat New Password</label>
            <div class="col-sm-9"><input class="form-control" name="password2" id="password2" type="password" required autocomplete="new-password"></div>
        </div>
        <button class="btn btn-success" type="submit">Change Password</button>
        <a class="btn btn-default" href="<?= site_url('admin') ?>">Cancel</a>
    </form>
</div>
<script>
    document.getElementById('change-password-form').addEventListener('submit', function (event) {
        const oldPassword = document.getElementById('oldPassword');
        const password = document.getElementById('password1');
        const confirmation = document.getElementById('password2');
        if (password.value !== confirmation.value) {
            event.preventDefault();
            alert('Passwords do not match');
            return;
        }

        oldPassword.value = md5(oldPassword.value);
        password.value = md5(password.value);
        confirmation.value = md5(confirmation.value);
    });
</script>
<?= $this->endSection() ?>
