<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<script src="<?= base_url('js/md5.min.js') ?>"></script>
<div class="inner_main container-fluid">
    <form class="form" name="login_form" action="<?= site_url('login') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control" name="username" type="text" id="username" size="10" maxlength="60" value="<?= esc(old('username', ''), 'attr') ?>" required autocomplete="username">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" name="password" type="password" id="password" size="10" required autocomplete="current-password">
        </div>
        <div class="form-group">
            <button class="btn btn-default" type="submit">Login</button>
        </div>
    </form>
    <div class="container-fluid"><?= esc($loginMessage) ?></div>
</div>
<script>
    document.forms.login_form.addEventListener('submit', function () {
        const password = this.elements.password;
        password.value = md5(password.value);
    });
</script>
<?= $this->endSection() ?>
