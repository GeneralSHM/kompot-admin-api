<?php

/** @var $app Slim\App */

/**
 * Login function
 */
$app->post('/login', function($request, \Slim\Http\Response $response, $args){
    /** @var $authController \Controller\AuthController */
    $authController = $this->get('AuthController');

    echo json_encode($authController->login());
})->add(\Middleware\LoginMiddleware::class)
    ->add(new \Middleware\CreateTokenMiddleware($app->getContainer()));

/**
 * Login function
 */
$app->post('/logout', function($request, \Slim\Http\Response $response, $args){
    /** @var $authController \Controller\AuthController */
    $authController = $this->get('AuthController');

    echo json_encode($authController->logout());
})
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Register function
 */
$app->post('/register', function($request, $response, $args){
    /** @var $authController \Controller\AuthController */
    $authController = $this->get('AuthController');

    echo json_encode($authController->register());
})->add(new \Middleware\Registration\RegisterValidateMiddleware($app->getContainer()))
  ->add(new \Middleware\CreateTokenMiddleware($app->getContainer()));

$app->group('/photo', function() use ($app){

    $app->post('/like', function($request, $response, $args){
        /** @var \Controller\PhotoController $photoController */
        $photoController = $this->get('PhotoController');

        $responseData = $photoController->setRequest($request)
                                        ->likePhoto();

        echo json_encode($responseData);
    })
        ->add(\Middleware\Photo\LikePhotoMiddleware::class)
        ->add(new \Middleware\AuthMiddleware($app->getContainer()));

    $app->post('/comment', function($request, $response, $args){
        /** @var \Controller\PhotoController $photoController */
        $photoController = $this->get('PhotoController');

        $responseData = $photoController->setRequest($request)
            ->commentPhoto();

        echo json_encode($responseData);
    })
        ->add(new \Middleware\Photo\CommentMiddleware($app->getContainer()))
        ->add(new \Middleware\AuthMiddleware($app->getContainer()));
});

/**
 * Edit user url.
 */
$app->post('/user', function($request, $response, $args){
    /** @var \Controller\UserController $userController */
    $userController = $this->get('UserController');
    $response = $userController->setRequest($request)
                               ->editUser();

    echo json_encode($response);
})
    ->add(new \Middleware\User\EditUserMiddleware($app->getContainer()))
    ->add(new \Middleware\Photo\AvatarMiddleware($app->getContainer()))
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Edit user url for admin.
 */
$app->post('/admin/user', function($request, $response, $args){
    /** @var \Controller\UserController $userController */
    $userController = $this->get('UserController');
    $userController->editUserAdmin();

})
    ->add(new \Middleware\User\EditUserAdminMiddleware($app->getContainer()))
    ->add(new \Middleware\Photo\AvatarMiddleware($app->getContainer()))
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Upload photo url.
 */
$app->post('/photo', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PhotoController $photoController */
    $photoController = $this->get('PhotoController');

    $responseData = $photoController->setRequest($request)
        ->uploadPhoto();

})  ->add(new \Middleware\Photo\UploadPhotoMiddleware($app->getContainer()))
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Upload photo admin url.
 */
$app->post('/admin/photo', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PhotoController $photoController */
    $photoController = $this->get('PhotoController');

    $responseData = $photoController->setRequest($request)
                                    ->uploadPhotoAdmin();

})  ->add(new \Middleware\Photo\AdminUploadPhotoMiddleware($app->getContainer()))
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Get hash tag by name or id.
 */
