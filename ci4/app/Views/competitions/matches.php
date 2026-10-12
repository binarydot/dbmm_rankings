<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'list' => 'List', 'details' => 'Details', 'results' => 'Results',
        'matches' => 'Matches', 'round' => 'Round', 'winner' => 'Winner',
        'loser' => 'Loser', 'score' => 'Score', 'edit' => 'Edit Match',
        'delete' => 'Delete Match', 'add' => 'Add Match', 'none' => 'No matches recorded',
    ],
    'italian' => [
        'list' => 'Elenco', 'details' => 'Dettagli', 'results' => 'Risultati',
        'matches' => 'Partite', 'round' => 'Turno', 'winner' => 'Vincitore',
        'loser' => 'Sconfitto', 'score' => 'Punteggio', 'edit' => 'Modifica partita',
        'delete' => 'Elimina partita', 'add' => 'Aggiungi partita', 'none' => 'Nessuna partita registrata',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$competitionId = (int) $competition['competition_id'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><a href="<?= site_url('competition') ?>"><?= esc($competition['competition_series_name']) ?></a></h1>
    <h2><a href="<?= site_url('competition/details/' . $competitionId) ?>"><?= esc($competition['competition_name']) ?></a></h2>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['matches'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('competition') ?>"><?= esc($text['list']) ?></a></li>
                <li><a href="<?= site_url('competition/details/' . $competitionId) ?>"><?= esc($text['details']) ?></a></li>
                <li><a href="<?= site_url('competition/results/' . $competitionId) ?>"><?= esc($text['results']) ?></a></li>
                <li class="active"><a href="<?= site_url('competition/matches/' . $competitionId) ?>" aria-current="page"><?= esc($text['matches']) ?></a></li>
            </ul>
            <?php if ($canAddMatch): ?>
                <a class="btn btn-default navbar-btn" href="<?= site_url('competition/addMatch/' . $competitionId) ?>"><?= esc($text['add']) ?></a>
            <?php endif ?>
        </div>
    </nav>

    <?php if ($rounds === []): ?>
        <p><?= esc($text['none']) ?></p>
    <?php else: ?>
        <?php foreach ($rounds as $roundNumber => $matches): ?>
            <details class="panel panel-default">
                <summary class="panel-heading"><h3><?= esc($text['round']) ?> <?= esc((string) $roundNumber) ?></h3></summary>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th><?= esc($text['winner']) ?></th>
                                <th><?= esc($text['score']) ?></th>
                                <th><?= esc($text['loser']) ?></th>
                                <?php if ($canManageMatches): ?><th><?= esc($text['matches']) ?></th><?php endif ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matches as $match): ?>
                                <?php $teamRows = max(count($match['winners']), count($match['losers']), 1); ?>
                                <?php for ($index = 0; $index < $teamRows; $index++): ?>
                                    <tr>
                                        <td>
                                            <?php if (isset($match['winners'][$index])): ?>
                                                <?php $winner = $match['winners'][$index]; ?>
                                                <a href="<?= site_url('player/details/' . $winner['id']) ?>"><?= esc($winner['name']) ?></a>
                                                <?php if ($winner['armyId'] !== null): ?>
                                                    <a href="<?= site_url('army/details/' . $winner['armyId']) ?>">(<?= esc($winner['armyName'] ?? '') ?>)</a>
                                                <?php endif ?>
                                            <?php endif ?>
                                        </td>
                                        <?php if ($index === 0): ?>
                                            <td rowspan="<?= $teamRows ?>" class="text-center">
                                                <span<?= $match['error'] ? ' style="color: red"' : '' ?>><?= esc($match['score']) ?></span>
                                            </td>
                                        <?php endif ?>
                                        <td>
                                            <?php if (isset($match['losers'][$index])): ?>
                                                <?php $loser = $match['losers'][$index]; ?>
                                                <a href="<?= site_url('player/details/' . $loser['id']) ?>"><?= esc($loser['name']) ?></a>
                                                <?php if ($loser['armyId'] !== null): ?>
                                                    <a href="<?= site_url('army/details/' . $loser['armyId']) ?>">(<?= esc($loser['armyName'] ?? '') ?>)</a>
                                                <?php endif ?>
                                            <?php endif ?>
                                        </td>
                                        <?php if ($canManageMatches && $index === 0): ?>
                                            <td rowspan="<?= $teamRows ?>">
                                                <a class="btn btn-default" href="<?= site_url('competition/editMatch/' . $competitionId . '/' . $match['id']) ?>"><i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i> <?= esc($text['edit']) ?></a>
                                                <a class="btn btn-danger" href="<?= site_url('competition/deleteMatch/' . $competitionId . '/' . $match['id']) ?>"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i> <?= esc($text['delete']) ?></a>
                                            </td>
                                        <?php elseif ($canManageMatches): ?>
                                            <td></td>
                                        <?php endif ?>
                                    </tr>
                                <?php endfor ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </details>
        <?php endforeach ?>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
