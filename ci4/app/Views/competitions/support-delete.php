<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($competition['competition_series_name']) ?></h1>
    <h2><?= esc($competition['competition_name']) ?> - <?= esc($title) ?></h2>
    <p>Are you sure you want to remove <?= esc($display) ?>?</p>
    <form action="<?= esc($action, 'attr') ?>" method="post">
        <?= csrf_field() ?>
        <a class="btn btn-default" href="<?= site_url('competition/results/' . (int) $competition['competition_id']) ?>">Cancel</a>
        <button class="btn btn-danger" type="submit">Delete</button>
    </form>
</div>
<?= $this->endSection() ?>
