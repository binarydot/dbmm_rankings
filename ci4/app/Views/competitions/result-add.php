<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($competition['competition_series_name']) ?></h1>
    <h2><?= esc($competition['competition_name']) ?> - Add Result</h2>
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>
    <form action="<?= site_url('competition/insertResult/' . (int) $competition['competition_id']) ?>" method="post" class="form-horizontal">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="competitorId">Competitor</label>
            <div class="col-sm-4">
                <select class="form-control" name="competitorId" id="competitorId" required>
                    <option value="">Select a player</option>
                    <?php foreach ($competitors as $competitor): ?>
                        <option value="<?= (int) $competitor['competitor_id'] ?>" <?= (string) old('competitorId', '', false) === (string) $competitor['competitor_id'] ? 'selected' : '' ?>><?= esc($competitor['first_name'] . ' ' . $competitor['last_name']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <?php if (in_array($competition['type'], ['Mixed', 'Doubles'], true)): ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="competitorId2">Partner (optional)</label>
                <div class="col-sm-4">
                    <select class="form-control" name="competitorId2" id="competitorId2">
                        <option value="-1">No partner</option>
                        <?php foreach ($competitors as $competitor): ?>
                            <option value="<?= (int) $competitor['competitor_id'] ?>" <?= (string) old('competitorId2', '-1', false) === (string) $competitor['competitor_id'] ? 'selected' : '' ?>><?= esc($competitor['first_name'] . ' ' . $competitor['last_name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        <?php else: ?>
            <input type="hidden" name="competitorId2" value="-1">
        <?php endif ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="army">Army</label>
            <div class="col-sm-4">
                <select class="form-control" name="army" id="army">
                    <option value="-1">No army selected</option>
                    <?php foreach ($armies as $army): ?>
                        <option value="<?= (int) $army['army_id'] ?>" <?= (string) old('army', '-1', false) === (string) $army['army_id'] ? 'selected' : '' ?>><?= esc($army['name'] . ' - ' . $army['system_name'] . ' v' . $army['version']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="position">Position</label>
            <div class="col-sm-2"><input class="form-control" type="number" min="1" name="position" id="position" value="<?= old('position', '') ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="score">Score</label>
            <div class="col-sm-2"><input class="form-control" type="number" step="any" name="score" id="score" value="<?= old('score', '') ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="bonus">Bonus</label>
            <div class="col-sm-2"><input class="form-control" type="number" step="1" name="bonus" id="bonus" value="<?= old('bonus', '0') ?>"></div>
            <span class="col-sm-8">Bonus is a discrepancy between match scores and overall score, such as a penalty for a late list.</span>
        </div>
        <div class="col-sm-offset-2 col-sm-4">
            <a class="btn btn-danger" href="<?= site_url('competition/results/' . (int) $competition['competition_id']) ?>">Cancel</a>
            <button class="btn btn-success" type="submit">Add Result</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
