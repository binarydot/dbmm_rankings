<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-default" aria-label="Administration">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li><a href="<?= site_url('admin/changePassword/' . $userId) ?>">Change Password</a></li>
            <?php if (($permissions['Create User'] ?? 0) == 1): ?>
                <li><a href="<?= site_url('admin/addUser') ?>">Add a new user</a></li>
            <?php endif ?>
            <li><a href="<?= site_url('admin/listUsers') ?>">User List</a></li>
            <?php if (($permissions['Run Glicko'] ?? 0) == 1): ?>
                <li><a href="<?= site_url('rankings/calc') ?>">Run Glicko</a></li>
            <?php endif ?>
        </ul>
    </div>
</nav>
<div class="container-fluid">
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <?php if ($message = session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc($message) ?></div>
    <?php endif ?>
    <form class="form-horizontal" action="<?= site_url('admin/updateUser/' . (int) $user->user_id) ?>" method="post">
        <?= csrf_field() ?>
        <div class="row">
            <section class="col-md-6">
                <h2>Personal Details</h2>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="userid">ID</label>
                    <div class="col-sm-9"><input class="form-control" id="userid" value="<?= esc((string) $user->user_id) ?>" disabled></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="username">User Name</label>
                    <div class="col-sm-9"><input class="form-control" maxlength="60" id="username" name="username" value="<?= esc(old('username', $user->user_name), 'attr') ?>" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="firstname">First Name</label>
                    <div class="col-sm-9"><input class="form-control" maxlength="30" id="firstname" name="firstname" value="<?= esc(old('firstname', $user->first_name), 'attr') ?>" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="lastname">Last Name</label>
                    <div class="col-sm-9"><input class="form-control" maxlength="30" id="lastname" name="lastname" value="<?= esc(old('lastname', $user->last_name), 'attr') ?>" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="creationdate">Creation Date</label>
                    <div class="col-sm-9"><input class="form-control" id="creationdate" value="<?= esc($user->cr_date) ?>" disabled></div>
                </div>
                <input name="targetUserId" value="<?= (int) $user->user_id ?>" type="hidden">
                <h2>System Permissions</h2>
                <table class="table table-striped">
                    <thead><tr><th>Permission</th><th>No</th><th>Yes</th></tr></thead>
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
                                               <?= (int) old('perm' . (int) $permission->permission_id, (string) $permission->value) === $value ? 'checked' : '' ?>>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </section>
            <section class="col-md-6">
                <h2>Administer Competition Series</h2>
                <table class="table table-striped">
                    <thead><tr><th>Competition Series</th><th>No</th><th>Yes</th></tr></thead>
                    <tbody>
                        <?php foreach ($seriesPermissions as $series): ?>
                            <tr>
                                <th scope="row"><?= esc($series->name) ?></th>
                                <?php foreach ([0, 1] as $value): ?>
                                    <td>
                                        <label class="sr-only" for="series<?= (int) $series->id ?>-<?= $value ?>">
                                            <?= esc($series->name) ?> <?= $value === 1 ? 'Yes' : 'No' ?>
                                        </label>
                                        <input id="series<?= (int) $series->id ?>-<?= $value ?>"
                                               name="series<?= (int) $series->id ?>"
                                               value="<?= $value ?>"
                                               type="radio"
                                               <?= (int) old('series' . (int) $series->id, (string) $series->value) === $value ? 'checked' : '' ?>>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </section>
        </div>
        <div class="btn-group">
            <button class="btn btn-primary" type="submit">Update</button>
            <a class="btn btn-primary" href="<?= site_url('admin/setPassword/' . (int) $user->user_id) ?>">Set Password</a>
            <a class="btn btn-danger" href="<?= site_url('admin/listUsers') ?>">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
