<?php

use Psr7Middlewares\Middleware\TrailingSlash;

$app->add(new TrailingSlash(false));

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST');
});
