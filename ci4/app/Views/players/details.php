<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'list' => 'List',
        'details' => 'Details',
        'results' => 'Results',
        'matches' => 'Matches',
        'glicko' => 'Glicko',
        'add' => 'Add Player',
        'edit' => 'Edit Player',
        'delete' => 'Delete Player',
        'id' => 'ID',
        'iwfId' => 'IWF ID',
        'firstName' => 'First Name',
        'lastName' => 'Last Name',
        'region' => 'Region',
        'creationDate' => 'Creation Date',
        'armies' => 'Armies used',
        'competitionSingular' => 'competition',
        'competitionPlural' => 'competitions',
        'none' => 'No matches recorded',
    ],
    'italian' => [
        'list' => 'Elenco',
        'details' => 'Dettagli',
        'results' => 'Risultati',
        'matches' => 'Partite',
        'glicko' => 'Glicko',
        'add' => 'Aggiungi giocatore',
        'edit' => 'Modifica giocatore',
        'delete' => 'Elimina giocatore',
        'id' => 'ID',
        'iwfId' => 'IWF ID',
        'firstName' => 'Nome',
        'lastName' => 'Cognome',
        'region' => 'Regione',
        'creationDate' => 'Data di creazione',
        'armies' => 'Eserciti utilizzati',
        'competitionSingular' => 'concorso',
        'competitionPlural' => 'concorsi',
        'none' => 'Nessuna partita registrata',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$playerId = (int) $player['competitor_id'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endif ?>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['details'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('player/playerList/' . rawurlencode(substr((string) $player['last_name'], 0, 1))) ?>"><?= esc($text['list']) ?></a></li>
                <li class="active"><a href="<?= site_url('player/details/' . $playerId) ?>" aria-current="page"><i class="fa fa-id-card-o fa-lg" aria-hidden="true"></i> <?= esc($text['details']) ?></a></li>
                <li><a href="<?= site_url('player/results/' . $playerId) ?>"><i class="fa fa-trophy fa-lg" aria-hidden="true"></i> <?= esc($text['results']) ?></a></li>
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

    <h2><?= esc($player['first_name']) ?> <?= esc($player['last_name']) ?></h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
            <tr><th scope="row"><?= esc($text['id']) ?></th><td><?= $playerId ?></td></tr>
            <tr><th scope="row"><?= esc($text['iwfId']) ?></th><td><?= esc($player['iwf_id'] ?? '') ?></td></tr>
            <tr><th scope="row"><?= esc($text['firstName']) ?></th><td><?= esc($player['first_name']) ?></td></tr>
            <tr><th scope="row"><?= esc($text['lastName']) ?></th><td><?= esc($player['last_name']) ?></td></tr>
            <tr><th scope="row"><?= esc($text['region']) ?></th><td><?= esc($player['rar_name']) ?></td></tr>
            <tr><th scope="row"><?= esc($text['creationDate']) ?></th><td><?= esc($player['cr_date']) ?></td></tr>
            </tbody>
        </table>
    </div>

    <h3><?= esc($text['armies']) ?></h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
            <?php if ($player['armies'] === []): ?>
                <tr><td><?= esc($text['none']) ?></td></tr>
            <?php else: ?>
                <?php foreach ($player['armies'] as $army): ?>
                    <?php $uses = (int) $army['uses']; ?>
                    <tr>
                        <td><a href="<?= site_url('army/listMatches/' . (int) $army['army_id']) ?>"><?= esc($army['name']) ?></a></td>
                        <td><?= $uses ?> <?= esc($uses === 1 ? $text['competitionSingular'] : $text['competitionPlural']) ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
