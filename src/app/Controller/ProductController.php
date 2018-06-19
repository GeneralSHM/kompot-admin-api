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
            $productId = $item['id'];

            $stores = $model->getItemStores($ean, $mpn, $upc, $productId);
            if (count($stores) >= 1) {
                $tmp = $stores[0];
                $stores[0] = [
                    'value' => $item['productUrl'],
                    'label' => $item['storeName']
                ];
                $stores[] = $tmp;
            } else {
                $stores[0] = [
                    'value' => $item['productUrl'],
                    'label' => $item['storeName']
                ];
            }
            $items[$key]['stores'] = $stores;
            $items[$key]['product_id'] = 'UPC' . $upc . ', EAN' . $ean . ', MPN' . $mpn;
            $items[$key]['send_to_amazon'] = $item['send_to_amazon'] === 1;
        }

        $totalProducts = $model->getTotalItemsCount()->toArray();
        return [
            'items' => $items,
            'pagination' => [
                'totalCount' => $totalProducts[0]['totalCount']
            ]
        ];
    }

    public function updateProduct($product)
    {
        $model = new Product();

        $model->updateProduct($product);
    }
}
