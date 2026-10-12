<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'list' => 'List', 'details' => 'Details', 'results' => 'Results',
        'matches' => 'Matches', 'glicko' => 'Glicko', 'add' => 'Add Player',
        'edit' => 'Edit Player', 'delete' => 'Delete Player',
        'competition' => 'Competition', 'army' => 'Army', 'position' => 'Position',
        'score' => 'Score', 'bonus' => 'Bonus', 'none' => 'No competitions found',
    ],
    'italian' => [
        'list' => 'Elenco', 'details' => 'Dettagli', 'results' => 'Risultati',
        'matches' => 'Partite', 'glicko' => 'Glicko', 'add' => 'Aggiungi giocatore',
        'edit' => 'Modifica giocatore', 'delete' => 'Elimina giocatore',
        'competition' => 'Concorso', 'army' => 'Esercito', 'position' => 'Posizione',
        'score' => 'Punteggio', 'bonus' => 'Bonus', 'none' => 'Nessun concorso trovato',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$playerId = (int) $player['competitor_id'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <nav class="navbar navbar-default" aria-label="<?= esc($text['results'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('player/playerList/' . rawurlencode(substr((string) $player['last_name'], 0, 1))) ?>"><?= esc($text['list']) ?></a></li>
                <li><a href="<?= site_url('player/details/' . $playerId) ?>"><i class="fa fa-id-card-o fa-lg" aria-hidden="true"></i> <?= esc($text['details']) ?></a></li>
                <li class="active"><a href="<?= site_url('player/results/' . $playerId) ?>" aria-current="page"><i class="fa fa-trophy fa-lg" aria-hidden="true"></i> <?= esc($text['results']) ?></a></li>
                <li><a href="<?= site_url('player/matches/' . $playerId) ?>"><i class="fa fa-handshake-o fa-lg" aria-hidden="true"></i> <?= esc($text['matches']) ?></a></li>
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

    <h2><?= esc(trim($player['first_name'] . ' ' . $player['last_name'])) ?></h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?= esc($text['competition']) ?></th>
                    <th><?= esc($text['army']) ?></th>
                    <th><?= esc($text['position']) ?></th>
                    <th><?= esc($text['score']) ?></th>
                    <th><?= esc($text['bonus']) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results === []): ?>
                    <tr><td colspan="5"><?= esc($text['none']) ?></td></tr>
                <?php else: ?>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><a href="<?= site_url('competition/details/' . (int) $result['competition_id']) ?>"><?= esc($result['competition_name']) ?></a></td>
                            <td><?= esc($result['army'] ?? '') ?></td>
                            <td><?= esc((string) $result['position']) ?></td>
                            <td><?= esc((string) $result['score']) ?></td>
                            <td><?= esc((string) $result['bonus']) ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
