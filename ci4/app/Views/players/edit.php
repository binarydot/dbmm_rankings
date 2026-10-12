<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php $playerId = (int) $player['competitor_id']; ?>
<div class="container-fluid">
    <h2>Edit <?= esc($player['first_name'] . ' ' . $player['last_name']) ?></h2>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger"><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul></div>
    <?php endif ?>
    <form action="<?= site_url('player/update/' . $playerId) ?>" method="post" class="form-horizontal">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="player-id">ID</label>
            <div class="col-sm-4"><input class="form-control" id="player-id" value="<?= $playerId ?>" disabled></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="iwf_id">IWF ID</label>
            <div class="col-sm-4"><input class="form-control" maxlength="7" id="iwf_id" name="iwf_id" value="<?= old('iwf_id', $player['iwf_id'] ?? '') ?>"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="first_name">First Name</label>
            <div class="col-sm-4"><input class="form-control" maxlength="20" id="first_name" name="first_name" value="<?= old('first_name', $player['first_name']) ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="last_name">Last Name</label>
            <div class="col-sm-4"><input class="form-control" maxlength="60" id="last_name" name="last_name" value="<?= old('last_name', $player['last_name']) ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="rar">Region</label>
            <div class="col-sm-4">
                <select class="form-control" id="rar" name="rar" required>
                    <?php foreach ($rars as $rar): ?>
                        <option value="<?= esc((string) $rar->rar_id, 'attr') ?>"<?= (int) old('rar', (string) $player['rar_id']) === (int) $rar->rar_id ? ' selected' : '' ?>>
                            <?= esc($rar->rar_name) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="anonymous">Anonymous</label>
            <div class="col-sm-4">
                <select class="form-control" id="anonymous" name="anonymous">
                    <option value="0"<?= (int) old('anonymous', (string) $player['anonymous']) === 0 ? ' selected' : '' ?>>No</option>
                    <option value="1"<?= (int) old('anonymous', (string) $player['anonymous']) === 1 ? ' selected' : '' ?>>Yes</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Creation Date</label>
            <div class="col-sm-4 form-control-static"><?= esc($player['cr_date']) ?></div>
        </div>
        <div class="col-sm-offset-2 col-sm-4">
            <a class="btn btn-danger" href="<?= site_url('player/details/' . $playerId) ?>">Cancel</a>
            <button class="btn btn-success" type="submit">Save</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
