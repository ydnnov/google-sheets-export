<?php

namespace app\Errors;

class InvalidSpreadsheetIdException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Google sheet id / url is invalid');
    }
}
