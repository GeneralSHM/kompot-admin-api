<?php

namespace Model;

class Brand extends BaseModel
{
    const ACTIVE_URL = 1;
    const INACTIVE_URL = 0;

    /**
     * Table name for the model.
     *
     * @var string
     */
    protected $table = 'brand';

    public function getAllBrands()
    {
        $query = Brand::query();

        return $query->get();
    }
}
