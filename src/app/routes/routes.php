<?php

/** @var $app Slim\App */


$app->get('/products', function(\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
    /** @var  $productController \Controller\ProductController */
    $productController = $this->get('ProductController');

    $limit = $request->getParam('limit', 20);
    $offset = $request->getParam('offset', 0);
    $brands = $request->getParam('brands', '');
    $brands = $brands !== '' ? explode(',', $brands) : [];
    $stores = $request->getParam('stores', '');
    $stores = $stores !== '' ? explode(',', $stores) : [];
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

    return $response->withJson($responseData);
})->add(\Middleware\ListMiddleware::class);

$app->get('/brands', function(\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
    /** @var  $brandController \Controller\BrandController */
    $brandController = $this->get('BrandController');

    $responseData = $brandController->getAllBrands();

    return $response->withJson($responseData);
});

$app->put('/product/{id}', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
    /** @var  $productController \Controller\ProductController */
    $productController = $this->get('ProductController');

    $product = $request->getParam('product');
    $productController->updateProduct($product);

    return true;
});

$app->delete('/product/{id}', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
    /** @var  $productController \Controller\ProductController */
    $productController = $this->get('ProductController');

    $productId = $args['id'];
    $productController->deleteProduct($productId);

    return true;
});

$app->get('/stores', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
    /** @var  $productController \Controller\ProductController */
    $productController = $this->get('ProductController');

    $responseData = $productController->getAllStores();

    return $response->withJson($responseData);
});
