<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($competition['competition_series_name']) ?></h1>
    <h2><?= esc($competition['competition_name']) ?> - Validate Results</h2>
    <p class="alert alert-warning">Saving will replace all existing results, runners, and matches for this competition. Review every player and army before continuing.</p>
    <form action="<?= site_url('competition/insertAllResults/' . (int) $competition['competition_id']) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token, 'attr') ?>">
        <div class="table-responsive">
            <table class="table table-striped table-condensed">
                <thead><tr><th>Place</th><th>Name in CSV</th><th>Database player</th><th>Army in CSV</th><th>Database army</th><th>Score</th></tr></thead>
                <tbody>
                    <?php foreach ($rows as $index => $row): ?>
                        <?php $playerSelection = $selectedPlayers[$index] ?? 0; $armySelection = $selectedArmies[$index] ?? 0; ?>
                        <tr>
                            <td><?= (int) $row['position'] ?></td>
                            <td><?= esc($row['name']) ?></td>
                            <td>
                                <select class="form-control" name="p<?= (int) $index ?>" required>
                                    <option value="">Select player</option>
                                    <?php foreach ($competitors as $competitor): ?>
                                        <option value="<?= (int) $competitor['competitor_id'] ?>" <?= $playerSelection === (int) $competitor['competitor_id'] ? 'selected' : '' ?>><?= esc($competitor['first_name'] . ' ' . $competitor['last_name'] . ' (' . $competitor['rar_name'] . ')') ?></option>
                                    <?php endforeach ?>
                                </select>
                                <?php if (count($row['playerOptions']) !== 1): ?><small class="text-danger"><?= count($row['playerOptions']) === 0 ? 'No exact player-name match; select the correct player.' : 'Multiple exact name matches; select the correct player.' ?></small><?php endif ?>
                            </td>
                            <td><?= esc($row['army']) ?></td>
                            <td>
                                <select class="form-control" name="a<?= (int) $index ?>" required>
                                    <option value="">Select army</option>
                                    <?php foreach ($armies as $army): ?>
                                        <option value="<?= (int) $army['army_id'] ?>" <?= $armySelection === (int) $army['army_id'] ? 'selected' : '' ?>><?= esc($army['name'] . ' - ' . $army['system_name'] . ' v' . $army['version']) ?></option>
                                    <?php endforeach ?>
                                </select>
                                <?php if (count($row['armyOptions']) !== 1): ?><small class="text-danger"><?= count($row['armyOptions']) === 0 ? 'No exact army-name match; select the correct army.' : 'Multiple army-name matches; select the correct version.' ?></small><?php endif ?>
                            </td>
                            <td><?= esc((string) $row['score']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <h3>Round match pairings</h3>
        <?php foreach ($matches as $match): ?>
            <?php
            $winner = null;
            $loser = null;
            foreach ($rows as $row) {
                if ($row['fileId'] === $match['winnerFileId']) {
                    $winner = $row['name'];
                }
                if ($match['loserFileId'] !== null && $row['fileId'] === $match['loserFileId']) {
                    $loser = $row['name'];
                }
            }
            ?>
            <p>Round <?= (int) $match['round'] ?>: <?= esc((string) $winner) ?> <?= esc((string) $match['winnerScore']) ?> - <?= esc((string) $match['loserScore']) ?> <?= esc($loser ?? 'BYE') ?></p>
        <?php endforeach ?>
        <a class="btn btn-default" href="<?= site_url('competition/addAllResults/' . (int) $competition['competition_id']) ?>">Upload a different file</a>
        <button class="btn btn-danger" type="submit">Replace competition results</button>
    </form>
</div>
<?= $this->endSection() ?>
