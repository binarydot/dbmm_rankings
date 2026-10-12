<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'list' => 'List',
        'fullList' => 'Full List',
        'add' => 'Add Player',
        'iwfId' => 'IWF ID',
        'firstName' => 'First Name',
        'lastName' => 'Last Name',
        'rar' => 'RAR',
        'competitions' => 'Comps',
        'best' => 'Best Place',
        'worst' => 'Worst Place',
        'armies' => 'Armies Used',
        'actions' => 'Actions',
        'update' => 'Update',
        'delete' => 'Delete',
        'results' => 'Results',
        'matches' => 'Matches',
        'na' => 'N/A',
        'none' => 'No players found for this initial.',
    ],
    'italian' => [
        'list' => 'Elenco',
        'fullList' => 'Elenco completo',
        'add' => 'Aggiungi giocatore',
        'iwfId' => 'IWF ID',
        'firstName' => 'Nome',
        'lastName' => 'Cognome',
        'rar' => 'RAR',
        'competitions' => 'Concorsi',
        'best' => 'Miglior piazzamento',
        'worst' => 'Peggior piazzamento',
        'armies' => 'Eserciti usati',
        'actions' => 'Azioni',
        'update' => 'Aggiorna',
        'delete' => 'Elimina',
        'results' => 'Risultati',
        'matches' => 'Partite',
        'na' => 'N/D',
        'none' => 'Nessun giocatore trovato per questa iniziale.',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h2><?= esc($text['list']) ?></h2>
    <nav aria-label="<?= esc($text['list'], 'attr') ?>">
        <ul class="pagination">
            <?php foreach ($initials as $item): ?>
                <?php $letter = (string) $item['initial']; ?>
                <li class="<?= $initial === $letter ? 'active' : '' ?>">
                    <a href="<?= $initial === $letter ? '#' : site_url('player/playerList/' . rawurlencode($letter)) ?>"
                       <?= $initial === $letter ? 'aria-current="page"' : '' ?>>
                        <?= esc($letter) ?>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </nav>
    <?php if ($canAddPlayer): ?>
        <p>
            <a class="btn btn-default" href="<?= site_url('player/add') ?>">
                <i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i> <?= esc($text['add']) ?>
            </a>
        </p>
    <?php endif ?>
    <p><a class="btn btn-default" href="<?= site_url('player/playerListFull') ?>"><?= esc($text['fullList']) ?></a></p>

    <div class="table-responsive">
        <table class="table table-striped" id="allPlayerTable">
            <thead>
            <tr>
                <th><?= esc($text['iwfId']) ?></th>
                <th><?= esc($text['firstName']) ?></th>
                <th><?= esc($text['lastName']) ?></th>
                <th><?= esc($text['rar']) ?></th>
                <th class="text-right"><?= esc($text['competitions']) ?></th>
                <th><?= esc($text['best']) ?></th>
                <th><?= esc($text['worst']) ?></th>
                <th class="text-right"><?= esc($text['armies']) ?></th>
                <th><?= esc($text['actions']) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($players as $player): ?>
                <?php
                $rarTag = $player['rar_tag'] ?? 'XI';
                $flag = $rarTag === 'XI' ? '_united-nations.png' : $rarTag . '.png';
                $flagPath = FCPATH . 'images/flags24/' . $flag;
                if (! is_file($flagPath)) {
                    $flag = '_united-nations.png';
                }
                $playerUrl = site_url('player/details/' . (int) $player['competitor_id']);
                ?>
                <tr id="playerRow<?= (int) $player['competitor_id'] ?>">
                    <td><?= esc($player['iwf_id'] ?? '') ?></td>
                    <td><a href="<?= $playerUrl ?>"><?= esc($player['first_name']) ?></a></td>
                    <td><a href="<?= $playerUrl ?>"><?= esc($player['last_name']) ?></a></td>
                    <td>
                        <img class="player-flag" src="<?= base_url('images/flags24/' . rawurlencode($flag)) ?>"
                             alt="<?= esc($player['rar_name']) ?>">
                        <?= esc($player['rar_name']) ?>
                    </td>
                    <td class="text-right"><?= (int) $player['num_comps'] ?></td>
                    <?php if ($player['num_comps'] > 0): ?>
                        <td><?= (int) $player['best_place'] ?> out of <?= (int) $player['best_place_from'] ?></td>
                        <td><?= (int) $player['worst_place'] ?> out of <?= (int) $player['worst_place_from'] ?></td>
                    <?php else: ?>
                        <td><?= esc($text['na']) ?></td>
                        <td><?= esc($text['na']) ?></td>
                    <?php endif ?>
                    <td class="text-right"><?= (int) $player['num_armies'] ?></td>
                    <td class="player-actions">
                        <?php if ($canUpdatePlayer): ?>
                            <a class="btn btn-default" href="<?= site_url('player/edit/' . (int) $player['competitor_id']) ?>">
                                <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i> <?= esc($text['update']) ?>
                            </a>
                            <a class="btn btn-default btn-danger" href="<?= site_url('player/delete/' . (int) $player['competitor_id']) ?>">
                                <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i> <?= esc($text['delete']) ?>
                            </a>
                        <?php endif ?>
                        <a class="btn btn-default" href="<?= site_url('player/results/' . (int) $player['competitor_id']) ?>">
                            <i class="fa fa-trophy fa-lg" aria-hidden="true"></i> <?= esc($text['results']) ?>
                        </a>
                        <a class="btn btn-default" href="<?= site_url('player/matches/' . (int) $player['competitor_id']) ?>">
                            <i class="fa fa-handshake-o fa-lg" aria-hidden="true"></i> <?= esc($text['matches']) ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php if ($players === []): ?>
                <tr><td colspan="9"><?= esc($text['none']) ?></td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
