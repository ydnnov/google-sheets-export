<?php

namespace App\Http\Controllers;

use App\Services\EntriesGSService;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

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

    public function readGoogleSheet(int $count = 10)
    {
        $output = new BufferedOutput();

        Artisan::call('app:read-google-sheet', [
            '--count' => $count
        ], $output);

        return nl2br($output->fetch());
    }
}
