<?php

namespace App\Exceptions;

use Exception;

class FileUploadFailedException extends Exception
{
    public function __construct()
    {
        parent::__construct('File upload failed.');
    }
}
