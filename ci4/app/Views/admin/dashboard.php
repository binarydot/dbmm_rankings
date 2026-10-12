<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-default" aria-label="Administration">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li><a href="<?= site_url('admin/changePassword/' . $userId) ?>">Change Password</a></li>
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
    <?php if ($message = session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc($message) ?></div>
    <?php endif ?>
    <p class="lead">Welcome <?= esc($firstName) ?> to the DBMM Rankings website administration page.</p>
    <div class="row">
        <section class="col-sm-6" aria-labelledby="global-permissions">
            <h2 id="global-permissions">Global permissions</h2>
            <ul class="list-unstyled">
                <?php foreach (array_keys($permissions) as $permission): ?>
                    <li><?= esc((string) $permission) ?></li>
                <?php endforeach ?>
            </ul>
        </section>
        <section class="col-md-6" aria-labelledby="series-permissions">
            <h2 id="series-permissions">Competition series you can administer</h2>
            <ul class="list-unstyled">
                <?php foreach ($seriesPermissions as $seriesPermission): ?>
                    <li><?= esc($seriesPermission->competition_series_name) ?></li>
                <?php endforeach ?>
            </ul>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
