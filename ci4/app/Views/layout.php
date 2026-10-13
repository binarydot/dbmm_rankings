<!doctype html>
<html lang="<?= session()->get('site_lang') === 'italian' ? 'it' : 'en' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DBMM Rankings<?= isset($title) ? ' - ' . esc($title) : '' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-theme.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/red_bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/font-awesome/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/material.css') ?>">
    <style>
        .language-switcher img { width: 24px; height: auto; }
        .ranking-flag { width: 24px; height: 24px; }
        .ranking-cell { min-width: 4rem; text-align: center; }
        .purple { color: #800080; }
        .green { color: #008000; }
        .red { color: #c00; }
        .blue { color: #00f; }
    </style>
</head>
<body class="body">
    <header class="site-header">
        <div class="site-header__inner">
            <h1 class="site-brand">DBMM Rankings</h1>
            <div class="language-switcher" aria-label="Language selection">
                <a href="<?= site_url('langswitch/switchLanguage/english') ?>" title="English" aria-label="Switch language to English">
                    <img src="<?= base_url('images/gb.gif') ?>" alt="English">
                </a>
                <a href="<?= site_url('langswitch/switchLanguage/italian') ?>" title="Italiano" aria-label="Switch language to Italian">
                    <img src="<?= base_url('images/it.gif') ?>" alt="Italiano">
                </a>
            </div>
        </div>
    </header>
    <?= view('partials/navigation', [
        'menuItem' => $menuItem ?? 'rankings',
        'loggedIn' => (bool) session()->get('loggedIn'),
        'siteLanguage' => session()->get('site_lang') === 'italian' ? 'italian' : 'english',
    ]) ?>
    <main class="page-shell">
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
