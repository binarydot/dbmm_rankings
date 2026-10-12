<?php

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);

$localPathsFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Paths.php';
$hostingerPathsFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'ci4' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Paths.php';
$pathsFile = is_file($localPathsFile) ? $localPathsFile : $hostingerPathsFile;

if (! is_file($pathsFile)) {
    throw new RuntimeException('CodeIgniter Paths.php was not found in the local or Hostinger deployment location.');
}

require $pathsFile;

$paths = new Config\Paths();

require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'Boot.php';

exit(CodeIgniter\Boot::bootWeb($paths));
