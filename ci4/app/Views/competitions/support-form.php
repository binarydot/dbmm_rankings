<?= $this->extend('layout') ?>

<?php
$heading = match ($type) {
    'runner' => 'Add Runner',
    'umpire' => 'Add Umpire',
    default => 'Add Stand-In',
};
$actionName = $type === 'standIn' ? 'StandIn' : ucfirst($type);
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><a href="<?= site_url('competition') ?>"><?= esc($competition['competition_series_name']) ?></a></h1>
    <h2><a href="<?= site_url('competition/results/' . (int) $competition['competition_id']) ?>"><?= esc($competition['competition_name']) ?></a> - <?= esc($heading) ?></h2>
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <form action="<?= site_url('competition/insert' . $actionName . '/' . (int) $competition['competition_id']) ?>" method="post" class="form-horizontal">
        <?= csrf_field() ?>
        <?php if ($type === 'standIn'): ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="standInId">Stand-In</label>
                <div class="col-sm-4">
                    <select class="form-control" id="standInId" name="standInId" required>
                        <option value="">Select a player</option>
                        <?php foreach ($options['competitors'] as $competitor): ?>
                            <option value="<?= (int) $competitor['competitor_id'] ?>" <?= (string) old('standInId') === (string) $competitor['competitor_id'] ? 'selected' : '' ?>><?= esc($competitor['first_name'] . ' ' . $competitor['last_name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="stoodInForId">Stood In For</label>
                <div class="col-sm-4">
                    <select class="form-control" id="stoodInForId" name="stoodInForId" required>
                        <option value="">Select an entered competitor</option>
                        <?php foreach ($options['competitionPlayers'] as $competitor): ?>
                            <option value="<?= (int) $competitor['competitor_id'] ?>" <?= (string) old('stoodInForId') === (string) $competitor['competitor_id'] ? 'selected' : '' ?>><?= esc($competitor['first_name'] . ' ' . $competitor['last_name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        <?php else: ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="competitorId"><?= $type === 'umpire' ? 'Umpire' : 'Competitor' ?></label>
                <div class="col-sm-4">
                    <select class="form-control" id="competitorId" name="competitorId" required>
                        <option value="">Select a player</option>
                        <?php foreach ($options['competitors'] as $competitor): ?>
                            <option value="<?= (int) $competitor['competitor_id'] ?>" <?= (string) old('competitorId') === (string) $competitor['competitor_id'] ? 'selected' : '' ?>><?= esc($competitor['first_name'] . ' ' . $competitor['last_name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <?php if ($type === 'runner' && $isTeamEvent): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="competitorId2">Partner</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="competitorId2" name="competitorId2">
                            <option value="-1">No partner</option>
                            <?php foreach ($options['competitors'] as $competitor): ?>
                                <option value="<?= (int) $competitor['competitor_id'] ?>" <?= (string) old('competitorId2', '-1') === (string) $competitor['competitor_id'] ? 'selected' : '' ?>><?= esc($competitor['first_name'] . ' ' . $competitor['last_name']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            <?php endif ?>
            <?php if ($type === 'runner'): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="army">Army</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="army" name="army">
                            <option value="-1">N/A</option>
                            <?php foreach ($options['armies'] as $army): ?>
                                <option value="<?= (int) $army['army_id'] ?>" <?= (string) old('army', '-1') === (string) $army['army_id'] ? 'selected' : '' ?>><?= esc($army['name'] . ' - ' . $army['system_name'] . ' v' . $army['version']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            <?php endif ?>
        <?php endif ?>
        <div class="col-sm-offset-2 col-sm-4">
            <a class="btn btn-default" href="<?= site_url('competition/results/' . (int) $competition['competition_id']) ?>">Cancel</a>
            <button class="btn btn-success" type="submit">Add</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
