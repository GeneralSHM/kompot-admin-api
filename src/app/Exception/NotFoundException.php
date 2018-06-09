<?php

namespace Exception;

class NotFoundException extends AbstractException
{
    public function __construct($missing, $code = 422)
    {
        parent::__construct($missing . ' was not found.', $code);
    }
}
