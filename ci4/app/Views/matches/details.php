<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'results' => 'Match Results',
        'details' => 'Match Details',
        'annual' => 'Annual Trends',
        'year' => 'Year',
        'download' => 'Download CSV of data',
        'system' => 'System',
        'date' => 'Date',
        'competition' => 'Competition',
        'round' => 'Round',
        'player' => 'Player',
        'army' => 'Army Name',
        'score' => 'Score',
        'none' => 'No matches found for this year.',
    ],
    'italian' => [
        'results' => 'Risultati partite',
        'details' => 'Dettagli partite',
        'annual' => 'Andamento annuale',
        'year' => 'Anno',
        'download' => 'Scarica dati CSV',
        'system' => 'Sistema',
        'date' => 'Data',
        'competition' => 'Concorso',
        'round' => 'Turno',
        'player' => 'Giocatore',
        'army' => 'Nome esercito',
        'score' => 'Punteggio',
        'none' => 'Nessuna partita trovata per questo anno.',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($text['details']) ?></h1>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['details'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('matches') ?>"><?= esc($text['results']) ?></a></li>
                <li class="active"><a href="<?= site_url('matches/matchesDetails/' . $year) ?>" aria-current="page"><?= esc($text['details']) ?></a></li>
                <li><a href="<?= site_url('matches/annualTrends') ?>"><?= esc($text['annual']) ?></a></li>
            </ul>
            <form class="navbar-form navbar-left" method="get" action="<?= site_url('matches/matchesDetails') ?>">
                <label for="match-year"><?= esc($text['year']) ?></label>
                <select id="match-year" class="form-control" onchange="window.location.href=this.form.action + '/' + this.value">
                    <?php foreach ($years as $optionYear): ?>
                        <option value="<?= esc((string) $optionYear, 'attr') ?>"<?= $year === $optionYear ? ' selected' : '' ?>><?= esc((string) $optionYear) ?></option>
                    <?php endforeach ?>
                </select>
            </form>
            <a class="btn btn-default navbar-btn" href="<?= site_url('matches/matchesDetailsCSV/' . $year) ?>"><?= esc($text['download']) ?></a>
        </div>
    </nav>

    <?php if ($matches === []): ?>
        <p><?= esc($text['none']) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th><?= esc($text['system']) ?></th>
                        <th><?= esc($text['date']) ?></th>
                        <th><?= esc($text['competition']) ?></th>
                        <th><?= esc($text['round']) ?></th>
                        <th><?= esc($text['player']) ?></th>
                        <th><?= esc($text['army']) ?></th>
                        <th><?= esc($text['score']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <?php $playerScore = $match['side'] === 'W' ? $match['winner_score'] : $match['loser_score']; ?>
                        <?php $opponentScore = $match['side'] === 'W' ? $match['loser_score'] : $match['winner_score']; ?>
                        <tr>
                            <td><?= esc($match['system_name'] . ' v' . $match['version']) ?></td>
                            <td><?= esc($match['competition_date']) ?></td>
                            <td><a href="<?= site_url('competition/matches/' . (int) $match['competition_id']) ?>"><?= esc($match['competition_name']) ?></a></td>
                            <td><?= esc((string) $match['round']) ?></td>
                            <td><a href="<?= site_url('player/details/' . (int) $match['competitor_id']) ?>"><?= esc(trim($match['first_name'] . ' ' . $match['last_name'])) ?></a></td>
                            <td><a href="<?= site_url('army/listMatches/' . (int) $match['army_id']) ?>"><?= esc(trim($match['army_name'] . ' ' . $match['army_system'] . ' v' . $match['army_version'])) ?></a></td>
                            <td><?= esc(number_format((float) $playerScore)) ?> - <?= esc(number_format((float) $opponentScore)) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
