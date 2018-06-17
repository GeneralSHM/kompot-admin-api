<?php

namespace Controller;

use Model\Product;

class ProductController extends BaseController {
    public function getProducts(
        int $limit = 20,
        int $offset = 0,
        array $brands = [],
        array $stores = [],
        string $searchCriteria = null,
        array $priceFromTo = []
    ) {
        $model = new Product();

        /** @var $items \Illuminate\Support\Collection */
        $items = $model->getItemsBasedOn($limit, $offset, $brands, $stores, $searchCriteria, $priceFromTo);
        $items = $items->toArray();

        foreach ($items as $key => $item) {
            $upc = $item['productUPC'];
            $ean = $item['productEAN'];
            $mpn = $item['productMPN'];
            $productId = $item['productId'];

            $stores = $model->getItemStores($ean, $mpn, $upc, $productId);
            $items[$key]['stores'] = $stores;
        }

        return $items;
    }
}
