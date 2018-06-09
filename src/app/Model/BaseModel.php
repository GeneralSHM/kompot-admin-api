<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    const ACTIVE = 1;
    const NOT_ACTIVE = 0;
    
    /**
     * Returning table name provided in child classes.
     *
     * @return mixed
     * @author Vasil.Rashkov
     */
    public static function getStaticTable()
    {
        $self = new static;
        return $self->getTable();
    }
}
