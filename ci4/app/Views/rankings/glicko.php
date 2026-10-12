<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'reliability' => 'Reliability',
        'region' => 'Country / Region',
        'all' => 'All',
        'rank' => '#',
        'player' => 'Player',
        'rating' => 'Rating',
        'thresholds' => 'Reliability Categories',
        'category' => 'Category',
        'lessThan' => 'Maximum reliability',
    ],
    'italian' => [
        'reliability' => 'Affidabilità',
        'region' => 'Paese / Regione',
        'all' => 'Tutti',
        'rank' => '#',
        'player' => 'Giocatore',
        'rating' => 'Punteggio',
        'thresholds' => 'Categorie di affidabilità',
        'category' => 'Categoria',
        'lessThan' => 'Affidabilità massima',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$grades = [
    'AAAAA' => ['stars' => 5, 'color' => '#191970'],
    'BBBB' => ['stars' => 4, 'color' => '#00f'],
    'CCC' => ['stars' => 3, 'color' => '#6495ed'],
    'DD' => ['stars' => 2, 'color' => '#87cefa'],
    'E' => ['stars' => 1, 'color' => '#b0e0e6'],
];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($siteLanguage === 'italian' ? 'Classifica Glicko' : 'Glicko Rankings') ?></h1>
    <form class="form-inline" method="get" action="<?= site_url('rankings/glicko') ?>">
        <div class="form-group">
            <label for="glicko-reliability"><?= esc($text['reliability']) ?></label>
            <select class="form-control" name="reliability" id="glicko-reliability" onchange="this.form.submit()">
                <?php foreach ($reliabilities as $reliability): ?>
                    <option value="<?= esc($reliability, 'attr') ?>"<?= $reliabilityUsed === $reliability ? ' selected' : '' ?>><?= esc($reliability) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-group">
            <label for="glicko-region"><?= esc($text['region']) ?></label>
            <select class="form-control" name="rar" id="glicko-region" onchange="this.form.submit()">
                <option value="-1"<?= $rarUsed < 0 ? ' selected' : '' ?>><?= esc($text['all']) ?></option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?= esc((string) $region['rar_id'], 'attr') ?>"<?= $rarUsed === (int) $region['rar_id'] ? ' selected' : '' ?>><?= esc($region['rar_name']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th><?= esc($text['rank']) ?></th>
                    <th><?= esc($text['player']) ?></th>
                    <th><?= esc($text['region']) ?></th>
                    <th colspan="<?= $showReliabilityValue ? 2 : 1 ?>"><?= esc($text['reliability']) ?></th>
                    <th><?= esc($text['rating']) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankings as $ranking): ?>
                    <?php $grade = $grades[$ranking['reliability_category']] ?? null; ?>
                    <?php $barWidth = max(0, min(100, ((float) $ranking['rating'] - 1600) / 8)); ?>
                    <tr>
                        <td><?= esc((string) $ranking['rank']) ?></td>
                        <td><a href="<?= site_url('player/glicko/' . (int) $ranking['competitor_id']) ?>"><?= esc($ranking['player_name']) ?></a></td>
                        <td>
                            <?php $flag = $ranking['rar_tag'] === 'XI' ? '_united-nations.png' : $ranking['rar_tag'] . '.png'; ?>
                            <?php if (! is_file(FCPATH . 'images/flags24/' . $flag)): ?><?php $flag = '_united-nations.png'; ?><?php endif ?>
                            <img class="ranking-flag" src="<?= esc(base_url('images/flags24/' . rawurlencode($flag)), 'attr') ?>" alt="<?= esc($ranking['rar_name'], 'attr') ?>">
                            <?= esc($ranking['rar_name']) ?>
                        </td>
                        <td>
                            <?php if ($grade !== null): ?>
                                <span style="color: <?= esc($grade['color'], 'attr') ?>" aria-label="<?= esc($ranking['reliability_category'], 'attr') ?>">
                                    <?php for ($star = 0; $star < $grade['stars']; $star++): ?><i class="fa fa-star fa-lg" aria-hidden="true"></i><?php endfor ?>
                                </span>
                            <?php endif ?>
                        </td>
                        <?php if ($showReliabilityValue): ?><td class="text-right"><?= esc(number_format((float) $ranking['reliability'], 2)) ?></td><?php endif ?>
                        <td>
                            <span style="width: <?= esc((string) $barWidth, 'attr') ?>%; min-width: 4rem; height: 20px; border: 1px solid #c00; background-color: #c00; color: white; display: inline-block; padding: 1px;">
                                <?= esc(number_format((float) $ranking['rating'])) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <h2><?= esc($text['thresholds']) ?></h2>
    <table class="table table-condensed">
        <thead><tr><th><?= esc($text['category']) ?></th><th><?= esc($text['lessThan']) ?></th></tr></thead>
        <tbody>
            <?php foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E'] as $grade => $label): ?>
                <tr><th scope="row"><?= esc($label) ?></th><td>&lt; <?= esc((string) $thresholds['reliability_' . $grade]) ?></td></tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
