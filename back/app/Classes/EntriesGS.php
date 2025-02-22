<?php

namespace app\Classes;

use App\Models\Entry;
use Google\Service\Sheets;

class EntriesGS
{
    protected Sheets $service;
    protected string $spreadsheetId;

    public function __construct(Sheets $service, string $spreadsheetId)
    {
        $this->service = $service;
        $this->spreadsheetId = $spreadsheetId;
    }

    public function export()
    {
        $data = Entry::allowed()->get();

        $values = [
            ['ID', 'Status', 'Content', 'Created at'],
            ...$data->map(function ($item) {
                return [
                    $item->id,
                    $item->status,
                    $item->content,
                    $item->created_at->toDateTimeString(),
                ];
            }),
        ];

        $range = 'Sheet1!A1:D' . count($values);

        $body = new Sheets\ValueRange(['values' => $values]);

        $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            [
                'valueInputOption' => 'RAW',
            ]);

        return 'Data exported to Google Sheets!';
    }
}
