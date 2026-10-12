<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'intro1' => 'Kudos is an experimental ranking system combining Glicko ratings with competition results.',
        'intro2' => 'Competition weighting depends on the number and ability of the other players attending and the number of rounds. Player ability is based on Glicko ratings at the start of each competition.',
        'dateRange' => 'Date range for this ranking',
        'year' => 'Year',
        'current' => 'Current period',
        'region' => 'Country / Region',
        'allRegions' => 'All regions',
        'rank' => '#',
        'name' => 'Name',
        'score' => 'Score',
        'competitions' => 'Comps',
        'competitionScores' => 'Competition Scores',
        'rankingSettings' => 'Ranking Settings',
        'maximumComps' => 'Maximum Competitions',
        'includedComps' => 'Competitions included in this ranking',
        'date' => 'Date',
        'weighting' => 'Weighting',
        'protected' => 'Protected',
        'yes' => 'Yes',
        'no' => 'No',
        'none' => 'No ranking data is available for this period and region.',
        'ratingTotal' => 'Total Glicko rating at the competition',
        'rounds' => 'Rounds',
    ],
    'italian' => [
        'intro1' => 'Kudos è un sistema sperimentale di classificazione che combina i punteggi Glicko con i risultati dei concorsi.',
        'intro2' => 'Il peso di ogni concorso dipende dal numero e dall’abilità degli altri partecipanti e dal numero di turni.',
        'dateRange' => 'Intervallo date della classifica',
        'year' => 'Anno',
        'current' => 'Periodo attuale',
        'region' => 'Paese / Regione',
        'allRegions' => 'Tutte le regioni',
        'rank' => '#',
        'name' => 'Nome',
        'score' => 'Punteggio',
        'competitions' => 'Concorsi',
        'competitionScores' => 'Punteggi concorsi',
        'rankingSettings' => 'Impostazioni classifica',
        'maximumComps' => 'Numero massimo di concorsi',
        'includedComps' => 'Concorsi inclusi nella classifica',
        'date' => 'Data',
        'weighting' => 'Peso',
        'protected' => 'Protetto',
        'yes' => 'Sì',
        'no' => 'No',
        'none' => 'Nessun dato disponibile per questo periodo e regione.',
        'ratingTotal' => 'Punteggio Glicko totale all’inizio del concorso',
        'rounds' => 'Turni',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$competitions = array_values($rankingsData['competitions'] ?? []);
$compDataTypes = [
    'Protected/Used' => 'purple',
    'Protected/Unused' => 'red',
    'Unprotected/Used' => 'green',
    'Unprotected/Unused' => 'red',
    'Unprotected/Average' => 'blue',
];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1>Kudos Rankings</h1>
    <p><?= esc($text['intro1']) ?></p>
    <p><?= esc($text['intro2']) ?></p>

    <form class="form-inline" method="get" action="<?= site_url('rankings/kudos') ?>">
        <div class="form-group">
            <label for="kudos-year"><?= esc($text['year']) ?></label>
            <select class="form-control" name="year" id="kudos-year">
                <option value="-1"<?= $selectedYear === '-1' ? ' selected' : '' ?>><?= esc($text['current']) ?></option>
                <?php foreach ($years as $year): ?>
                    <option value="<?= esc((string) $year, 'attr') ?>"<?= (int) $selectedYear === $year ? ' selected' : '' ?>><?= esc((string) $year) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-group">
            <label for="kudos-region"><?= esc($text['region']) ?></label>
            <select class="form-control" name="rar" id="kudos-region">
                <option value="-1"<?= $selectedRar < 0 ? ' selected' : '' ?>><?= esc($text['allRegions']) ?></option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?= esc((string) $region->rar_id, 'attr') ?>"<?= $selectedRar === (int) $region->rar_id ? ' selected' : '' ?>><?= esc($region->rar_name) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <button class="btn btn-default" type="submit"><?= esc($text['year']) ?></button>
    </form>

    <p><?= esc($text['dateRange']) ?>: <?= esc((string) $rankingsData['startDate']) ?> - <?= esc((string) $rankingsData['endDate']) ?></p>

    <?php if ($rankings === []): ?>
        <p><?= esc($text['none']) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th><?= esc($text['rank']) ?></th>
                        <th><?= esc($text['name']) ?></th>
                        <th><?= esc($text['score']) ?></th>
                        <th><?= esc($text['competitions']) ?></th>
                        <th><?= esc($text['competitionScores']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rankings as $index => $ranking): ?>
                        <tr>
                            <td><?= esc((string) ($index + 1)) ?></td>
                            <td><a href="<?= site_url('player/details/' . (int) $ranking['playerId']) ?>"><?= esc($ranking['playerName']) ?></a></td>
                            <td class="text-right"><?= esc(number_format((float) $ranking['playerData']['ranking'], 2)) ?></td>
                            <td class="text-center"><?= esc((string) $ranking['playerData']['numComps']) ?></td>
                            <td>
                                <?php foreach ($ranking['playerData']['compData'] as $comp): ?>
                                <?php $class = $compDataTypes[$comp['type'] ?? ''] ?? ''; ?>
                                    <span class="<?= esc($class, 'attr') ?>" title="<?= esc($comp['notes'] ?? '', 'attr') ?>"><?= esc($comp['score'] ?? '') ?></span>
                                <?php endforeach ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <h2><?= esc($text['rankingSettings']) ?></h2>
    <p><?= esc($text['maximumComps']) ?>: <?= esc((string) $rankingsData['minComps']) ?></p>
    <h2><?= esc($text['includedComps']) ?></h2>
    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead><tr><th><?= esc($text['date']) ?></th><th><?= esc($text['name']) ?></th><th><?= esc($text['weighting']) ?></th><th><?= esc($text['protected']) ?></th><th><?= esc($text['ratingTotal']) ?></th><th><?= esc($text['rounds']) ?></th></tr></thead>
            <tbody>
                <?php foreach ($competitions as $competition): ?>
                    <tr>
                        <td><?= esc($competition->competition_date) ?></td>
                        <td><a href="<?= site_url('competition/details/' . (int) $competition->competition_id) ?>"><?= esc($competition->competition_name) ?></a></td>
                        <td><?= esc((string) $competition->weighting) ?></td>
                        <td><?= $competition->protected ? esc($text['yes']) : esc($text['no']) ?></td>
                        <td><?= esc((string) ($rankingsData['totalRating'][$competition->competition_id] ?? 0)) ?></td>
                        <td><?= esc((string) ($rankingsData['rounds'][$competition->competition_id] ?? 0)) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
