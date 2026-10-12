<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<script src="<?= base_url('js/md5.min.js') ?>"></script>
<nav class="navbar navbar-default" aria-label="Administration">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li><a href="<?= site_url('admin/changePassword/' . $userId) ?>">Change Password</a></li>
            <?php if (($permissions['Create User'] ?? 0) == 1): ?>
                <li class="active"><a href="<?= site_url('admin/addUser') ?>" aria-current="page">Add a new user</a></li>
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
    <h2>Add User</h2>
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <form id="add-user-form" class="form-horizontal" action="<?= site_url('admin/insertUser') ?>" method="post">
        <?= csrf_field() ?>
        <div class="row">
            <section class="col-sm-6">
                <h3>Personal Details</h3>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="username">User Name</label>
                    <div class="col-sm-9"><input class="form-control" maxlength="60" name="username" id="username" value="<?= esc(old('username', ''), 'attr') ?>" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="firstname">First Name</label>
                    <div class="col-sm-9"><input class="form-control" maxlength="30" name="firstname" id="firstname" value="<?= esc(old('firstname', ''), 'attr') ?>" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="lastname">Last Name</label>
                    <div class="col-sm-9"><input class="form-control" maxlength="30" name="lastname" id="lastname" value="<?= esc(old('lastname', ''), 'attr') ?>" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="password1">Password</label>
                    <div class="col-sm-9"><input class="form-control" name="password1" id="password1" type="password" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="password2">Password (repeat)</label>
                    <div class="col-sm-9"><input class="form-control" name="password2" id="password2" type="password" required></div>
                </div>
                <h3>General Permissions</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead><tr><th>System Permission</th><th>No</th><th>Yes</th><th>Description</th></tr></thead>
                        <tbody>
                            <?php foreach ($userPermissions as $permission): ?>
                                <tr>
                                    <th scope="row"><?= esc($permission->name) ?></th>
                                    <?php foreach ([0, 1] as $value): ?>
                                        <td>
                                            <label class="sr-only" for="perm<?= (int) $permission->permission_id ?>-<?= $value ?>">
                                                <?= esc($permission->name) ?> <?= $value === 1 ? 'Yes' : 'No' ?>
                                            </label>
                                            <input id="perm<?= (int) $permission->permission_id ?>-<?= $value ?>"
                                                   name="perm<?= (int) $permission->permission_id ?>"
                                                   value="<?= $value ?>"
                                                   type="radio"
                                                   <?= (int) old('perm' . (int) $permission->permission_id, '0') === $value ? 'checked' : '' ?>>
                                        </td>
                                    <?php endforeach ?>
                                    <td><?= esc($permission->description) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="col-sm-6">
                <h3>Competition Series Permissions</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead><tr><th>Competition Series</th><th>No</th><th>Yes</th></tr></thead>
                        <tbody>
                            <?php foreach ($seriesPermissions as $series): ?>
                                <tr>
                                    <th scope="row"><?= esc((string) $series->id . ' ' . $series->name) ?></th>
                                    <?php foreach ([0, 1] as $value): ?>
                                        <td>
                                            <label class="sr-only" for="series<?= (int) $series->id ?>-<?= $value ?>">
                                                <?= esc($series->name) ?> <?= $value === 1 ? 'Yes' : 'No' ?>
                                            </label>
                                            <input id="series<?= (int) $series->id ?>-<?= $value ?>"
                                                   name="series<?= (int) $series->id ?>"
                                                   value="<?= $value ?>"
                                                   type="radio"
                                                   <?= (int) old('series' . (int) $series->id, '0') === $value ? 'checked' : '' ?>>
                                        </td>
                                    <?php endforeach ?>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <button class="btn btn-success" type="submit">Create</button>
        <a class="btn btn-danger" href="<?= site_url('admin/listUsers') ?>">Cancel</a>
    </form>
</div>
<script>
    document.getElementById('add-user-form').addEventListener('submit', function (event) {
        const password = document.getElementById('password1');
        const confirmation = document.getElementById('password2');
        if (password.value !== confirmation.value) {
            event.preventDefault();
            alert('Passwords do not match');
            return;
        }

        password.value = md5(password.value);
        confirmation.value = md5(confirmation.value);
    });
</script>
<?= $this->endSection() ?>
