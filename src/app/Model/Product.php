<?php

namespace Model;

use Illuminate\Database\Capsule\Manager;

class Product extends BaseModel
{
    const ACTIVE_URL = 1;
    const INACTIVE_URL = 0;

    const UPDATED_AT = 'last_change';

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
        $query = self::query();
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
                $this->table . '.id as id',
                $this->table . '.name as name',
                $this->table . '.amazon_name as sku',
                $this->table . '.amazon_price as our_price',
                $this->table . '.availability',
                $this->table . '.thumbnail',
                $this->table . '.url as productUrl',
                $this->table . '.last_change',
                $this->table . '.send_to_amazon',
                $this->table . '.price as price',
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

    public function getTotalItemsCount()
    {
        $query = Product::query();
        $query->select(array(
            Manager::connection()->raw('COUNT(*) as totalCount')
        ));

        return $query->get();
    }

    public function getItemStores($ean, $mpn, $upc, $productId)
    {
        if (!$ean && !$mpn && !$upc) {
            return [];
        }
        $query = self::query();

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
            $this->table . '.store as label',
            $this->table . '.url as value'
        ));

        return $query->get();
    }

    public function updateProduct($product)
    {
        $query = self::query();
        $query->where('id', $product['id'])
            ->update(array(
                'amazon_price' => $product['our_price'],
                'brand_id' => $product['brandId'],
                'amazon_name' => $product['sku']
            ));
    }
}
