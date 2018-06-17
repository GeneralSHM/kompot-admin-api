<?php

namespace Model;

class Product extends BaseModel
{
    const ACTIVE_URL = 1;
    const INACTIVE_URL = 0;

    /**
     * Table name for the model.
     *
     * @var string
     */
    protected $table = 'items';

    public function getItemsBasedOn(
        int $limit = 20,
        int $offset = 0,
        array $brands = [],
        array $stores = [],
        string $searchCriteria = null,
        array $priceFromTo = []
    )
    {
        $query = Product::query();
        $query = $query->leftJoin('brand', function($join)
        {
            $join->on($this->table . '.brand_id', '=', 'brand.id');
        });

        if (count($brands) >= 1) {
            $query = $query->where($this->table . '.brand_id', $brands);
        }

        if (count($stores) >= 1) {
            $query = $query->whereIn($this->table . '.store', $stores);
        }

        if( ! is_null($searchCriteria) && strlen($searchCriteria) >= 1) {
            $query = $query->where(function ($query) use ($searchCriteria) {
                $query->where($this->table . '.name', '=', $searchCriteria)
                    ->orWhere($this->table . '.upc', '=', $searchCriteria)
                    ->orWhere($this->table . '.ean', '=', $searchCriteria)
                    ->orWhere($this->table . '.mpn', '=', $searchCriteria);
            });
        }

        if (count($priceFromTo) >= 1) {
            if ($priceFromTo['from']) {
                $query = $query->where($this->table . '.price', '>=', $priceFromTo['from']);
            }

            if ($priceFromTo['to']) {
                $query = $query->where($this->table . '.price', '<=', $priceFromTo['to']);
            }
        }
        $query
            ->select(array(
                $this->table . '.id as productId',
                $this->table . '.name as productName',
                $this->table . '.amazon_name as productSku',
                $this->table . '.amazon_price as ourPrice',
                $this->table . '.availability',
                $this->table . '.thumbnail',
                $this->table . '.url as productUrl',
                $this->table . '.last_change as productLastChange',
                $this->table . '.send_to_amazon as sendToAmazon',
                $this->table . '.price as productPrice',
                $this->table . '.upc as productUPC',
                $this->table . '.ean as productEAN',
                $this->table . '.mpn as productMPN',
                $this->table . '.store as storeName',
                'brand.name as brandName',
                'brand.id as brandId'
            ))
            ->orderBy('last_change', 'DESC')
            ->limit($limit)
            ->offset($offset);

        return $query->get();
    }

    public function getItemStores($ean, $mpn, $upc, $productId)
    {
        if (!$ean && !$mpn && !$upc) {
            return [];
        }
        $query = Product::query();

        $query = $query->where($this->table . '.id', '!=', $productId);
        if ($ean) {
            $query = $query->where(function ($query) use ($ean) {
                $query->where($this->table . '.upc', '=', $ean)
                    ->orWhere($this->table . '.ean', '=', $ean)
                    ->orWhere($this->table . '.mpn', '=', $ean);
            });
        }

        if ($mpn) {
            $query = $query->where(function ($query) use ($mpn) {
                $query->where($this->table . '.upc', '=', $mpn)
                    ->orWhere($this->table . '.ean', '=', $mpn)
                    ->orWhere($this->table . '.mpn', '=', $mpn);
            });
        }

        if ($upc) {
            $query = $query->where(function ($query) use ($upc) {
                $query->where($this->table . '.upc', '=', $upc)
                    ->orWhere($this->table . '.ean', '=', $upc)
                    ->orWhere($this->table . '.mpn', '=', $upc);
            });
        }

        $query->select(array(
            $this->table . '.store',
            $this->table . '.url'
        ));

        return $query->get();
    }
}