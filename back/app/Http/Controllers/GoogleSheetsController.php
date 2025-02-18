<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetsController extends Controller
{
    public function export()
    {
        // TODO don't forget to move this outside!!!
        $spreadsheetId = '1_ZTsyNGf7HVSx2qTrseDbTjMZqKh91u72xgd2Ygxh4s';
        $range = 'Sheet1!A1:B3';

        $client = new Client();
        $client->setAuthConfig(storage_path('app/gscreds.json'));
        $client->addScope(Sheets::SPREADSHEETS);

        $service = new Sheets($client);

        $values = [
            ['ID', 'Name'],
            [1, 'Alice'],
            [2, 'Bob'],
        ];
        $body = new Sheets\ValueRange(['values' => $values]);

        $service->spreadsheets_values->update($spreadsheetId, $range, $body, [
            'valueInputOption' => 'RAW'
        ]);

        return 'Data exported to Google Sheets!';
    }
}
