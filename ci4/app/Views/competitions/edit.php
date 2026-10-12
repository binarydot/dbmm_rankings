<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<script>
    var baseURL = <?= json_encode(rtrim(site_url(), '/'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
</script>
<script src="<?= base_url('js/util.js') ?>"></script>
<script src="<?= base_url('js/addCompSeries.js') ?>"></script>
<div class="container-fluid">
    <h2>Edit Competition</h2>
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>
    <div id="addCompSeriesHolder"></div>
    <form action="<?= site_url('competition/update/' . (int) $competition['competition_id']) ?>" method="post" class="form-horizontal">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="col-sm-2 control-label">ID</label>
            <div class="col-sm-4"><input class="form-control" value="<?= (int) $competition['competition_id'] ?>" disabled></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Name</label>
            <div class="col-sm-4"><input class="form-control" name="name" id="name" value="<?= old('name', $competition['competition_name']) ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="series">Series Name</label>
            <div class="col-sm-4">
                <div id="seriesPicker">
                    <select class="form-control" name="series" id="series" required>
                        <?php foreach ($series as $competitionSeries): ?>
                            <option value="<?= (int) $competitionSeries->id ?>" <?= (int) old('series', (string) $competition['competition_series_id'], false) === (int) $competitionSeries->id ? 'selected' : '' ?>><?= esc($competitionSeries->name) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-2"><button class="btn btn-default" type="button" onclick="addCompetitionSeries()">Add competition series</button></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="location">Location</label>
            <div class="col-sm-4"><input class="form-control" name="location" id="location" value="<?= old('location', $competition['location'] ?? '') ?>"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="date">Competition Date</label>
            <div class="col-sm-4"><input class="form-control" type="date" name="date" id="date" value="<?= old('date', $competition['competition_date']) ?>" required></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="type">Type</label>
            <div class="col-sm-4">
                <select class="form-control" name="type" id="type">
                    <?php foreach (['Singles', 'Mixed', 'Doubles'] as $type): ?>
                        <option value="<?= esc($type) ?>" <?= old('type', $competition['type'], false) === $type ? 'selected' : '' ?>><?= esc($type) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="protected">Protected</label>
            <div class="col-sm-4">
                <select class="form-control" name="protected" id="protected">
                    <option value="0" <?= (int) old('protected', (string) $competition['protected'], false) === 0 ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= (int) old('protected', (string) $competition['protected'], false) === 1 ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="weighting">Weighting</label>
            <div class="col-sm-4"><input class="form-control" type="number" step="any" name="weighting" id="weighting" value="<?= old('weighting', (string) $competition['weighting']) ?>"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="glicko_eligible">Eligible for Glicko</label>
            <div class="col-sm-4">
                <select class="form-control" name="glicko_eligible" id="glicko_eligible">
                    <option value="0" <?= (int) old('glicko_eligible', (string) $competition['glicko_eligible'], false) === 0 ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= (int) old('glicko_eligible', (string) $competition['glicko_eligible'], false) === 1 ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="rar">Country/Region</label>
            <div class="col-sm-4">
                <select class="form-control" name="rar" id="rar" required>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= (int) $region->rar_id ?>" <?= (int) old('rar', (string) $competition['rar_id'], false) === (int) $region->rar_id ? 'selected' : '' ?>><?= esc($region->rar_name) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="num_competitors">Number of Competitors/Teams</label>
            <div class="col-sm-4"><input class="form-control" type="number" name="num_competitors" id="num_competitors" value="<?= old('num_competitors', (string) $competition['num_competitors']) ?>"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="rounds">Rounds</label>
            <div class="col-sm-4"><input class="form-control" type="number" name="rounds" id="rounds" value="<?= old('rounds', (string) $competition['rounds']) ?>"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="system">System</label>
            <div class="col-sm-4">
                <select class="form-control" name="system" id="system">
                    <?php foreach ($systems as $system): ?>
                        <option value="<?= (int) $system->system_id ?>" <?= (int) old('system', (string) $competition['system_id'], false) === (int) $system->system_id ? 'selected' : '' ?>><?= esc($system->system_name . ' ' . $system->version) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="scale">Scale</label>
            <div class="col-sm-4">
                <select class="form-control" name="scale" id="scale">
                    <?php foreach ($scales as $scale): ?>
                        <option value="<?= (int) $scale->scale_id ?>" <?= (int) old('scale', (string) $competition['scale_id'], false) === (int) $scale->scale_id ? 'selected' : '' ?>><?= esc($scale->desc) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="army_points">Army Points</label>
            <div class="col-sm-4"><input class="form-control" type="number" name="army_points" id="army_points" value="<?= old('army_points', (string) $competition['army_points']) ?>"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="lists_allowed">Number of Lists Allowed</label>
            <div class="col-sm-4"><input class="form-control" type="number" step="any" name="lists_allowed" id="lists_allowed" value="<?= old('lists_allowed', (string) $competition['lists_allowed']) ?>"></div>
            <div class="col-sm-6">Fractional values are allowed when players can substitute part of a list.</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="scoring_system_id">Scoring System</label>
            <div class="col-sm-4">
                <select class="form-control" name="scoring_system_id" id="scoring_system_id">
                    <?php foreach ($scoringSystems as $scoringSystem): ?>
                        <option value="<?= (int) $scoringSystem->scoring_system_id ?>" <?= (int) old('scoring_system_id', (string) $competition['scoring_system_id'], false) === (int) $scoringSystem->scoring_system_id ? 'selected' : '' ?>><?= esc($scoringSystem->desc) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="format_id">Format</label>
            <div class="col-sm-4">
                <select class="form-control" name="format_id" id="format_id">
                    <?php foreach ($formats as $format): ?>
                        <option value="<?= (int) $format->format_id ?>" <?= (int) old('format_id', (string) $competition['format_id'], false) === (int) $format->format_id ? 'selected' : '' ?>><?= esc($format->desc) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="theme_id">Theme</label>
            <div class="col-sm-4">
                <select class="form-control" name="theme_id" id="theme_id">
                    <?php foreach ($themes as $theme): ?>
                        <option value="<?= (int) $theme->theme_id ?>" <?= (int) old('theme_id', (string) $competition['theme_id'], false) === (int) $theme->theme_id ? 'selected' : '' ?>><?= esc($theme->desc) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="notes">Notes</label>
            <div class="col-sm-4"><textarea class="form-control" name="notes" id="notes"><?= old('notes', $competition['notes'] ?? '') ?></textarea></div>
        </div>
        <div class="col-sm-offset-2 col-sm-4">
            <a class="btn btn-danger" href="<?= site_url('competition/details/' . (int) $competition['competition_id']) ?>">Cancel</a>
            <button class="btn btn-success" type="submit">Update</button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
