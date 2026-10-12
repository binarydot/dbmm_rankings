<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php $playerId = (int) $player['competitor_id']; ?>
<div class="container-fluid">
    <h2>Delete Player</h2>
    <p>
        Are you sure you want to delete
        <strong><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></strong>
        (ID <?= $playerId ?>)?
    </p>
    <p>This action cannot be undone. Players with competition, match, umpire, stand-in, or ranking history cannot be deleted.</p>
    <form action="<?= site_url('player/delete/' . $playerId) ?>" method="post">
        <?= csrf_field() ?>
        <button class="btn btn-danger" type="submit">Delete Player</button>
        <a class="btn btn-default" href="<?= site_url('player/details/' . $playerId) ?>">Cancel</a>
    </form>
</div>
<?= $this->endSection() ?>
