<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DBMM Rankings<?= isset($title) ? ' - ' . esc($title) : '' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-theme.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/red_bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/font-awesome/css/font-awesome.min.css') ?>">
    <style>
        body { padding: 1rem; }
        .ranking-flag { width: 24px; height: 24px; }
        .ranking-cell { min-width: 4rem; text-align: center; }
        .purple { color: #800080; }
        .green { color: #008000; }
        .red { color: #c00; }
        .blue { color: #00f; }
    </style>
</head>
<body class="body">
    <header class="container-fluid">
        <div class="row banner">
            <h1 class="inner_banner">DBMM Rankings<?= isset($title) ? ' - ' . esc($title) : '' ?></h1>
        </div>
    </header>
    <?= view('legacy/menu', [
        'menuItem' => 'rankings',
        'legacySession' => new \App\Libraries\LegacySession(service('session')),
    ]) ?>
    <main>
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
