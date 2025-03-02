<?php

namespace App\Services;

use App\Classes\EntriesGS;
use App\Errors\InvalidSpreadsheetUrlException;
use App\Errors\MissingSpreadsheetSettingException;
use App\Models\Setting;
use Google\Client;
use Google\Service\Sheets;

class EntriesGSService
{

    public function __construct(
        protected Sheets $service,
        protected Client $client,
    )
    {
    }

    public function fromSetting(): EntriesGS
    {
        $setting = Setting::where('key', 'google-sheet-url')->first();
        if (!$setting) {
            throw new MissingSpreadsheetSettingException();
        }

        $spreadsheetId = $this->normalizeSheetId($setting->value);

        return new EntriesGS($this->service, $spreadsheetId);
    }

    protected function normalizeSheetId($sheetIdOrUrl)
    {
        if (!str_contains($sheetIdOrUrl, '/')) {
            return $sheetIdOrUrl;
        }
        $result = preg_match(
            '/^https:\/\/docs\.google\.com\/spreadsheets\/d\/(\w+)/',
            $sheetIdOrUrl,
            $matches
        );
        if (!$result) {
            throw new InvalidSpreadsheetUrlException();
        }

        return $matches[1];
    }
}
