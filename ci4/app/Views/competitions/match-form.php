<?= $this->extend('layout') ?>

<?php
$isEdit = $match !== null;
$winnerIds = $match['winners'] ?? [];
$loserIds = $match['losers'] ?? [];
$firstCompetitorId = $competitors[0]['competitor_id'] ?? '-1';
$winnerDefault0 = $winnerIds[0]['competitor_id'] ?? ($isEdit ? '-1' : $firstCompetitorId);
$winnerDefault1 = $winnerIds[1]['competitor_id'] ?? '-1';
$loserDefault0 = $loserIds[0]['competitor_id'] ?? ($isEdit ? '-1' : $firstCompetitorId);
$loserDefault1 = $loserIds[1]['competitor_id'] ?? '-1';
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($competition['competition_series_name']) ?></h1>
    <h2><?= esc($competition['competition_name']) ?> - <?= $isEdit ? 'Edit Match' : 'Add Match' ?></h2>
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>
    <form action="<?= site_url('competition/' . ($isEdit ? 'updateMatch/' . (int) $competition['competition_id'] . '/' . (int) $match['match_id'] : 'insertMatch/' . (int) $competition['competition_id'])) ?>" method="post" class="form-horizontal">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="matchdate">Match Date</label>
            <div class="col-sm-4"><input class="form-control" type="date" name="matchdate" id="matchdate" value="<?= old('matchdate', $match['match_date'] ?? $competition['competition_date']) ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="round">Round</label>
            <div class="col-sm-4"><input class="form-control" type="number" min="1" name="round" id="round" value="<?= old('round', (string) ($match['round'] ?? '')) ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="glicko_eligible">Eligible for Glicko</label>
            <div class="col-sm-4">
                <select class="form-control" name="glicko_eligible" id="glicko_eligible">
                    <option value="0" <?= old('glicko_eligible', (string) ($match['glicko_eligible'] ?? '0')) === '0' ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= old('glicko_eligible', (string) ($match['glicko_eligible'] ?? '0')) === '1' ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>
        </div>
        <?php foreach (['winner' => 'Winner(s)', 'loser' => 'Loser(s)'] as $side => $label): ?>
            <?php
            $ids = $side === 'winner' ? [$winnerDefault0, $winnerDefault1] : [$loserDefault0, $loserDefault1];
            ?>
            <h3><?= esc($label) ?></h3>
            <?php foreach ([0, 1] as $index): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="<?= $side ?>id<?= $index ?>">Competitor <?= $index + 1 ?></label>
                    <div class="col-sm-4">
                        <select class="form-control" name="<?= $side ?>id<?= $index ?>" id="<?= $side ?>id<?= $index ?>" <?= $index === 0 ? 'required' : '' ?>>
                            <option value="-1" <?= (string) old($side . 'id' . $index, (string) $ids[$index]) === '-1' ? 'selected' : '' ?>><?= $index === 0 ? '-- BYE --' : 'None' ?></option>
                            <?php foreach ($competitors as $competitor): ?>
                                <option value="<?= (int) $competitor['competitor_id'] ?>" <?= (string) old($side . 'id' . $index, (string) $ids[$index]) === (string) $competitor['competitor_id'] ? 'selected' : '' ?>><?= esc($competitor['first_name'] . ' ' . $competitor['last_name']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            <?php endforeach ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="<?= $side ?>army">Army</label>
                <div class="col-sm-4">
                    <select class="form-control" name="<?= $side ?>army" id="<?= $side ?>army">
                        <option value="-1">N/A</option>
                        <?php foreach ($armies as $army): ?>
                            <?php $defaultArmy = $match[$side === 'winner' ? 'winners' : 'losers'][0]['army_id'] ?? ($isEdit ? '-1' : ($competitors[0]['army_id'] ?? '-1')); ?>
                            <option value="<?= (int) $army['army_id'] ?>" <?= (string) old($side . 'army', (string) $defaultArmy) === (string) $army['army_id'] ? 'selected' : '' ?>><?= esc($army['name'] . ' - ' . $army['system_name'] . ' v' . $army['version']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        <?php endforeach ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="winnerscore">Winner Score</label>
            <div class="col-sm-2"><input class="form-control" type="number" step="any" min="0" max="25" name="winnerscore" id="winnerscore" value="<?= old('winnerscore', (string) ($match['winner_score'] ?? '')) ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="loserscore">Loser Score</label>
            <div class="col-sm-2"><input class="form-control" type="number" step="any" min="0" max="25" name="loserscore" id="loserscore" value="<?= old('loserscore', (string) ($match['loser_score'] ?? '')) ?>" required></div>
        </div>
        <div class="col-sm-offset-2 col-sm-4">
            <a class="btn btn-danger" href="<?= site_url('competition/matches/' . (int) $competition['competition_id']) ?>">Cancel</a>
            <button class="btn btn-success" type="submit"><?= $isEdit ? 'Update Match' : 'Add Match' ?></button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
