<?php

namespace Controller;

use Model\Brand;

class BrandController extends BaseController {
    public function getAllBrands()
    {
        $model = new Brand();

        return $model->getAllBrands()->toArray();
    }
}
