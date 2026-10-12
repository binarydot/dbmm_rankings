<?php
$labels = [
    'english' => [
        'rankings' => 'Rankings',
        'glicko' => 'Glicko',
        'players' => 'Players',
        'competitions' => 'Competitions',
        'armies' => 'Armies',
        'stats' => 'Stats',
        'admin' => 'Admin',
        'logout' => 'Log Out',
    ],
    'italian' => [
        'rankings' => 'Classifiche',
        'glicko' => 'Glicko',
        'players' => 'Giocatori',
        'competitions' => 'Concorsi',
        'armies' => 'Eserciti',
        'stats' => 'Statistiche',
        'admin' => 'Amministrazione',
        'logout' => 'Esci',
    ],
];
$menuLabels = $labels[$siteLanguage] ?? $labels['english'];
$items = [
    'rankings' => ['rankings/comps', 'fa-star'],
    'glicko' => ['rankings/glicko', 'fa-line-chart'],
    'players' => ['player', 'fa-users'],
    'competitions' => ['competition', 'fa-trophy'],
    'armies' => ['army', 'fa-shield'],
    'stats' => ['matches', 'fa-pie-chart'],
];
?>
<div class="row container-fluid">
    <nav id="main_menubar" aria-label="Main navigation">
        <ul class="nav nav-tabs" id="main_menu_items">
            <?php foreach ($items as $item => [$route, $icon]): ?>
                <li class="<?= $menuItem === $item ? 'active' : '' ?>" id="menuitem_<?= esc($item, 'attr') ?>">
                    <a href="<?= site_url($route) ?>"<?= $menuItem === $item ? ' aria-current="page"' : '' ?>>
                        <i class="fa <?= esc($icon, 'attr') ?> fa-lg" aria-hidden="true"></i>
                        <?= esc($menuLabels[$item]) ?>
                    </a>
                </li>
            <?php endforeach ?>
            <?php if ($loggedIn): ?>
                <li class="<?= $menuItem === 'admin' ? 'active' : '' ?>" id="menuitem_admin">
                    <a href="<?= site_url('admin') ?>"<?= $menuItem === 'admin' ? ' aria-current="page"' : '' ?>>
                        <i class="fa fa-wrench fa-lg" aria-hidden="true"></i>
                        <?= esc($menuLabels['admin']) ?>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('logoff') ?>">
                        <i class="fa fa-sign-out fa-lg" aria-hidden="true"></i>
                        <?= esc($menuLabels['logout']) ?>
                    </a>
                </li>
            <?php else: ?>
                <li class="<?= $menuItem === 'login' ? 'active' : '' ?>" id="menuitem_login">
                    <a href="<?= site_url('login') ?>"<?= $menuItem === 'login' ? ' aria-current="page"' : '' ?>>
                        <i class="fa fa-wrench fa-lg" aria-hidden="true"></i>
                        <?= esc($menuLabels['admin']) ?>
                    </a>
                </li>
            <?php endif ?>
            <li id="menuitem_forum">
                <a href="https://dbmm.org.uk/forum" target="_blank" rel="noopener noreferrer">Forum</a>
            </li>
        </ul>
    </nav>
</div>
