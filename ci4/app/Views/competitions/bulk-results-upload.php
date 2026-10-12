<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><a href="<?= site_url('competition') ?>"><?= esc($competition['competition_series_name']) ?></a></h1>
    <h2><a href="<?= site_url('competition/results/' . (int) $competition['competition_id']) ?>"><?= esc($competition['competition_name']) ?></a> - Add All Results</h2>
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <p class="alert alert-warning">Uploading a results file will replace all existing results, runners, and matches for this competition after you review and confirm the preview.</p>
    <p>Upload a BHGS Web CSV with no header row. Each row contains position, file player ID, player name, one opponent column per round, one match-score column per round, overall score, and army name. Use X when a player did not play and B for a bye. Bulk results currently support singles competitions.</p>
    <form action="<?= site_url('competition/validateAllResults/' . (int) $competition['competition_id']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="csvfile">Results CSV (maximum 100 KB)</label>
            <input class="form-control" type="file" name="csvfile" id="csvfile" accept=".csv,text/csv" required>
        </div>
        <a class="btn btn-default" href="<?= site_url('competition/results/' . (int) $competition['competition_id']) ?>">Cancel</a>
        <button class="btn btn-primary" type="submit">Validate CSV</button>
    </form>
</div>
<?= $this->endSection() ?>
