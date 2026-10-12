<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-default" aria-label="Administration">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li><a href="<?= site_url('admin/changePassword/' . $userId) ?>">Change Password</a></li>
            <?php if (($permissions['Create User'] ?? 0) == 1): ?>
                <li><a href="<?= site_url('admin/addUser') ?>">Add a new user</a></li>
            <?php endif ?>
            <li class="active"><a href="<?= site_url('admin/listUsers') ?>" aria-current="page">User List</a></li>
            <?php if (($permissions['Run Glicko'] ?? 0) == 1): ?>
                <li><a href="<?= site_url('rankings/calc') ?>">Run Glicko</a></li>
            <?php endif ?>
        </ul>
    </div>
</nav>
<div class="container-fluid">
    <h2>User List</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Created</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= esc((string) $user->user_id) ?></td>
                        <td><?= esc($user->first_name) ?></td>
                        <td><?= esc($user->last_name) ?></td>
                        <td><?= esc($user->cr_date) ?></td>
                        <td><a class="btn btn-default" href="<?= site_url('admin/userDetails/' . (int) $user->user_id) ?>">View &amp; Edit</a></td>
                    </tr>
                <?php endforeach ?>
                <?php if ($users === []): ?>
                    <tr><td colspan="5">No users found.</td></tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
