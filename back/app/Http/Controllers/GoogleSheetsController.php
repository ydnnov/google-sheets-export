<?php

namespace App\Http\Controllers;

use App\Services\EntriesGSService;

class GoogleSheetsController extends Controller
{
    public function export(EntriesGSService $entriesGSService)
    {
        $entriesGS = $entriesGSService->fromSetting();

        $entriesGS->setLogTo('file');

        $result = $entriesGS->export(
            config('gs-export.sheet_batch_size'),
            config('gs-export.db_batch_size')
        );

        return $result;
    }
}
