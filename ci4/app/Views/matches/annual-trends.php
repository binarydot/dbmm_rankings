<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'results' => 'Match Results',
        'details' => 'Match Details',
        'annual' => 'Annual Trends',
        'year' => 'Year',
        'players' => 'Players',
        'matches' => 'Matches',
        'newPlayers' => 'New Players',
        'none' => 'No annual statistics are available.',
    ],
    'italian' => [
        'results' => 'Risultati partite',
        'details' => 'Dettagli partite',
        'annual' => 'Andamento annuale',
        'year' => 'Anno',
        'players' => 'Giocatori',
        'matches' => 'Partite',
        'newPlayers' => 'Nuovi giocatori',
        'none' => 'Non sono disponibili statistiche annuali.',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($text['annual']) ?></h1>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['annual'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('matches') ?>"><?= esc($text['results']) ?></a></li>
                <li><a href="<?= site_url('matches/matchesDetails/' . date('Y')) ?>"><?= esc($text['details']) ?></a></li>
                <li class="active"><a href="<?= site_url('matches/annualTrends') ?>" aria-current="page"><?= esc($text['annual']) ?></a></li>
            </ul>
        </div>
    </nav>

    <?php if ($trends === []): ?>
        <p><?= esc($text['none']) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th><?= esc($text['year']) ?></th>
                        <th><?= esc($text['players']) ?></th>
                        <th><?= esc($text['matches']) ?></th>
                        <th><?= esc($text['newPlayers']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trends as $year => $trend): ?>
                        <tr>
                            <th scope="row"><?= esc((string) $year) ?></th>
                            <td><?= esc((string) $trend['players']) ?></td>
                            <td><?= esc((string) $trend['matches']) ?></td>
                            <td><?= esc((string) $trend['newbies']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
