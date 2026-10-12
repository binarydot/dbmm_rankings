<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'list' => 'List', 'details' => 'Details', 'results' => 'Results',
        'matches' => 'Matches', 'glicko' => 'Glicko', 'add' => 'Add Player',
        'edit' => 'Edit Player', 'delete' => 'Delete Player', 'date' => 'Date',
        'competition' => 'Competition', 'round' => 'Round', 'army' => 'Army',
        'score' => 'Score', 'opponents' => 'Opponent(s)', 'none' => 'No matches recorded',
        'scoreDistribution' => 'Match score distribution',
    ],
    'italian' => [
        'list' => 'Elenco', 'details' => 'Dettagli', 'results' => 'Risultati',
        'matches' => 'Partite', 'glicko' => 'Glicko', 'add' => 'Aggiungi giocatore',
        'edit' => 'Modifica giocatore', 'delete' => 'Elimina giocatore', 'date' => 'Data',
        'competition' => 'Concorso', 'round' => 'Turno', 'army' => 'Esercito',
        'score' => 'Punteggio', 'opponents' => 'Avversari', 'none' => 'Nessuna partita registrata',
        'scoreDistribution' => 'Distribuzione dei punteggi',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$playerId = (int) $player['competitor_id'];
$playerName = trim($player['first_name'] . ' ' . $player['last_name']);
$scores = array_fill(0, 26, 0);
foreach ($matches as $match) {
    $ownScore = (float) ($match['side'] === 'W' ? $match['winner_score'] : $match['loser_score']);
    $scores[max(0, min(25, (int) round($ownScore)))]++;
}
$maxScoreCount = max($scores ?: [0]);
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <nav class="navbar navbar-default" aria-label="<?= esc($text['matches'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('player/playerList/' . rawurlencode(substr((string) $player['last_name'], 0, 1))) ?>"><?= esc($text['list']) ?></a></li>
                <li><a href="<?= site_url('player/details/' . $playerId) ?>"><i class="fa fa-id-card-o fa-lg" aria-hidden="true"></i> <?= esc($text['details']) ?></a></li>
                <li><a href="<?= site_url('player/results/' . $playerId) ?>"><i class="fa fa-trophy fa-lg" aria-hidden="true"></i> <?= esc($text['results']) ?></a></li>
                <li class="active"><a href="<?= site_url('player/matches/' . $playerId) ?>" aria-current="page"><i class="fa fa-handshake-o fa-lg" aria-hidden="true"></i> <?= esc($text['matches']) ?></a></li>
                <li><a href="<?= site_url('player/glicko/' . $playerId) ?>"><i class="fa fa-line-chart fa-lg" aria-hidden="true"></i> <?= esc($text['glicko']) ?></a></li>
            </ul>
            <?php if ($canAddPlayer): ?>
                <a class="btn btn-default navbar-btn" href="<?= site_url('player/add') ?>"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i> <?= esc($text['add']) ?></a>
            <?php endif ?>
            <?php if ($canUpdatePlayer): ?>
                <a class="btn btn-default navbar-btn" href="<?= site_url('player/edit/' . $playerId) ?>"><i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i> <?= esc($text['edit']) ?></a>
                <a class="btn btn-default navbar-btn btn-danger" href="<?= site_url('player/delete/' . $playerId) ?>"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i> <?= esc($text['delete']) ?></a>
            <?php endif ?>
        </div>
    </nav>

    <h2><?= esc($playerName) ?></h2>
    <?php if ($matches === []): ?>
        <p><?= esc($text['none']) ?></p>
    <?php else: ?>
        <h3><?= esc($text['scoreDistribution']) ?></h3>
        <div class="table-responsive">
            <table class="table table-condensed" aria-label="<?= esc($text['scoreDistribution'], 'attr') ?>">
                <tbody><tr>
                <?php foreach ($scores as $score => $count): ?>
                    <?php $width = $maxScoreCount > 0 ? ($count / $maxScoreCount * 100) : 0; ?>
                    <td class="text-center" title="<?= esc($score . ' points: ' . $count, 'attr') ?>" style="vertical-align: bottom; height: 100px; padding: 2px">
                        <span style="display: block; background: #337ab7; height: <?= esc((string) $width) ?>%; min-height: <?= $count > 0 ? '3px' : '0' ?>"></span>
                        <small><?= esc((string) $score) ?></small>
                    </td>
                <?php endforeach ?>
                </tr></tbody>
            </table>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr>
                    <th><?= esc($text['date']) ?></th>
                    <th><?= esc($text['competition']) ?></th>
                    <th><?= esc($text['round']) ?></th>
                    <th><?= esc($text['army']) ?></th>
                    <th><?= esc($text['score']) ?></th>
                    <th><?= esc($text['opponents']) ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($matches as $match): ?>
                    <?php
                    $won = $match['side'] === 'W';
                    $ownScore = $won ? $match['winner_score'] : $match['loser_score'];
                    $opponentScore = $won ? $match['loser_score'] : $match['winner_score'];
                    $opponents = $won ? $match['losers'] : $match['winners'];
                    ?>
                    <tr>
                        <td><?= esc(date('Y-m-d', strtotime((string) $match['competition_date']))) ?></td>
                        <td><a href="<?= site_url('competition/matches/' . (int) $match['competition_id']) ?>"><?= esc($match['competition_name']) ?></a></td>
                        <td><?= esc((string) $match['round']) ?></td>
                        <td>
                            <?php if (! empty($match['army_id'])): ?>
                                <a href="<?= site_url('army/listMatches/' . (int) $match['army_id']) ?>"><?= esc($match['army'] ?? '') ?></a>
                            <?php else: ?>
                                <?= esc($match['army'] ?? '') ?>
                            <?php endif ?>
                        </td>
                        <td class="<?= $won ? 'green' : 'red' ?>"><?= esc(number_format((float) $ownScore) . ' - ' . number_format((float) $opponentScore)) ?></td>
                        <td>
                            <?php foreach ($opponents as $index => $opponent): ?>
                                <?php if ($index > 0): ?>, <?php endif ?>
                                <a href="<?= site_url('player/details/' . (int) $opponent['competitor_id']) ?>"><?= esc(trim($opponent['first_name'] . ' ' . $opponent['last_name'])) ?></a>
                                <?php if (! empty($opponent['army_name'])): ?> (<?= esc($opponent['army_name']) ?>)<?php endif ?>
                            <?php endforeach ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
