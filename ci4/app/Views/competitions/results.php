<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'list' => 'List', 'details' => 'Details', 'results' => 'Results', 'matches' => 'Matches',
        'addRunner' => 'Add Runner', 'addResult' => 'Add Result', 'addAll' => 'Add All Results',
        'addUmpire' => 'Add Umpire', 'addStandIn' => 'Add Stand-In', 'competitor' => 'Competitor',
        'army' => 'Army', 'position' => 'Position', 'score' => 'Score', 'ranking' => 'Ranking',
        'rating' => 'Rating', 'actions' => 'Actions', 'updateRunner' => 'Update Runner Data',
        'update' => 'Update', 'delete' => 'Delete', 'noResults' => 'No results for this competition yet',
        'umpires' => 'Umpires', 'noUmpires' => 'No umpires', 'standIns' => 'Stand-Ins',
        'for' => 'for', 'noStandIns' => 'No stand-ins',
        'ratingNote' => 'Rating is the initial Glicko rating of each player at the start of the competition.',
        'totalRating' => 'Total rating of all players in this competition was',
        'roundProgress' => 'Positions after each round', 'roundScores' => 'Match scores by round',
        'round' => 'Round', 'noRoundMatches' => 'No round match data available',
    ],
    'italian' => [
        'list' => 'Elenco', 'details' => 'Dettagli', 'results' => 'Risultati', 'matches' => 'Partite',
        'addRunner' => 'Aggiungi partecipante', 'addResult' => 'Aggiungi risultato', 'addAll' => 'Aggiungi tutti i risultati',
        'addUmpire' => 'Aggiungi arbitro', 'addStandIn' => 'Aggiungi sostituto', 'competitor' => 'Concorrente',
        'army' => 'Esercito', 'position' => 'Posizione', 'score' => 'Punteggio', 'ranking' => 'Classifica',
        'rating' => 'Valutazione', 'actions' => 'Azioni', 'updateRunner' => 'Aggiorna partecipante',
        'update' => 'Aggiorna', 'delete' => 'Elimina', 'noResults' => 'Nessun risultato per questo concorso',
        'umpires' => 'Arbitri', 'noUmpires' => 'Nessun arbitro', 'standIns' => 'Sostituti',
        'for' => 'per', 'noStandIns' => 'Nessun sostituto',
        'ratingNote' => 'La valutazione è il punteggio Glicko iniziale di ogni giocatore all’inizio del concorso.',
        'totalRating' => 'La valutazione totale dei giocatori in questo concorso era',
        'roundProgress' => 'Posizioni dopo ogni turno', 'roundScores' => 'Punteggi per turno',
        'round' => 'Turno', 'noRoundMatches' => 'Nessun dato di turno disponibile',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$competitionId = (int) $competition['competition_id'];
$competitorCount = count($roundStandings['players']);
$maxPosition = max(1, $competitorCount);
$colors = ['#337ab7', '#d9534f', '#5cb85c', '#f0ad4e', '#8064a2', '#46b8da', '#8a6d3b', '#777'];
$chartPoints = [];
if (count($roundStandings['rounds']) > 0 && $competitorCount > 0) {
    $lastRoundIndex = count($roundStandings['rounds']) - 1;
    foreach ($roundStandings['players'] as $playerIndex => $player) {
        $points = [];
        $markers = [];
        foreach ($roundStandings['rounds'] as $roundIndex => $round) {
            $x = $lastRoundIndex === 0 ? 380 : 70 + ($roundIndex / $lastRoundIndex * 620);
            $position = $player['positions'][$round] ?? $maxPosition;
            $y = 35 + (($position - 1) / $maxPosition * 490);
            $points[] = round($x, 2) . ',' . round($y, 2);
            $markers[] = [
                'x' => round($x, 2),
                'y' => round($y, 2),
                'round' => $round,
                'position' => $position,
                'score' => $player['scores'][$round] ?? 0,
            ];
        }
        $chartPoints[] = [
            'name' => $player['name'],
            'points' => implode(' ', $points),
            'markers' => $markers,
            'color' => $colors[$playerIndex % count($colors)],
        ];
    }
}
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <h1><a href="<?= site_url('competition') ?>"><?= esc($competition['competition_series_name']) ?></a></h1>
    <h2><a href="<?= site_url('competition/details/' . $competitionId) ?>"><?= esc($competition['competition_name']) ?></a></h2>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['results'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('competition') ?>"><?= esc($text['list']) ?></a></li>
                <li><a href="<?= site_url('competition/details/' . $competitionId) ?>"><?= esc($text['details']) ?></a></li>
                <li class="active"><a href="<?= site_url('competition/results/' . $competitionId) ?>" aria-current="page"><?= esc($text['results']) ?></a></li>
                <li><a href="<?= site_url('competition/matches/' . $competitionId) ?>"><?= esc($text['matches']) ?></a></li>
            </ul>
            <?php if ($canAddResult): ?>
                <?php if (! $fullResults): ?>
                    <a class="btn btn-default navbar-btn" href="<?= site_url('competition/addRunner/' . $competitionId) ?>"><?= esc($text['addRunner']) ?></a>
                <?php endif ?>
                <a class="btn btn-default navbar-btn" href="<?= site_url('competition/addResult/' . $competitionId) ?>"><?= esc($text['addResult']) ?></a>
                <a class="btn btn-default navbar-btn" href="<?= site_url('competition/addAllResults/' . $competitionId) ?>"><?= esc($text['addAll']) ?></a>
                <a class="btn btn-default navbar-btn" href="<?= site_url('competition/addUmpire/' . $competitionId) ?>"><?= esc($text['addUmpire']) ?></a>
                <a class="btn btn-default navbar-btn" href="<?= site_url('competition/addStandIn/' . $competitionId) ?>"><?= esc($text['addStandIn']) ?></a>
            <?php endif ?>
        </div>
    </nav>

    <h3><?= esc($text['results']) ?></h3>
    <?php if ($results === []): ?>
        <p><?= esc($text['noResults']) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-condensed" id="resultsTable">
                <thead>
                    <tr>
                        <th><?= esc($text['competitor']) ?></th>
                        <th></th>
                        <th><?= esc($text['army']) ?></th>
                        <?php if ($fullResults): ?>
                            <th><?= esc($text['position']) ?></th>
                            <th><?= esc($text['score']) ?></th>
                            <th><?= esc($text['ranking']) ?></th>
                        <?php endif ?>
                        <th><?= esc($text['rating']) ?></th>
                        <?php if ($canUpdateResult): ?>
                            <th colspan="2"><?= esc($text['actions']) ?></th>
                        <?php endif ?>
                        <?php if ($fullResults && $canUpdateResult): ?><th></th><?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <?php
                        $flagFile = $row['rar_tag'] === 'XI' ? '_united-nations.png' : $row['rar_tag'] . '.png';
                        if (! is_file(FCPATH . 'images/flags24/' . $flagFile)) {
                            $flagFile = '_united-nations.png';
                        }
                        ?>
                        <tr>
                            <td><a href="<?= site_url('player/details/' . (int) $row['competitor_id']) ?>"><?= esc($row['competitor_name']) ?></a></td>
                            <td><img src="<?= esc(base_url('images/flags24/' . rawurlencode($flagFile)), 'attr') ?>" alt="<?= esc($row['rar_name'], 'attr') ?>"></td>
                            <td>
                                <?php if ($row['army_id'] !== null): ?>
                                    <a href="<?= site_url('army/listMatches/' . (int) $row['army_id']) ?>"><?= esc($row['army_name'] ?? '') ?></a>
                                <?php endif ?>
                            </td>
                            <?php if ($fullResults): ?>
                                <td><?= esc((string) $row['position']) ?></td>
                                <td>
                                    <?= esc((string) $row['score']) ?>
                                    <?php if ((float) $row['bonus'] > 0): ?>(+<?= esc((string) $row['bonus']) ?>)
                                    <?php elseif ((float) $row['bonus'] < 0): ?>(<?= esc((string) $row['bonus']) ?>)<?php endif ?>
                                </td>
                                <td><?= esc(number_format((float) $row['ranking_points'], 2)) ?></td>
                            <?php endif ?>
                            <td><?= esc(number_format((float) $row['rating'], 0)) ?></td>
                            <?php if ($canUpdateResult): ?>
                                <td><a class="btn btn-default" href="<?= site_url('competition/editResult/' . $competitionId . '/' . (int) $row['position_id']) ?>"><?= esc($text['update']) ?></a></td>
                                <td><a class="btn btn-danger" href="<?= site_url('competition/deleteResult/' . $competitionId . '/' . (int) $row['position_id']) ?>"><?= esc($text['delete']) ?></a></td>
                                <?php if ($fullResults): ?><td class="error"><?= esc($row['error_message']) ?></td><?php endif ?>
                            <?php endif ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <?php if ($fullResults && $roundStandings['rounds'] !== []): ?>
        <h3><?= esc($text['roundProgress']) ?></h3>
        <div class="row">
            <div class="col-md-9">
                <svg viewBox="0 0 760 600" role="img" aria-label="<?= esc($text['roundProgress'], 'attr') ?>" style="width: 100%; height: 560px">
                    <title><?= esc($text['roundProgress']) ?></title>
                    <line x1="70" y1="35" x2="70" y2="525" stroke="#777" />
                    <line x1="70" y1="525" x2="690" y2="525" stroke="#777" />
                    <?php foreach ($chartPoints as $series): ?>
                        <polyline points="<?= esc($series['points'], 'attr') ?>" fill="none" stroke="<?= esc($series['color'], 'attr') ?>" stroke-width="2" />
                        <?php foreach ($series['markers'] as $marker): ?>
                            <?php $tooltip = $series['name'] . ' - ' . $text['round'] . ' ' . $marker['round'] . ': ' . number_format((float) $marker['score'], 0) . ' ' . $text['score'] . ' (' . $text['position'] . ' ' . $marker['position'] . ')'; ?>
                            <circle cx="<?= esc((string) $marker['x'], 'attr') ?>" cy="<?= esc((string) $marker['y'], 'attr') ?>" r="5" fill="<?= esc($series['color'], 'attr') ?>" stroke="#fff" stroke-width="1" tabindex="0" aria-label="<?= esc($tooltip, 'attr') ?>">
                                <title><?= esc($tooltip) ?></title>
                            </circle>
                        <?php endforeach ?>
                    <?php endforeach ?>
                    <?php foreach ($roundStandings['rounds'] as $index => $round): ?>
                        <?php $x = count($roundStandings['rounds']) === 1 ? 380 : 70 + ($index / (count($roundStandings['rounds']) - 1) * 620); ?>
                        <text x="<?= esc((string) $x, 'attr') ?>" y="550" text-anchor="middle" font-size="12"><?= esc($text['round']) ?> <?= esc((string) $round) ?></text>
                    <?php endforeach ?>
                </svg>
            </div>
            <div class="col-md-3">
                <ul class="list-unstyled" aria-label="<?= esc($text['roundProgress'], 'attr') ?>">
                <?php foreach ($chartPoints as $series): ?>
                    <li><span aria-hidden="true" style="color: <?= esc($series['color'], 'attr') ?>">&#9632;</span> <?= esc($series['name']) ?></li>
                <?php endforeach ?>
                </ul>
            </div>
        </div>
        <h3><?= esc($text['roundScores']) ?></h3>
        <div class="table-responsive">
            <table class="table table-condensed table-striped">
                <thead><tr><th><?= esc($text['competitor']) ?></th>
                    <?php foreach ($roundStandings['rounds'] as $round): ?><th><?= esc($text['round']) ?> <?= esc((string) $round) ?></th><?php endforeach ?>
                </tr></thead>
                <tbody>
                    <?php foreach ($roundStandings['players'] as $player): ?>
                        <tr>
                            <th scope="row"><?= esc($player['name']) ?></th>
                            <?php foreach ($roundStandings['rounds'] as $round): ?><td><?= esc(number_format((float) ($player['scores'][$round] ?? 0), 0)) ?></td><?php endforeach ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <h3><?= esc($text['umpires']) ?></h3>
    <?php if ($umpires === []): ?>
        <p><?= esc($text['noUmpires']) ?></p>
    <?php else: ?>
        <table class="table table-striped table-condensed"><tbody>
            <?php foreach ($umpires as $umpire): ?>
                <tr>
                    <td><a href="<?= site_url('player/details/' . (int) $umpire['competitor_id']) ?>"><?= esc(trim($umpire['first_name'] . ' ' . $umpire['last_name'])) ?></a></td>
                    <?php if ($canUpdateResult): ?><td><a class="btn btn-danger" href="<?= site_url('competition/deleteUmpire/' . $competitionId . '/' . (int) $umpire['umpire_id']) ?>"><?= esc($text['delete']) ?></a></td><?php endif ?>
                </tr>
            <?php endforeach ?>
        </tbody></table>
    <?php endif ?>

    <h3><?= esc($text['standIns']) ?></h3>
    <?php if ($standIns === []): ?>
        <p><?= esc($text['noStandIns']) ?></p>
    <?php else: ?>
        <table class="table table-striped table-condensed"><tbody>
            <?php foreach ($standIns as $standIn): ?>
                <tr>
                    <td>
                        <a href="<?= site_url('player/details/' . (int) $standIn['competitor_id']) ?>"><?= esc(trim($standIn['first_name'] . ' ' . $standIn['last_name'])) ?></a>
                        <?= esc($text['for']) ?>
                        <a href="<?= site_url('player/details/' . (int) $standIn['stood_in_for_id']) ?>"><?= esc(trim($standIn['stood_in_for_first_name'] . ' ' . $standIn['stood_in_for_last_name'])) ?></a>
                    </td>
                    <?php if ($canUpdateResult): ?><td><a class="btn btn-danger" href="<?= site_url('competition/deleteStandIn/' . $competitionId . '/' . (int) $standIn['stand_in_id']) ?>"><?= esc($text['delete']) ?></a></td><?php endif ?>
                </tr>
            <?php endforeach ?>
        </tbody></table>
    <?php endif ?>

    <p><?= esc($text['ratingNote']) ?></p>
    <p><?= esc($text['totalRating']) ?> <?= esc(number_format($totalRating, 2)) ?></p>
</div>
<?= $this->endSection() ?>
