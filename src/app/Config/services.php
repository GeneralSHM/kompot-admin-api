<?php

/** @var $app \Slim\App */

$container = $app->getContainer();

$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        /** @var \Exception $exception */

        $debug = $request->getHeader('debugg');
        if (isset($debug[0]) == 1) {
            dd($exception->getMessage(), $exception->getTraceAsString());
        }

        if ($exception instanceof \Illuminate\Database\QueryException) {
            $code = 400;
            $message = $exception->getMessage();//'Something went wrong';
        } else {
            $code = $exception->getCode();
            $message = $exception->getMessage();
        }

        $data = [
            'message' => $message
        ];

        return $container['response']->withStatus($code)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withJson($data);

    };
};

$container['notFoundHandler'] = function($container) {
    return function ($request, $response) use ($container) {
        return $container['response']
            ->withStatus(404);
    };
};

$container['db'] = function ($container) {

    $capsule = new \Illuminate\Database\Capsule\Manager();
    $capsule->addConnection($container['settings']['db']);

    $capsule->bootEloquent();
    $capsule->setAsGlobal();

    return $capsule;
};
