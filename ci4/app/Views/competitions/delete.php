<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h2>Delete Competition</h2>
    <p>Are you sure you want to delete <strong><?= esc($competition['competition_name']) ?></strong>?</p>
    <p>This action cannot be undone.</p>
    <form action="<?= site_url('competition/delete/' . (int) $competition['competition_id']) ?>" method="post">
        <?= csrf_field() ?>
        <a class="btn btn-default" href="<?= site_url('competition/details/' . (int) $competition['competition_id']) ?>">Cancel</a>
        <button class="btn btn-danger" type="submit">Delete Competition</button>
    </form>
</div>
<?= $this->endSection() ?>
