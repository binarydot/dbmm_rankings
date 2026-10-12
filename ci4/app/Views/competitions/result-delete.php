<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($competition['competition_series_name']) ?></h1>
    <h2><?= esc($competition['competition_name']) ?> - Delete Result</h2>
    <p>Are you sure you want to delete this result? Paired team records are removed together.</p>
    <form action="<?= site_url('competition/deleteResult/' . (int) $competition['competition_id'] . '/' . (int) $result['position_id']) ?>" method="post">
        <?= csrf_field() ?>
        <a class="btn btn-default" href="<?= site_url('competition/results/' . (int) $competition['competition_id']) ?>">Cancel</a>
        <button class="btn btn-danger" type="submit">Delete Result</button>
    </form>
</div>
<?= $this->endSection() ?>
