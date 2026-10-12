<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'list' => 'List', 'details' => 'Details', 'results' => 'Results',
        'matches' => 'Matches', 'glicko' => 'Glicko', 'add' => 'Add Player',
        'edit' => 'Edit Player', 'delete' => 'Delete Player', 'date' => 'Date',
        'competition' => 'Competition', 'round' => 'Round', 'rating' => 'Rating',
        'reliability' => 'Reliability', 'score' => 'Score', 'opponent' => 'Opponent(s)',
        'new' => 'New', 'previous' => 'Previous', 'empty' => 'No Glicko history recorded',
        'ratingByMatch' => 'Glicko rating by match', 'history' => 'Rating history',
        'starting' => 'Starting',
    ],
    'italian' => [
        'list' => 'Elenco', 'details' => 'Dettagli', 'results' => 'Risultati',
        'matches' => 'Partite', 'glicko' => 'Glicko', 'add' => 'Aggiungi giocatore',
        'edit' => 'Modifica giocatore', 'delete' => 'Elimina giocatore', 'date' => 'Data',
        'competition' => 'Concorso', 'round' => 'Turno', 'rating' => 'Punteggio',
        'reliability' => 'Affidabilità', 'score' => 'Risultato', 'opponent' => 'Avversari',
        'new' => 'Nuovo', 'previous' => 'Precedente', 'empty' => 'Nessuna cronologia Glicko registrata',
        'ratingByMatch' => 'Punteggio Glicko per partita', 'history' => 'Cronologia punteggi',
        'starting' => 'Iniziale',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$playerId = (int) $player['competitor_id'];
$chartPoints = [];
$chartRangePoints = [];
if ($history !== []) {
    $chartValues = [];
    foreach ($history as $record) {
        $chartValues[] = (float) $record['rating'] - (float) $record['reliability'];
        $chartValues[] = (float) $record['rating'] + (float) $record['reliability'];
    }
    $minValue = min($chartValues);
    $maxValue = max($chartValues);
    $range = max(1.0, $maxValue - $minValue);
    $minValue -= $range * 0.05;
    $maxValue += $range * 0.05;
    $lastIndex = count($history) - 1;
    $upperPoints = [];
    $lowerPoints = [];
    foreach ($history as $index => $record) {
        $x = $lastIndex === 0 ? 450 : 50 + ($index / $lastIndex * 800);
        $y = 225 - (((float) $record['rating'] - $minValue) / ($maxValue - $minValue) * 180);
        $chartPoints[] = round($x, 2) . ',' . round($y, 2);
        $upper = (float) $record['rating'] + (float) $record['reliability'];
        $lower = (float) $record['rating'] - (float) $record['reliability'];
        $upperPoints[] = round($x, 2) . ',' . round(225 - (($upper - $minValue) / ($maxValue - $minValue) * 180), 2);
        $lowerPoints[] = round($x, 2) . ',' . round(225 - (($lower - $minValue) / ($maxValue - $minValue) * 180), 2);
    }
    $chartRangePoints = array_merge($upperPoints, array_reverse($lowerPoints));
}
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <nav class="navbar navbar-default" aria-label="<?= esc($text['glicko'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('player/playerList/' . rawurlencode(substr((string) $player['last_name'], 0, 1))) ?>"><?= esc($text['list']) ?></a></li>
                <li><a href="<?= site_url('player/details/' . $playerId) ?>"><i class="fa fa-id-card-o fa-lg" aria-hidden="true"></i> <?= esc($text['details']) ?></a></li>
                <li><a href="<?= site_url('player/results/' . $playerId) ?>"><i class="fa fa-trophy fa-lg" aria-hidden="true"></i> <?= esc($text['results']) ?></a></li>
                <li><a href="<?= site_url('player/matches/' . $playerId) ?>"><i class="fa fa-handshake-o fa-lg" aria-hidden="true"></i> <?= esc($text['matches']) ?></a></li>
                <li class="active"><a href="<?= site_url('player/glicko/' . $playerId) ?>" aria-current="page"><i class="fa fa-line-chart fa-lg" aria-hidden="true"></i> <?= esc($text['glicko']) ?></a></li>
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

    <h2><?= esc(trim($player['first_name'] . ' ' . $player['last_name'])) ?></h2>
    <?php if ($history === []): ?>
        <p><?= esc($text['empty']) ?></p>
    <?php else: ?>
        <h3><?= esc($text['ratingByMatch']) ?></h3>
        <div class="table-responsive">
            <svg viewBox="0 0 900 270" role="img" aria-label="<?= esc($text['history'], 'attr') ?>" style="width: 100%; max-width: 900px; height: auto">
                <title><?= esc($text['history']) ?></title>
                <line x1="50" y1="225" x2="850" y2="225" stroke="#777" />
                <line x1="50" y1="35" x2="50" y2="225" stroke="#777" />
                <polygon points="<?= esc(implode(' ', $chartRangePoints), 'attr') ?>" fill="#337ab7" fill-opacity="0.15" />
                <?php if (count($chartPoints) > 1): ?>
                    <polyline points="<?= esc(implode(' ', $chartPoints), 'attr') ?>" fill="none" stroke="#337ab7" stroke-width="3" />
                <?php else: ?>
                    <?php [$pointX, $pointY] = explode(',', $chartPoints[0]); ?>
                    <circle cx="<?= esc($pointX, 'attr') ?>" cy="<?= esc($pointY, 'attr') ?>" r="5" fill="#337ab7" />
                <?php endif ?>
                <text x="50" y="252" font-size="12"><?= esc(substr((string) $history[0]['competition_date'], 0, 10)) ?></text>
                <text x="850" y="252" text-anchor="end" font-size="12"><?= esc(substr((string) $history[count($history) - 1]['competition_date'], 0, 10)) ?></text>
            </svg>
        </div>
        <h3><?= esc($text['history']) ?></h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?= esc($text['date']) ?></th>
                        <th><?= esc($text['competition']) ?></th>
                        <th><?= esc($text['round']) ?></th>
                        <th><?= esc($text['rating']) ?></th>
                        <th><?= esc($text['reliability']) ?></th>
                        <th><?= esc($text['score']) ?></th>
                        <th><?= esc($text['opponent']) ?></th>
                        <th><?= esc($text['rating']) ?> (<?= esc($text['new']) ?>)</th>
                        <th><?= esc($text['rating']) ?> (<?= esc($text['previous']) ?>)</th>
                        <th><?= esc($text['reliability']) ?> (<?= esc($text['new']) ?>)</th>
                        <th><?= esc($text['reliability']) ?> (<?= esc($text['previous']) ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $record): ?>
                        <?php
                        $scoreClass = (float) $record['myScore'] > 15 ? 'green' : ((float) $record['myScore'] > 9 ? '' : 'red');
                        ?>
                        <?php foreach ($record['opponents'] as $opponent): ?>
                            <tr>
                                <td><?= esc(substr((string) $record['competition_date'], 0, 10)) ?></td>
                                <td><a href="<?= site_url('competition/matches/' . (int) $record['competition_id']) ?>"><?= esc($record['competition_name']) ?></a></td>
                                <td><?= esc((string) $record['round']) ?></td>
                                <td><?= esc((string) $record['rating']) ?></td>
                                <td><?= esc(number_format((float) $record['reliability'], 2)) ?></td>
                                <td class="<?= esc($scoreClass, 'attr') ?>"><?= esc(number_format((float) $record['myScore']) . ' - ' . number_format((float) $record['yourScore'])) ?></td>
                                <td>
                                    <?php if ($opponent['id'] !== null): ?>
                                        <a href="<?= site_url('player/glicko/' . (int) $opponent['id']) ?>"><?= esc($opponent['name']) ?></a>
                                    <?php else: ?>
                                        <?= esc($opponent['name']) ?>
                                    <?php endif ?>
                                </td>
                                <td><?= esc((string) $opponent['rating']) ?></td>
                                <td><?= esc((string) $opponent['previousRating']) ?></td>
                                <td><?= esc(is_numeric($opponent['reliability']) ? number_format((float) $opponent['reliability'], 2) : (string) $opponent['reliability']) ?></td>
                                <td><?= esc(is_numeric($opponent['previousReliability']) ? number_format((float) $opponent['previousReliability'], 2) : (string) $opponent['previousReliability']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php endforeach ?>
                    <tr>
                        <td><?= esc($text['starting']) ?></td>
                        <td colspan="2"></td>
                        <td>2000</td>
                        <td>300.00</td>
                        <td colspan="6"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
