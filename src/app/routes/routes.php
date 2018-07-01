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
    $ourPrice = $request->getParam('ourPrice', 0);

    $priceFromTo = [];
    if ($priceFrom) {
        $priceFromTo['from'] = $priceFrom;
    }
    if ($priceTo) {
        $priceFromTo['to'] = $priceTo;
    }

    $responseData = $productController->getProducts($limit, $offset, $brands, $stores, $searchCriteria, $priceFromTo, $ourPrice);

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

$app->post('/brand', function(\Slim\Http\Request $request, \Slim\Http\Response $response, $args){
    $model = new \Model\Brand();
    $model::create([
        'name' => $request->getParam('name')
    ]);

    return true;
});

$app->get('/export', function(\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
    /** @var  $productController \Controller\ProductController */
    $productController = $this->get('ProductController');

    $products = $productController->getAllActiveProducts();

    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    $filename = "data_export_" . date("Y-m-d") . ".csv";
    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");

    ob_start();
    $df = fopen("php://output", 'w');
    fputcsv($df, array_keys(reset($products)));
    foreach ($products as $row) {
        fputcsv($df, $row);
    }
    fclose($df);
    echo ob_get_clean();
    die;
});