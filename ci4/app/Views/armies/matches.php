<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'mostUsed' => 'Most Used Armies',
        'mostUsed12' => 'Most Used Armies in the Last 12 Months',
        'used' => 'Armies Used',
        'used12' => 'Armies Used in the Last 12 Months',
        'firstUsed' => 'First Use of Armies',
        'date' => 'Date',
        'competition' => 'Competition',
        'round' => 'Round',
        'players' => 'Player(s)',
        'score' => 'Score',
        'opponents' => 'Opponent(s)',
        'opposingArmy' => 'Opposing Army',
        'noMatches' => 'No match history is available for this army.',
        'unknown' => 'N/A',
    ],
    'italian' => [
        'mostUsed' => 'Eserciti più utilizzati',
        'mostUsed12' => 'Eserciti più utilizzati negli ultimi 12 mesi',
        'used' => 'Eserciti utilizzati',
        'used12' => 'Eserciti utilizzati negli ultimi 12 mesi',
        'firstUsed' => 'Primo utilizzo degli eserciti',
        'date' => 'Data',
        'competition' => 'Concorso',
        'round' => 'Turno',
        'players' => 'Giocatore/i',
        'score' => 'Punteggio',
        'opponents' => 'Avversario/i',
        'opposingArmy' => 'Esercito avversario',
        'noMatches' => 'Non sono disponibili partite per questo esercito.',
        'unknown' => 'N/D',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($army['name']) ?></h1>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['used'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('army/armyStats/mostused/0/none') ?>"><?= esc($text['mostUsed']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/mostused/12/none') ?>"><?= esc($text['mostUsed12']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/booklistorder/0/none') ?>"><?= esc($text['used']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/booklistorder/12/none') ?>"><?= esc($text['used12']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/firstused/0/none') ?>"><?= esc($text['firstUsed']) ?></a></li>
            </ul>
        </div>
    </nav>

    <?php if ($matches === []): ?>
        <p><?= esc($text['noMatches']) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th><?= esc($text['date']) ?></th>
                        <th><?= esc($text['competition']) ?></th>
                        <th><?= esc($text['round']) ?></th>
                        <th><?= esc($text['players']) ?></th>
                        <th><?= esc($text['score']) ?></th>
                        <th><?= esc($text['opponents']) ?></th>
                        <th><?= esc($text['opposingArmy']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr>
                            <td><?= esc($match['competition_date']) ?></td>
                            <td>
                                <a href="<?= site_url('competition/matches/' . $match['competition_id']) ?>">
                                    <?= esc($match['competition_name']) ?>
                                </a>
                            </td>
                            <td><?= esc((string) $match['round']) ?></td>
                            <td>
                                <?php foreach ($match['players'] as $player): ?>
                                    <a href="<?= site_url('player/details/' . $player['id']) ?>"><?= esc($player['name']) ?></a><br>
                                <?php endforeach ?>
                            </td>
                            <td class="text-center"><?= esc($match['score']) ?></td>
                            <td>
                                <?php foreach ($match['opponents'] as $opponent): ?>
                                    <a href="<?= site_url('player/details/' . $opponent['id']) ?>"><?= esc($opponent['name']) ?></a><br>
                                <?php endforeach ?>
                            </td>
                            <td>
                                <?php if ($match['other_army'] === null): ?>
                                    <?= esc($text['unknown']) ?>
                                <?php else: ?>
                                    <a href="<?= site_url('army/listMatches/' . $match['other_army']['id']) ?>"><?= esc($match['other_army']['name']) ?></a>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
