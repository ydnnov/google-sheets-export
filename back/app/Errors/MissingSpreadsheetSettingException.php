<?php

namespace app\Errors;

class MissingSpreadsheetSettingException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Google sheet url not set');
    }
}
