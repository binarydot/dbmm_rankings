<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'results' => 'Match Results',
        'details' => 'Match Details',
        'annual' => 'Annual Trends',
        'system' => 'System',
        'score' => 'Result',
        'number' => 'Number',
        'percent' => '%',
        'excluded' => 'Matches not scored from 25 points are excluded.',
        'none' => 'No 0-to-25 match results are available.',
        'chart' => 'Result distribution by system',
    ],
    'italian' => [
        'results' => 'Risultati partite',
        'details' => 'Dettagli partite',
        'annual' => 'Andamento annuale',
        'system' => 'Sistema',
        'score' => 'Risultato',
        'number' => 'Numero',
        'percent' => '%',
        'excluded' => 'Sono escluse le partite non basate sul punteggio da 25.',
        'none' => 'Non sono disponibili risultati con punteggio da 0 a 25.',
        'chart' => 'Distribuzione dei risultati per sistema',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$colors = ['#337ab7', '#5cb85c', '#f0ad4e', '#d9534f', '#8064a2', '#46b8da', '#8a6d3b', '#777', '#1b9e77', '#d95f02', '#7570b3', '#e7298a', '#66a61e'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($text['results']) ?></h1>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['results'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li class="active"><a href="<?= site_url('matches') ?>" aria-current="page"><?= esc($text['results']) ?></a></li>
                <li><a href="<?= site_url('matches/matchesDetails/' . date('Y')) ?>"><?= esc($text['details']) ?></a></li>
                <li><a href="<?= site_url('matches/annualTrends') ?>"><?= esc($text['annual']) ?></a></li>
            </ul>
        </div>
    </nav>

    <h2><?= esc($text['chart']) ?></h2>
    <?php if ($results === []): ?>
        <p><?= esc($text['none']) ?></p>
    <?php else: ?>
        <div role="region" aria-label="<?= esc($text['chart'], 'attr') ?>" style="position: relative; height: 380px; padding: 0 10px 45px 55px;">
            <div style="position: absolute; top: 0; bottom: 60px; left: 0; right: 0;">
                <?php foreach ([100, 75, 50, 25, 0] as $tickIndex => $tick): ?>
                    <?php $top = $tickIndex === 4 ? 100 : $tickIndex * 25; ?>
                    <span aria-hidden="true" style="position: absolute; top: <?= $top ?>%; left: 0; width: 43px; transform: translateY(-50%); text-align: right; font-size: 12px;"><?= $tick ?>%</span>
                    <span aria-hidden="true" style="position: absolute; top: <?= $top ?>%; left: 50px; right: 0; border-top: 1px solid #ddd;"></span>
                <?php endforeach ?>
                <div style="position: absolute; inset: 0 0 0 50px; display: grid; grid-template-columns: repeat(<?= count($results) ?>, minmax(0, 1fr));">
                    <?php foreach ($results as $system): ?>
                        <div style="position: relative; height: 100%; display: flex; flex-direction: column; align-items: center;">
                            <div style="width: 60%; max-width: 70px; height: 100%; display: flex; flex-direction: column; z-index: 1;">
                                <?php foreach ($scoreLabels as $scoreIndex => $scoreLabel): ?>
                                    <?php $score = $system['scores'][$scoreLabel] ?? null; $percent = (float) ($score['percent'] ?? 0); ?>
                                    <?php if ($percent > 0): ?>
                                        <?php $tooltip = $system['name'] . ' - ' . $scoreLabel . ': ' . ($score['number'] ?? 0) . ' (' . number_format($percent, 2) . '%)'; ?>
                                        <div role="img" aria-label="<?= esc($tooltip, 'attr') ?>" title="<?= esc($tooltip, 'attr') ?>" style="height: <?= esc((string) $percent, 'attr') ?>%; flex-shrink: 0; background-color: <?= esc($colors[$scoreIndex % count($colors)], 'attr') ?>;"></div>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </div>
                            <div style="position: absolute; top: calc(100% + 6px); width: 100%; padding: 0 3px; text-align: center; overflow-wrap: anywhere; font-size: 12px;"><?= esc($system['name']) ?></div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
        <ul class="list-inline" aria-label="<?= esc($text['chart'], 'attr') ?>">
            <?php foreach ($scoreLabels as $scoreIndex => $scoreLabel): ?>
                <li><span aria-hidden="true" style="color: <?= esc($colors[$scoreIndex % count($colors)], 'attr') ?>">&#9632;</span> <?= esc($scoreLabel) ?></li>
            <?php endforeach ?>
        </ul>

        <div class="table-responsive">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <?php foreach ($results as $system): ?>
                            <th colspan="3" class="text-center"><?= esc($system['name']) ?></th>
                        <?php endforeach ?>
                    </tr>
                    <tr>
                        <?php foreach ($results as $_): ?>
                            <th><?= esc($text['score']) ?></th>
                            <th><?= esc($text['number']) ?></th>
                            <th><?= esc($text['percent']) ?></th>
                        <?php endforeach ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scoreLabels as $scoreLabel): ?>
                        <tr>
                            <?php foreach ($results as $system): ?>
                                <?php $score = $system['scores'][$scoreLabel] ?? null; ?>
                                <td class="text-center"><?= esc($scoreLabel) ?></td>
                                <td class="text-right"><?= $score === null ? '-' : esc((string) $score['number']) ?></td>
                                <td class="text-right"><?= $score === null ? '-' : esc(number_format((float) $score['percent'], 2)) ?></td>
                            <?php endforeach ?>
                        </tr>
                    <?php endforeach ?>
                    <tr>
                        <?php foreach ($results as $system): ?>
                            <td></td>
                            <td class="text-right"><?= esc((string) $system['total']) ?></td>
                            <td class="text-right">100.00</td>
                        <?php endforeach ?>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif ?>
    <p><?= esc($text['excluded']) ?></p>
</div>
<?= $this->endSection() ?>
