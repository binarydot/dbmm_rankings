<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'region' => 'Country / Region', 'series' => 'Competition Series',
        'year' => 'Year', 'all' => 'All', 'current' => 'Current',
        'name' => 'Name', 'date' => 'Date', 'weighting' => 'Wht',
        'teams' => 'Teams', 'players' => 'Players', 'rar' => 'Region',
        'actions' => 'Actions', 'notes' => 'Notes', 'results' => 'Results', 'runners' => 'Runners',
        'matches' => 'Matches', 'update' => 'Update', 'delete' => 'Delete',
        'add' => 'Add Competition', 'none' => 'No competitions found',
    ],
    'italian' => [
        'region' => 'Paese / Regione', 'series' => 'Serie del concorso',
        'year' => 'Anno', 'all' => 'Tutti', 'current' => 'Attuale',
        'name' => 'Nome', 'date' => 'Data', 'weighting' => 'Peso',
        'teams' => 'Squadre', 'players' => 'Giocatori', 'rar' => 'Regione',
        'actions' => 'Azioni', 'notes' => 'Note', 'results' => 'Risultati', 'runners' => 'Partecipanti',
        'matches' => 'Partite', 'update' => 'Aggiorna', 'delete' => 'Elimina',
        'add' => 'Aggiungi concorso', 'none' => 'Nessun concorso trovato',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
$yearOptions = range(2007, (int) date('Y'));
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <form class="form-inline" id="listComps" action="<?= site_url('competition/competitionList') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="rar"><?= esc($text['region']) ?></label>
            <select class="form-control" name="rar" id="rar" onchange="this.form.submit()">
                <option value="0" <?= $regionId === 0 ? 'selected' : '' ?>><?= esc($text['all']) ?></option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?= (int) $region['rar_id'] ?>" <?= $regionId === (int) $region['rar_id'] ? 'selected' : '' ?>><?= esc($region['rar_name']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-group">
            <label for="series"><?= esc($text['series']) ?></label>
            <select class="form-control" name="series" id="series" onchange="this.form.submit()">
                <option value="0" <?= $seriesId === 0 ? 'selected' : '' ?>><?= esc($text['all']) ?></option>
                <?php foreach ($series as $item): ?>
                    <option value="<?= (int) $item['id'] ?>" <?= $seriesId === (int) $item['id'] ? 'selected' : '' ?>><?= esc($item['name']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year"><?= esc($text['year']) ?></label>
            <select class="form-control" name="year" id="year" onchange="this.form.submit()">
                <option value="-1" <?= $year === -1 ? 'selected' : '' ?>><?= esc($text['current']) ?></option>
                <option value="0" <?= $year === 0 ? 'selected' : '' ?>><?= esc($text['all']) ?></option>
                <?php foreach ($yearOptions as $yearOption): ?>
                    <option value="<?= $yearOption ?>" <?= $year === $yearOption ? 'selected' : '' ?>><?= $yearOption ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <?php if ($canAddCompetition): ?>
            <a class="btn btn-default" href="<?= site_url('competition/add') ?>"><?= esc($text['add']) ?></a>
        <?php endif ?>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th><?= esc($text['name']) ?></th>
                    <th><?= esc($text['date']) ?></th>
                    <th class="text-right"><?= esc($text['weighting']) ?></th>
                    <th class="text-right"><?= esc($text['teams']) ?></th>
                    <th class="text-right"><?= esc($text['players']) ?></th>
                    <th><?= esc($text['rar']) ?></th>
                    <th colspan="<?= $canUpdateCompetition ? 4 : 2 ?>"><?= esc($text['actions']) ?></th>
                    <?php if ($loggedIn): ?><th><?= esc($text['notes']) ?></th><?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($competitions === []): ?>
                    <tr><td colspan="<?= 8 + ($canUpdateCompetition ? 2 : 0) + ($loggedIn ? 1 : 0) ?>"><?= esc($text['none']) ?></td></tr>
                <?php else: ?>
                    <?php foreach ($competitions as $competition): ?>
                        <?php
                        $hasSeriesAccess = isset($seriesPermissions[$competition['series_id']]);
                        $canManage = $canUpdateCompetition && $hasSeriesAccess;
                        ?>
                        <tr>
                            <td><a href="<?= site_url('competition/details/' . (int) $competition['competition_id']) ?>"><?= esc($competition['competition_name']) ?></a></td>
                            <td><?= esc($competition['competition_date']) ?></td>
                            <td class="text-right"><?= esc((string) $competition['weighting']) ?></td>
                            <td class="text-right"><?= esc((string) $competition['entrants']) ?></td>
                            <td class="text-right"><?= esc((string) $competition['players']) ?></td>
                            <td><?= esc($competition['rar_name']) ?></td>
                            <td><a class="btn btn-default" href="<?= site_url('competition/results/' . (int) $competition['competition_id']) ?>">
                                <?php if ($competition['runners']): ?>
                                    <?= esc($text['runners']) ?>
                                <?php else: ?>
                                    <i class="fa fa-trophy fa-lg" aria-hidden="true"></i> <?= esc($text['results']) ?>
                                <?php endif ?>
                            </a></td>
                            <td><a class="btn btn-default" href="<?= site_url('competition/matches/' . (int) $competition['competition_id']) ?>"><i class="fa fa-handshake-o fa-lg" aria-hidden="true"></i> <?= esc($text['matches']) ?></a></td>
                            <?php if ($canUpdateCompetition): ?>
                                <td>
                                    <?php if ($canManage): ?>
                                        <a class="btn btn-default" href="<?= site_url('competition/edit/' . (int) $competition['competition_id']) ?>"><i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i> <?= esc($text['update']) ?></a>
                                    <?php else: ?>
                                        <span class="btn btn-default disabled"><?= esc($text['update']) ?></span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <?php if ($canManage): ?>
                                        <a class="btn btn-default btn-danger" href="<?= site_url('competition/delete/' . (int) $competition['competition_id']) ?>"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i> <?= esc($text['delete']) ?></a>
                                    <?php else: ?>
                                        <span class="btn btn-default btn-danger disabled"><?= esc($text['delete']) ?></span>
                                    <?php endif ?>
                                </td>
                            <?php endif ?>
                            <?php if ($loggedIn): ?><td class="error"><?= esc($competition['notes']) ?></td><?php endif ?>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
