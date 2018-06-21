<?php
// Phinx configuration file
require __DIR__.'/vendor/autoload.php';
$config = require __DIR__ . '/config/config.php';
$app = new \Slim\App($config);
require __DIR__ . '/config/dependencies.php';
$db = $app->getContainer()->get('db');
$dbSettings = $app->getContainer()->get('settings')['db'];

return [
    'environments' => [
        'production' => [
            'name'       => $dbSettings['database'],
            'connection' => $db->pdo
        ]
    ],
    'paths' => [
        'migrations' => 'db/migrations'
    ],
];