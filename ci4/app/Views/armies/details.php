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
        'startYear' => 'Start Year',
        'endYear' => 'End Year',
        'matches' => 'View Match History',
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
        'startYear' => 'Anno iniziale',
        'endYear' => 'Anno finale',
        'matches' => 'Visualizza partite',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($army['name']) ?></h1>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['directory'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('army/listArmies') ?>"><?= esc($text['directory']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/mostused/0/none') ?>"><?= esc($text['mostUsed']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/mostused/12/none') ?>"><?= esc($text['mostUsed12']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/booklistorder/0/none') ?>"><?= esc($text['used']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/booklistorder/12/none') ?>"><?= esc($text['used12']) ?></a></li>
                <li><a href="<?= site_url('army/armyStats/firstused/0/none') ?>"><?= esc($text['firstUsed']) ?></a></li>
            </ul>
        </div>
    </nav>

    <dl class="dl-horizontal">
        <dt><?= esc($text['name']) ?></dt>
        <dd><?= esc($army['name']) ?></dd>
        <dt><?= esc($text['book']) ?></dt>
        <dd><?= esc((string) $army['book_number']) ?></dd>
        <dt><?= esc($text['list']) ?></dt>
        <dd><?= esc((string) $army['list_number']) ?></dd>
        <dt><?= esc($text['version']) ?></dt>
        <dd><?= esc(trim($army['system_name'] . ' ' . $army['version'])) ?></dd>
        <dt><?= esc($text['startYear']) ?></dt>
        <dd><?= esc((string) $army['start_year']) ?></dd>
        <dt><?= esc($text['endYear']) ?></dt>
        <dd><?= esc((string) $army['end_year']) ?></dd>
    </dl>
    <p><a class="btn btn-default" href="<?= site_url('army/listMatches/' . (int) $army['army_id']) ?>"><?= esc($text['matches']) ?></a></p>
</div>
<?= $this->endSection() ?>
