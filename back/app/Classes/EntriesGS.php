<?php

namespace App\Classes;

use App\Models\Entry;
use Google\Service\Sheets;

class EntriesGS
{
    protected mixed $logTo = null;

    protected mixed $logFilename;

    public function __construct(
        protected Sheets $service,
        protected string $spreadsheetId,
    )
    {
    }

    public function getService(): Sheets
    {
        return $this->service;
    }

    public function getSpreadsheetId(): string
    {
        return $this->spreadsheetId;
    }

    public function setLogTo($target = 'file' | 'live' | null)
    {
        $this->logTo = $target;
    }

    public function export(int $sheetBatchSize, int $dbBatchSize)
    {
        if ($this->logTo === 'file') {
            $this->logFilename = 'logs/export_' . date('Y-m-d_H-i-s') . '.log';
        } else if ($this->logTo === 'web') {
            echo '<pre>';
        }

        $this->writeHeader();

        $this->deleteNotExistingAnymore($sheetBatchSize);

        $this->addNewlyCreated($dbBatchSize, $sheetBatchSize);

        if ($this->logTo === 'web') {
            echo '</pre>';
        }
    }

    public function createWindow(int $size = 100): SheetWindow
    {
        return new SheetWindow($this, $size);
    }

    public function getNumRows(): int
    {
        $range = "Sheet1!A:A";
        $response = $this->service->spreadsheets_values
            ->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        $result = count($values);

        return $result;
    }

    public function getRows(int $from, int $count): array
    {
        $range = 'Sheet1!A' . ($from + 2) . ':E' . ($from + $count + 1);
        $response = $this->service->spreadsheets_values
            ->get($this->spreadsheetId, $range);
        $result = $response->getValues();

        return $result;
    }

    protected function writeHeader()
    {
        $values = [['ID', 'Status', 'Content', 'Created at']];
        $range = 'Sheet1!A1:D1';
        $body = new Sheets\ValueRange(['values' => $values]);
        $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            [
                'valueInputOption' => 'RAW',
            ]);
    }

    /**
     * Go through all spreadsheet records in batches (to minimize
     * HTTP requests). For each batch:
     * 1. Search (WHERE id IN (...)) for corresponding records in the
     *    database, also applying filtering scope
     * 2. Diff arrays of ids from both places, to find which records
     *    are absent in the database, but present in the spreadsheet.
     *    Delete those records from the spreadsheet
     * @param int $sheetBatchSize
     * @return void
     */
    protected function deleteNotExistingAnymore(int $sheetBatchSize)
    {
        $win = $this->createWindow($sheetBatchSize);
        while (true) {
            if ($win->isReachedEOF()) {
                $this->log("Finished deleting non-existing entries");
                break;
            }
            $ids = $win->getIds();
            $this->log("From {$win->getFrom()} to {$win->getTo()}");
            $this->log('Existing in the sheet: ' . implode(', ', $ids));
            $shouldStayIds = Entry
                ::whereIn('id', $ids)
                ->includedInExport()
                ->select('id')
                ->get()
                ->pluck('id');
            $this->log('Should stay after export: ' . $shouldStayIds->sort()->join(', '));
            $toDeleteIds = collect($ids)->diff($shouldStayIds);
            $this->log('To delete ids: ' . ($toDeleteIds->sort()->join(', ') ?: 'none'));
            $toDeleteIndices = $toDeleteIds->map(
                fn($v) => array_search($v, $ids, false)
            )
                ->sortDesc()
                ->values()
                ->toArray();
            $this->log('To delete indices: ' . implode(', ', $toDeleteIndices));
            $win->deleteRows($toDeleteIndices);
            $win->gotoNextPage();
        }
    }

    /**
     * An inverse of deleteNotExistingAnymore, our goal is to find all
     * records that are presend in the database, but absent in the
     * spreadsheet. One technical difference here, is that we can't
     * search in the spreadsheet using WHERE IN, so we do a nested loop:
     * Go through database records in batches (minimizing HTTP requests).
     * For each batch, go through spreadsheet rows, also in batches
     * (minimizing database queries). Diff id arrays and append records
     * absent in the spreadsheet but present in DB
     * @param int $dbBatchSize
     * @param int $sheetBatchSize
     * @return void
     */
    protected function addNewlyCreated(int $dbBatchSize, int $sheetBatchSize)
    {
        $baseQuery = Entry
            ::includedInExport()
            ->orderBy('id')
            ->limit($dbBatchSize);
        $dbOffset = 0;
        $sheetNextRowNum = $this->getNumRows() + 1;
        while (true) {
            $records = $baseQuery->offset($dbOffset)->get();
            if (!count($records)) {
                $this->log("Finished adding newly created entries");
                break;
            }

            $toAddIds = $records->pluck('id');
            $win = $this->createWindow($sheetBatchSize);
            while (true) {
                if ($win->isReachedEOF()) {
                    break;
                }
                $sheetIds = $win->getIds();
                $toAddIds = $toAddIds->diff($sheetIds);
                $win->gotoNextPage();
            }
            $toAddRecords = $records->whereIn('id', $toAddIds);
            $this->log('Adding entries: ' . $toAddIds->join(', '));
            $values = [
                ...$toAddRecords->map(function ($item) {
                    return [
                        $item->id,
                        $item->status,
                        $item->content,
                        $item->created_at->toDateTimeString(),
                    ];
                }),
            ];

            $range = 'Sheet1!A' . $sheetNextRowNum .
                ':D' . ($sheetNextRowNum + count($values));

            $body = new Sheets\ValueRange(['values' => $values]);

            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                $body,
                [
                    'valueInputOption' => 'RAW',
                ]);

            $dbOffset += $dbBatchSize;
            $sheetNextRowNum += count($values);
        }
    }

    protected function log($message)
    {
        if ($this->logTo === 'file') {
            file_put_contents(
                storage_path($this->logFilename),
                $message . PHP_EOL,
                FILE_APPEND
            );
        } else if ($this->logTo === 'web') {
            echo $message . PHP_EOL;
        }
    }
}