$app->get('/hashtag', function($request, $response, $args){
    /** @var \Controller\HashTagController $hashTagController */
    $hashTagController = $this->get('HashTagController');
    $responseData = $hashTagController->getHashTag();

    echo json_encode($responseData);
})->add(\Middleware\HashTag\HashTagSearchMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Get all users by limit/offset.
 */
$app->get('/users', function($request, $response, $args){
    /** @var \Slim\Http\Request $request */
    /** @var \Controller\UserController $userController */
    $userController = $this->get('UserController');

    $limit = $request->getParam('limit');
    $offset = $request->getParam('offset');

    $username = $request->getParam('username', null);
    $email = $request->getParam('email', null);

    $responseData = $userController->getUsers($limit, $offset, $username, $email);

    echo json_encode($responseData);
})  ->add(\Middleware\ListMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Create partner
 */
$app->put('/admin/partner', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PartnerController $partnerController */
    $partnerController = $this->get('PartnerController');
    $partnerController->createPartner();
})
    ->add(new \Middleware\Partner\CreatePartnerMiddleware($app->getContainer()))
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Edit partner
 */
$app->post('/admin/partner', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PartnerController $partnerController */
    $partnerController = $this->get('PartnerController');
    $partnerController->editPartner();
})
    ->add(new \Middleware\Partner\EditPartnerMiddleware($app->getContainer()))
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Get partners
 */
$app->get('/admin/partner', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PartnerController $partnerController */
    $partnerController = $this->get('PartnerController');

    $limit = $request->getParam('limit');
    $offset = $request->getParam('offset');

    $webPage = (string) $request->getParam('webpage');
    $companyName = (string) $request->getParam('company_name');
    $workFlow = (string) $request->getParam('work_flow');

    $responseData = $partnerController->getPartners($limit, $offset, $webPage, $companyName, $workFlow);
    echo json_encode($responseData);
})
    ->add(\Middleware\ListMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Get category
 */
$app->get('/admin/category', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\CategoryController $categoryController */
    $categoryController = $this->get('CategoryController');

    $limit = $request->getParam('limit');
    $offset = $request->getParam('offset');

    $name = (string) $request->getParam('category_name');
    $active = (int) $request->getParam('active');

    $responseData = $categoryController->getCategory($limit, $offset, $name, $active);
    echo json_encode($responseData);
})
    ->add(\Middleware\ListMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));


/**
 * Create category
 */
$app->put('/admin/category', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\CategoryController $categoryController */
    $categoryController = $this->get('CategoryController');
    $categoryController->createCategory();
})
    ->add(new \Middleware\Category\CreateCategoryMiddleware($app->getContainer()))
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Edit category
 */
$app->post('/admin/category', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\CategoryController $categoryController */
    $categoryController = $this->get('CategoryController');
    $categoryController->editCategory();
})
    ->add(new \Middleware\Category\EditCategoryMiddleware($app->getContainer()))
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Get product
 */
$app->get('/admin/product', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\ProductController $productController */
    $productController = $this->get('ProductController');
    $limit = $request->getParam('limit');
    $offset = $request->getParam('offset');

    $name = (string) $request->getParam('name');
    $price = $request->getParam('price');
    $webUrl = $request->getParam('web_url');
    $partnerId = $request->getParam('partner_id');
    $active = (int) $request->getParam('active');

    $responseData = $productController->getProduct($limit, $offset, $name, $price, $webUrl, $partnerId, $active);
    echo json_encode($responseData);
})
    ->add(\Middleware\Product\GetProductMiddleware::class)
    ->add(\Middleware\ListMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Create product
 */
$app->post('/admin/product', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\ProductController $productController */
    $productController = $this->get('ProductController');

    $product = $productController->createProduct();

    echo json_encode($product);
})
    ->add(new \Middleware\Product\CreateProductMiddleware($app->getContainer()))
    ->add(new \Middleware\Photo\UploadPhotoMiddleware($app->getContainer()))
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Create product from csv
 */
$app->post('/admin/product/csv', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\ProductController $productController */
    $productController = $this->get('ProductController');

    $productController->importProducts();
})
    ->add(\Middleware\Partner\PartnerIdMiddleware::class)
    ->add(\Middleware\File\CsvUploadMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Edit product
 */
$app->post('/admin/product/edit', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\ProductController $productController */
    $productController = $this->get('ProductController');

    $result = $productController->editProduct();

    echo json_encode($result);
})
    ->add(new \Middleware\Product\EditProductMiddleware($app->getContainer()))
    ->add(new \Middleware\Photo\AvatarMiddleware($app->getContainer()))
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Tag photo
 */
