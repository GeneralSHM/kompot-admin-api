<?php

namespace Exception;

class UploadException extends AbstractException
{
    public function __construct()
    {
        parent::__construct('Upload failed, please try again later.', 400);
    }
}
