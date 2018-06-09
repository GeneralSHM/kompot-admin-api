<?php

try {
    require_once __DIR__ . '/../Support/AutoLoader.php';
    AutoLoader::init();
    require_once __DIR__ . '/../../../vendor/autoload.php';

    require_once __DIR__ . '/../Config/config.php';

    require_once __DIR__ . '/../Support/helpers.php';
    /** @var $config array */

    $app = new \Slim\App($config);

    $app->add(\Middleware\UserTypeResponseMiddleware::class);

    $app->add(function ($req, $res, $next) {
        /** @var \Slim\Http\Response $res */
        /** @var \Slim\Http\Response $response */
        $response = $res
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        return $next($req, $response);
    });

    $app->add(\Middleware\JsonResponseMiddleware::class);

    require_once __DIR__ . '/../Config/services.php';
    require_once __DIR__ . '/../routes/routes.php';

    $app->getContainer()->get('db');

    $app->run();

} catch (\Exception $e) {
    dd($e->getMessage());
}