$app->post('/admin/photo/product', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PhotoController $photoController */
    $photoController = $this->get('PhotoController');

    $photoController->tagProductToPhoto();
    $photoId = $request->getParam('photoId');
    $newPhoto = $photoController->getPhoto($photoId);

    echo json_encode($newPhoto);
})
    ->add(\Middleware\Product\TagProductMiddleware::class)
    ->add(\Middleware\Photo\PhotoIdMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Add category to product
 */
$app->post('/admin/category/product', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Module\Tags\Product\Controller\ProductCategoryController $productCategoryController */
    $productCategoryController = $this->get('ProductCategoryController');
    $productId = (int) $request->getParam('productId');
    $categoryId = (int) $request->getParam('categoryId');

    $productCategoryController->setCategoryToProduct($productId, $categoryId);
})
    ->add(\Middleware\Product\ProductIdMiddleware::class)
    ->add(\Middleware\Category\CategoryIdMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Delete product.
 */
$app->delete('/admin/category/product', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Module\Tags\Product\Controller\ProductCategoryController $productCategoryController */
    $productCategoryController = $this->get('ProductCategoryController');
    $productId = (int) $request->getParam('productId');
    $categoryId = (int) $request->getParam('categoryId');

    $productCategoryController->unsetCategoryFromProduct($productId, $categoryId);
})
    ->add(\Middleware\Product\ProductIdMiddleware::class)
    ->add(\Middleware\Category\CategoryIdMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Delete photo
 */
$app->delete('/admin/photo', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PhotoController $photoController */
    $photoController = $this->get('PhotoController');

    $photoId = $request->getParam('photoId');
    $photoController->deletePhoto($photoId);
})
    ->add(\Middleware\Photo\PhotoIdMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));




















/**
 * Delete photo comment
 */
$app->delete('/admin/photo/comment', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Module\Photo\Controller\PhotoCommentController $photoController */
    $photoController = $this->get('PhotoCommentController');

    $commentId = $request->getParam('commentId');
    $photoController->deletePhotoComment($commentId);
})
    ->add(\Module\Photo\Middleware\PhotoCommentIdMiddleware::class)
    ->add(\Middleware\AdminMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

















/**
 * Get Profile
 */
$app->get('/user/{username}', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\UserController $userController */
    $userController = $this->get('UserController');

    echo json_encode($userController->getUserData());
})
    ->add(\Module\User\Middleware\UsernameMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Get user uploaded images.
 */
$app->get('/user/{username}/photos', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\UserController $userController */
    $userController = $this->get('UserController');

    $userData = \Lib\TemporarySession::getInstance()->get('username');
    $uId = $userData['id'];

    $limit = $request->getParam('limit');
    $offset = $request->getParam('offset');

    echo json_encode($userController->getUserApprovedImages($uId, $limit, $offset));
})
    ->add(\Middleware\ListMiddleware::class)
    ->add(\Module\User\Middleware\UsernameMiddleware::class)
    ->add(new \Middleware\AuthMiddleware($app->getContainer()));

/**
 * Get homepage photos
 */
$app->get('/photos', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PhotoController $photoController */
    $photoController = $this->get('PhotoController');

    $limit = $request->getParam('limit');
    $offset = $request->getParam('offset');

    echo json_encode($photoController->getPhotos($limit, $offset));
})
    ->add(\Middleware\ListMiddleware::class);

/**
 * Get photo details
 */
$app->get('/photo/{id}', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PhotoController $photoController */
    $photoController = $this->get('PhotoController');
    $photoId = $args['id'];

    echo json_encode($photoController->getPhoto($photoId));
})
    ->add(\Middleware\Photo\PhotoIdMiddleware::class);

/**
 * Get photo comments
 */
$app->get('/photo/{id}/comments', function(\Slim\Http\Request $request, $response, $args){
    /** @var \Controller\PhotoController $photoController */
    $photoController = $this->get('PhotoController');
    $photoId = $args['id'];

    $limit = $request->getParam('limit');
    $offset = $request->getParam('offset');

    echo json_encode($photoController->getPhotoComments($photoId, $limit, $offset));
})
    ->add(\Middleware\ListMiddleware::class)
    ->add(\Middleware\Photo\PhotoIdMiddleware::class);
