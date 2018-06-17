<?php

/** @var $app Slim\App */


$app->get('/products', function(\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
    /** @var  $productController \Controller\ProductController */
    $productController = $this->get('ProductController');

    $limit = $request->getParam('limit', 20);
    $offset = $request->getParam('offset', 0);
    $brands = $request->getParam('brands', []);
    $stores = $request->getParam('stores', []);
    $searchCriteria = $request->getParam('search', '');
    $priceFrom = $request->getParam('priceFrom');
    $priceTo = $request->getParam('priceTo');

    $priceFromTo = [];
    if ($priceFrom) {
        $priceFromTo['from'] = $priceFrom;
    }
    if ($priceTo) {
        $priceFromTo['to'] = $priceTo;
    }

    $responseData = $productController->getProducts($limit, $offset, $brands, $stores, $searchCriteria, $priceFromTo);

    echo json_encode($responseData);
    die;
})->add(\Middleware\ListMiddleware::class);
