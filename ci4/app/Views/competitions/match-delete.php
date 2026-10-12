<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($competition['competition_series_name']) ?></h1>
    <h2><?= esc($competition['competition_name']) ?> - Delete Match</h2>
    <p>Are you sure you want to delete the match from round <?= (int) $match['round'] ?> (<?= esc($match['winner_score']) ?> - <?= esc($match['loser_score']) ?>)?</p>
    <form action="<?= site_url('competition/deleteMatch/' . (int) $competition['competition_id'] . '/' . (int) $match['match_id']) ?>" method="post">
        <?= csrf_field() ?>
        <a class="btn btn-default" href="<?= site_url('competition/matches/' . (int) $competition['competition_id']) ?>">Cancel</a>
        <button class="btn btn-danger" type="submit">Delete Match</button>
    </form>
</div>
<?= $this->endSection() ?>
