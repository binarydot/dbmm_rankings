<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h2>Competition Rankings</h2>
    <form class="form-inline" id="ranking-filter">
        <label for="rar">Country/Region:</label>
        <select class="form-control" id="rar" name="rar">
            <option value="-1"<?= $rarUsed < 1 ? ' selected' : '' ?>>All</option>
            <?php foreach ($rars as $rar): ?>
                <option value="<?= esc($rar->rar_id) ?>"<?= $rarUsed === (int) $rar->rar_id ? ' selected' : '' ?>>
                    <?= esc($rar->rar_name) ?>
                </option>
            <?php endforeach ?>
        </select>

        <label for="year">Year:</label>
        <select class="form-control" id="year" name="year">
            <option value="-1"<?= $yearUsed < 2001 ? ' selected' : '' ?>>Current</option>
            <?php for ($year = 2006, $currentYear = (int) date('Y'); $year <= $currentYear; $year++): ?>
                <option value="<?= $year ?>"<?= $yearUsed === $year ? ' selected' : '' ?>><?= $year ?></option>
            <?php endfor ?>
        </select>
        <button class="btn btn-default" type="submit">Show rankings</button>
    </form>
</div>

<section class="container-fluid">
    <h3>
        Date range for this ranking:
        <?= esc($rankingsData['startDate']) ?> - <?= esc($rankingsData['endDate']) ?>
    </h3>
    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th colspan="2">Position</th>
                <th>Name</th>
                <th>Score</th>
                <th>Competitions</th>
                <th colspan="20">Contributing Rankings</th>
            </tr>
            </thead>
            <tbody>
            <?php $position = 1; ?>
            <?php foreach ($rankingsData['rankings'] as $ranking): ?>
                <tr>
                    <td class="text-right"><?= $position++ ?></td>
                    <td>
                        <?php
                        $flag = $rarTags[$ranking['rarName']] ?? 'XI';
                        $flagFile = $flag === 'XI' ? '_united-nations.png' : $flag . '.png';
                        $flagPath = FCPATH . 'assets/images/flags24/' . $flagFile;
                        if (! is_file($flagPath)) {
                            $flagFile = '_united-nations.png';
                        }
                        ?>
                        <img class="ranking-flag" src="<?= base_url('assets/images/flags24/' . rawurlencode($flagFile)) ?>"
                             alt="<?= esc($ranking['rarName']) ?>">
                    </td>
                    <td>
                        <a href="<?= site_url('player/details/' . (int) $ranking['playerId']) ?>">
                            <?= esc($ranking['playerName']) ?>
                        </a>
                    </td>
                    <td class="text-right"><?= number_format((float) $ranking['playerData']['ranking'], 2) ?></td>
                    <td class="text-center"><?= (int) $ranking['playerData']['numComps'] ?></td>
                    <?php foreach ($ranking['playerData']['compData'] as $competition): ?>
                        <?php
                        $class = match ($competition['type']) {
                            'Protected/Used' => 'purple',
                            'Unprotected/Used' => 'green',
                            'Unprotected/Average' => 'blue',
                            default => 'red',
                        };
                        ?>
                        <td class="ranking-cell">
                            <span class="<?= $class ?>" title="<?= esc($competition['notes'], 'attr') ?>">
                                <?= esc($competition['score']) ?>
                            </span>
                        </td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <h3>Settings for this ranking</h3>
    <div>Maximum competitions: <?= (int) $rankingsData['minComps'] ?></div>
    <div>Maximum score: <?= (int) $rankingsData['maxScore'] ?></div>
</section>

<section class="container-fluid">
    <h3>Competitions included in this ranking</h3>
    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead>
            <tr><th>Date</th><th>Name</th><th>Weighting</th><th>Protected</th></tr>
            </thead>
            <tbody>
            <?php foreach ($rankingsData['competitions'] as $competition): ?>
                <tr>
                    <td><?= esc($competition->competition_date) ?></td>
                    <td><?= esc($competition->competition_name) ?></td>
                    <td><?= esc($competition->weighting) ?></td>
                    <td><?= $competition->protected ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
<script>
    document.getElementById('ranking-filter').addEventListener('submit', function (event) {
        event.preventDefault();
        const rar = encodeURIComponent(document.getElementById('rar').value);
        const year = encodeURIComponent(document.getElementById('year').value);
        window.location.href = '<?= site_url('rankings/comps') ?>/' + rar + '/' + year;
    });
</script>
<?= $this->endSection() ?>
