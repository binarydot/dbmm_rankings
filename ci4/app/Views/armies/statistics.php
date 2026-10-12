<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'mostUsed' => 'Most Used Armies',
        'mostUsed12' => 'Most Used Armies (Last 12 Months)',
        'used' => 'Armies Used',
        'used12' => 'Armies Used (Last 12 Months)',
        'firstUsed' => 'First Use of Armies',
        'rank' => 'Rank',
        'summary' => 'The table summarises army usage and results in recorded matches.',
        'mostUsedDescription' => 'Armies are ranked by their number of recorded match uses.',
        'bookOrderDescription' => 'Armies are listed in book and list number order.',
        'scoreDescription' => 'Average scores are based on recorded 0-25 and converted 32-point match results.',
        'topDogDescription' => 'Top Dog handicap uses the average score and number of recorded results. The final Top Dog combines two-thirds of the player handicap and one-third of the army handicap.',
        'book' => 'Book',
        'list' => 'List',
        'name' => 'Name',
        'version' => 'Version',
        'total' => 'Total',
        'average' => 'Avg. Score',
        'topDog' => 'Top Dog',
        'wins' => '% Won',
        'draws' => '% Drawn',
        'losses' => '% Lost',
        'firstPlayer' => 'First User',
        'when' => 'When',
        'competition' => 'Competition',
        'noResults' => 'No army statistics are available for this selection.',
        'notAvailable' => 'N/A',
        'round' => 'R',
    ],
    'italian' => [
        'mostUsed' => 'Eserciti più utilizzati',
        'mostUsed12' => 'Eserciti più utilizzati (ultimi 12 mesi)',
        'used' => 'Eserciti utilizzati',
        'used12' => 'Eserciti utilizzati (ultimi 12 mesi)',
        'firstUsed' => 'Primo utilizzo degli eserciti',
        'rank' => 'Posizione',
        'summary' => 'La tabella riassume l’utilizzo e i risultati registrati per gli eserciti.',
        'mostUsedDescription' => 'Gli eserciti sono classificati in base al numero di partite registrate.',
        'bookOrderDescription' => 'Gli eserciti sono elencati per libro e numero di lista.',
        'scoreDescription' => 'Le medie utilizzano i risultati registrati con punteggio 0-25 e quelli convertiti dal sistema a 32 punti.',
        'topDogDescription' => 'Il handicap Top Dog utilizza il punteggio medio e il numero di risultati registrati.',
        'book' => 'Libro',
        'list' => 'Lista',
        'name' => 'Nome',
        'version' => 'Versione',
        'total' => 'Totale',
        'average' => 'Media',
        'topDog' => 'Top Dog',
        'wins' => '% Vittorie',
        'draws' => '% Pareggi',
        'losses' => '% Sconfitte',
        'firstPlayer' => 'Primo giocatore',
        'when' => 'Quando',
        'competition' => 'Concorso',
        'noResults' => 'Nessuna statistica disponibile per questa selezione.',
        'notAvailable' => 'N/D',
        'round' => 'T',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$tabs = [
    ['mostused', 0, 'mostUsed'],
    ['mostused', 12, 'mostUsed12'],
    ['booklistorder', 0, 'used'],
    ['booklistorder', 12, 'used12'],
    ['firstused', 0, 'firstUsed'],
];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($text['used']) ?></h1>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['used'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <?php foreach ($tabs as [$tabType, $tabPeriod, $label]): ?>
                    <?php $active = $viewType === $tabType && $monthsBack === $tabPeriod; ?>
                    <li<?= $active ? ' class="active"' : '' ?>>
                        <a href="<?= site_url('army/armyStats/' . $tabType . '/' . $tabPeriod . '/none') ?>"<?= $active ? ' aria-current="page"' : '' ?>><?= esc($text[$label]) ?></a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </nav>

    <p><?= esc($text['summary']) ?></p>
    <p><?= esc($viewType === 'mostused' ? $text['mostUsedDescription'] : $text['bookOrderDescription']) ?></p>
    <?php if ($viewType !== 'firstused'): ?>
        <p><?= esc($text['scoreDescription']) ?></p>
        <p><?= esc($text['topDogDescription']) ?> <a href="<?= site_url('army/topdog/' . $viewType . '/' . $monthsBack . '/' . $rankingMethod) ?>">Top Dog</a>.</p>
    <?php endif ?>

    <?php if ($armies === []): ?>
        <p><?= esc($text['noResults']) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <?php if ($viewType === 'mostused'): ?><th><?= esc($text['rank']) ?></th><?php endif ?>
                        <th><?= esc($text['book']) ?></th>
                        <th><?= esc($text['list']) ?></th>
                        <th><?= esc($text['name']) ?></th>
                        <th><?= esc($text['version']) ?></th>
                        <th><?= esc($text['total']) ?></th>
                        <th><?= esc($text['average']) ?></th>
                        <th><?= esc($text['topDog']) ?></th>
                        <?php if ($viewType === 'firstused'): ?>
                            <th><?= esc($text['firstPlayer']) ?></th>
                            <th><?= esc($text['when']) ?></th>
                            <th><?= esc($text['competition']) ?></th>
                        <?php else: ?>
                            <th><?= esc($text['wins']) ?></th>
                            <th><?= esc($text['draws']) ?></th>
                            <th><?= esc($text['losses']) ?></th>
                        <?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($armies as $army): ?>
                        <tr>
                            <?php if ($viewType === 'mostused'): ?><td class="text-center"><?= esc((string) $army['armyRank']) ?></td><?php endif ?>
                            <td class="text-center"><?= esc((string) $army['armyBook']) ?></td>
                            <td class="text-center"><?= esc((string) $army['armyList']) ?></td>
                            <td><a href="<?= site_url('army/listMatches/' . $army['armyID']) ?>"><?= esc($army['armyName']) ?></a></td>
                            <td class="text-center"><?= esc($army['rulesSystemName'] . ' ' . $army['armyListVersion']) ?></td>
                            <td class="text-center"><?= esc((string) $army['numMatches']) ?></td>
                            <td class="text-center" title="<?= esc($army['avgScoreHover'], 'attr') ?>">
                                <?= $army['avgScore'] === 'N/A' ? esc($text['notAvailable']) : esc(number_format((float) $army['avgScore'], 2)) ?>
                            </td>
                            <td class="text-center"><?= esc(number_format((float) $army['topDog'], 2)) ?></td>
                            <?php if ($viewType === 'firstused'): ?>
                                <?php $firstUse = $army['first_use']; ?>
                                <td><?= esc($firstUse['player_name'] ?? $text['notAvailable']) ?></td>
                                <td class="text-center"><?= esc($firstUse['date'] ?? $text['notAvailable']) ?></td>
                                <td>
                                    <?php if ($firstUse === null): ?>
                                        <?= esc($text['notAvailable']) ?>
                                    <?php else: ?>
                                        <?= esc($text['round'] . (string) $firstUse['round']) ?>
                                        <a href="<?= site_url('competition/details/' . $firstUse['competition_id']) ?>"><?= esc($firstUse['competition_name']) ?></a>
                                    <?php endif ?>
                                </td>
                            <?php else: ?>
                                <td class="text-center"><?= esc((string) $army['percentWins']) ?></td>
                                <td class="text-center"><?= esc((string) $army['percentDraws']) ?></td>
                                <td class="text-center"><?= esc((string) $army['percentLosses']) ?></td>
                            <?php endif ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
