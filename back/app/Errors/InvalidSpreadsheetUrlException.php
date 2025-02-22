<?php

namespace app\Errors;

class InvalidSpreadsheetUrlException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Google sheet url is invalid');
    }
}
