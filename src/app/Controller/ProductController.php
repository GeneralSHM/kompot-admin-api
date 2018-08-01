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
        array $priceFromTo = [],
        $ourPrice = 0
    ) {
        $model = new Product();

        /** @var $items \Illuminate\Support\Collection */
        $items = $model->getItemsBasedOn($limit, $offset, $brands, $stores, $searchCriteria, $priceFromTo, $ourPrice);
        $items = $items->toArray();
        $totalProducts = $model->getItemsBasedOnCount($brands, $stores, $searchCriteria, $priceFromTo, $ourPrice)->toArray();

        foreach ($items as $key => $item) {
            $upc = $item['productUPC'];
            $ean = $item['productEAN'];
            $mpn = $item['productMPN'];
            $productId = $item['id'];

            $stores = [];// $model->getItemStores($ean, $mpn, $upc, $productId);
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

    public function deleteProduct($id)
    {
        $query = Product::query();
        $query->where('id' , '=' , $id)
              ->update(array(
                  'active' => 0
              ));
    }

    public function getAllStores()
    {
        $model = new Product();
        $stores = $model->getAllStores()->toArray();
        $finalArray = [];

        foreach ($stores as $key => $store) {
            $finalArray[] = [
                'label' => $store['storeName'],
                'value' => $store['storeName']
            ];
        }

        return $finalArray;
    }

    public function getAllActiveProducts()
    {
        $query = Product::query();
        $query = $query->leftJoin('brand', function($join)
        {
            $join->on(Product::getStaticTable() . '.brand_id', '=', 'brand.id');
        });
        $query = $query->where('items.active', '=', Product::ACTIVE_PRODUCT);
	$query = $query->where('items.did_download_from_api', '=', 1);

        $query->select(array(
            'items.name',
            'items.url',
            'items.upc',
            'items.mpn',
            'items.ean',
            'items.ebay_item_id as sku',
            'brand.name as brand',
            'items.price as price'
	));
        return $query->get()->toArray();
    }

    public function panic()
    {

        $query = Product::query();
        $query->update(array(
            'send_to_amazon' => 0
        ));

//        $query = Product::query();
//        $query->select(array(
//            'items.send_to_amazon',
//            'items.ebay_item_id as sku',
//            'items.availability'
//        ));
//
//        $items = $query->get()->toArray();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://212.237.24.65/mf-AWS-service/src/public/update-quantities",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([]),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response; die;
        }
    }
}
