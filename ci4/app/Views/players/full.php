<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h2>Full Player List</h2>
    <form class="form-inline" method="get" action="<?= site_url('player/playerListFull') ?>">
        <div class="form-group">
            <label for="player-rar">Country / Region</label>
            <select class="form-control" id="player-rar" name="rar">
                <option value="0"<?= $rarId === 0 ? ' selected' : '' ?>>All</option>
                <?php foreach ($rars as $rar): ?>
                    <option value="<?= esc((string) $rar->rar_id, 'attr') ?>"<?= $rarId === (int) $rar->rar_id ? ' selected' : '' ?>>
                        <?= esc($rar->rar_name) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <button class="btn btn-default" type="submit">Filter</button>
        <?php if ($canAddPlayer): ?>
            <a class="btn btn-default" href="<?= site_url('player/add') ?>">Add a new player</a>
        <?php endif ?>
    </form>
    <p><?= esc((string) $totalPlayers) ?> players</p>

    <div class="table-responsive">
        <table class="table table-striped" id="allPlayerTable">
            <thead>
                <tr>
                    <th>IWF ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>RAR</th>
                    <th>Competitions</th>
                    <th>Best Place</th>
                    <th>Worst Place</th>
                    <th>Armies Used</th>
                    <?php if ($canUpdatePlayer): ?><th>Update</th><th>Delete</th><?php endif ?>
                    <th>Results</th>
                    <th>Matches</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                    <?php $playerId = (int) $player['competitor_id']; ?>
                    <tr id="playerRow<?= $playerId ?>">
                        <td><?= esc($player['iwf_id'] ?? '') ?></td>
                        <td><a href="<?= site_url('player/details/' . $playerId) ?>"><?= esc($player['first_name']) ?></a></td>
                        <td><a href="<?= site_url('player/details/' . $playerId) ?>"><?= esc($player['last_name']) ?></a></td>
                        <td><?= esc($player['rar_name']) ?></td>
                        <td class="text-right"><?= (int) $player['num_comps'] ?></td>
                        <td><?= (int) $player['best_place'] ?></td>
                        <td><?= (int) $player['worst_place'] ?></td>
                        <td class="text-right"><?= (int) $player['num_armies'] ?></td>
                        <?php if ($canUpdatePlayer): ?>
                            <td><a href="<?= site_url('player/edit/' . $playerId) ?>">Update</a></td>
                            <td><a href="<?= site_url('player/delete/' . $playerId) ?>">Delete</a></td>
                        <?php endif ?>
                        <td><a href="<?= site_url('player/results/' . $playerId) ?>">Results</a></td>
                        <td><a href="<?= site_url('player/matches/' . $playerId) ?>">Matches</a></td>
                    </tr>
                <?php endforeach ?>
                <?php if ($players === []): ?>
                    <tr><td colspan="<?= $canUpdatePlayer ? 12 : 10 ?>">No players found.</td></tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Player list pages">
            <ul class="pagination">
                <?php if ($pageNumber > 1): ?>
                    <li><a href="<?= site_url("player/playerListFull/{$rarId}/" . ($pageNumber - 1) . "/{$pageSize}") ?>">Previous</a></li>
                <?php endif ?>
                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <li class="<?= $page === $pageNumber ? 'active' : '' ?>">
                        <a href="<?= site_url("player/playerListFull/{$rarId}/{$page}/{$pageSize}") ?>"
                           <?= $page === $pageNumber ? 'aria-current="page"' : '' ?>><?= $page ?></a>
                    </li>
                <?php endfor ?>
                <?php if ($pageNumber < $totalPages): ?>
                    <li><a href="<?= site_url("player/playerListFull/{$rarId}/" . ($pageNumber + 1) . "/{$pageSize}") ?>">Next</a></li>
                <?php endif ?>
            </ul>
        </nav>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
