<?php

namespace Model;

class Brand extends BaseModel
{
    const ACTIVE_URL = 1;
    const INACTIVE_URL = 0;

    const UPDATED_AT = 'last_edited';
    const CREATED_AT = 'date_created';

    protected $fillable = [
        'name'
    ];

    /**
     * Table name for the model.
     *
     * @var string
     */
    protected $table = 'brand';

    public function getAllBrands()
    {
        $query = Brand::query();

        $query->select(array(
            $this->table . '.id as value',
            $this->table . '.name as label',
        ));
        return $query->get();
    }
}
