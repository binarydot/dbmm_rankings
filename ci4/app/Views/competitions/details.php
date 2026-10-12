<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'list' => 'List', 'details' => 'Details', 'results' => 'Results', 'matches' => 'Matches',
        'edit' => 'Edit', 'delete' => 'Delete', 'id' => 'ID', 'name' => 'Name',
        'series' => 'Series Name', 'location' => 'Location', 'date' => 'Competition Date',
        'type' => 'Type', 'protected' => 'Protected', 'weighting' => 'Weighting',
        'glicko' => 'Eligible for Glicko', 'region' => 'Country / Region',
        'competitors' => 'Number of Competitors / Teams', 'rounds' => 'Rounds',
        'system' => 'System', 'scale' => 'Scale', 'armyPoints' => 'Army Points',
        'lists' => 'Number of Lists Allowed', 'fractional' => '(fractional if players may substitute part of the list)',
        'scoring' => 'Scoring System', 'format' => 'Format', 'theme' => 'Theme', 'notes' => 'Notes',
        'yes' => 'Yes', 'no' => 'No',
    ],
    'italian' => [
        'list' => 'Elenco', 'details' => 'Dettagli', 'results' => 'Risultati', 'matches' => 'Partite',
        'edit' => 'Modifica', 'delete' => 'Elimina', 'id' => 'ID', 'name' => 'Nome',
        'series' => 'Serie', 'location' => 'Luogo', 'date' => 'Data del concorso',
        'type' => 'Tipo', 'protected' => 'Protetto', 'weighting' => 'Peso',
        'glicko' => 'Idoneo per Glicko', 'region' => 'Paese / Regione',
        'competitors' => 'Numero di concorrenti / squadre', 'rounds' => 'Turni',
        'system' => 'Sistema', 'scale' => 'Scala', 'armyPoints' => 'Punti esercito',
        'lists' => 'Numero di liste consentite', 'fractional' => '(frazionale se è possibile sostituire parte della lista)',
        'scoring' => 'Sistema di punteggio', 'format' => 'Formato', 'theme' => 'Tema', 'notes' => 'Note',
        'yes' => 'Sì', 'no' => 'No',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$id = (int) $competition['competition_id'];
$fields = [
    'id' => $id,
    'name' => $competition['competition_name'],
    'series' => $competition['competition_series_name'],
    'location' => $competition['location'],
    'date' => $competition['competition_date'],
    'type' => $competition['type'],
    'protected' => (int) $competition['protected'] === 1 ? $text['yes'] : $text['no'],
    'weighting' => $competition['weighting'],
    'glicko' => (int) $competition['glicko_eligible'] === 1 ? $text['yes'] : $text['no'],
    'region' => $competition['rar_name'],
    'competitors' => $competition['num_competitors'],
    'rounds' => $competition['rounds'],
    'system' => trim($competition['system_name'] . ' ' . $competition['version']),
    'scale' => $competition['scale_desc'],
    'armyPoints' => $competition['army_points'],
    'lists' => $competition['lists_allowed'],
    'scoring' => $competition['scoring_system_desc'],
    'format' => $competition['format_desc'],
    'theme' => $competition['theme_desc'],
    'notes' => $competition['notes'],
];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <nav class="navbar navbar-default" aria-label="<?= esc($text['details'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('competition') ?>"><?= esc($text['list']) ?></a></li>
                <li class="active"><a href="<?= site_url('competition/details/' . $id) ?>" aria-current="page"><?= esc($text['details']) ?></a></li>
                <li><a href="<?= site_url('competition/results/' . $id) ?>"><?= esc($text['results']) ?></a></li>
                <li><a href="<?= site_url('competition/matches/' . $id) ?>"><?= esc($text['matches']) ?></a></li>
            </ul>
            <?php if ($canUpdate): ?>
                <a class="btn btn-default navbar-btn" href="<?= site_url('competition/edit/' . $id) ?>"><?= esc($text['edit']) ?></a>
                <a class="btn btn-default btn-danger navbar-btn" href="<?= site_url('competition/delete/' . $id) ?>"><?= esc($text['delete']) ?></a>
            <?php endif ?>
        </div>
    </nav>
    <h2><?= esc($competition['competition_name']) ?></h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <?php foreach ($fields as $key => $value): ?>
                    <tr>
                        <th scope="row"><?= esc($text[$key]) ?></th>
                        <td><?= esc((string) ($value ?? '')) ?>
                            <?php if ($key === 'lists'): ?> <small><?= esc($text['fractional']) ?></small><?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
