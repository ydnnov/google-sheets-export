<?php

namespace App\Http\Controllers;

use App\Services\EntriesGSService;

class GoogleSheetsController extends Controller
{
    public function export(EntriesGSService $entriesGSService)
    {
        $entriesGS = $entriesGSService->fromSetting();
        $result = $entriesGS->export();
        return $result;
    }
}
