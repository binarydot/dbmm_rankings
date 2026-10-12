<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'directory' => 'Active Armies',
        'mostUsed' => 'Most Used Armies',
        'mostUsed12' => 'Most Used Armies in the Last 12 Months',
        'used' => 'Armies Used',
        'used12' => 'Armies Used in the Last 12 Months',
        'firstUsed' => 'First Use of Armies',
        'book' => 'Book',
        'list' => 'List',
        'name' => 'Name',
        'version' => 'Rules Version',
        'years' => 'Years',
        'noArmies' => 'No active armies are available.',
    ],
    'italian' => [
        'directory' => 'Eserciti attivi',
        'mostUsed' => 'Eserciti più utilizzati',
        'mostUsed12' => 'Eserciti più utilizzati negli ultimi 12 mesi',
        'used' => 'Eserciti utilizzati',
        'used12' => 'Eserciti utilizzati negli ultimi 12 mesi',
        'firstUsed' => 'Primo utilizzo degli eserciti',
        'book' => 'Libro',
        'list' => 'Lista',
        'name' => 'Nome',
        'version' => 'Versione regole',
        'years' => 'Anni',
        'noArmies' => 'Nessun esercito attivo disponibile.',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($text['directory']) ?></h1>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['directory'], 'attr') ?>">
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

    <?php if ($armies === []): ?>
        <p><?= esc($text['noArmies']) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th><?= esc($text['book']) ?></th>
                        <th><?= esc($text['list']) ?></th>
                        <th><?= esc($text['name']) ?></th>
                        <th><?= esc($text['version']) ?></th>
                        <th><?= esc($text['years']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($armies as $army): ?>
                        <tr>
                            <td><?= esc((string) $army['book_number']) ?></td>
                            <td><?= esc((string) $army['list_number']) ?></td>
                            <td><a href="<?= site_url('army/listMatches/' . (int) $army['army_id']) ?>"><?= esc($army['name']) ?></a></td>
                            <td><?= esc($army['system_name'] . ' ' . $army['version']) ?></td>
                            <td><?= esc((string) $army['start_year']) ?> - <?= esc((string) $army['end_year']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
