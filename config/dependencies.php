<?php
$container = $app->getContainer();
// database
$container['db'] = function ($c) {
    $config = $c->get('settings')['db'];
    $db =  new \Medoo\Medoo([
        // required
        'database_type' => 'mysql',
        'database_name' => $config['database'],
        'server' => $config['host'],
        'username' => $config['username'],
        'password' => $config['password'],
        //optional
        'port' => $config['port'],
    ]);
    return $db;
};