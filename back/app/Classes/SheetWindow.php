<?php

namespace App\Classes;

use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request;

/**
 * Represents a window in batch processing of a spreadsheet when
 * deleting entries that are no more present in the database.
 * This is for convenience, to reduce complexity of
 * EntriesGS::deleteNotExistingAnymore() method
 */
class SheetWindow
{
    protected int $from;

    protected int $to;

    protected int $numRows;

    protected bool $reachedEOF;

    public function __construct(
        protected EntriesGS $owner,
        protected int       $size
    )
    {
        // Starting numeration from 1, and account for header row
        $this->from = 2;
        $this->to = $size + 1;
        $this->numRows = $owner->getNumRows();
        $this->updateEOF();
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getFrom(): int
    {
        return $this->from;
    }

    public function getTo(): int
    {
        return $this->to;
    }

    public function isReachedEOF(): bool
    {
        return $this->reachedEOF;
    }

    public function gotoNextPage(): void
    {
        if ($this->reachedEOF) {
            return;
        }

        $this->from = $this->to + 1;
        $this->to = $this->from + $this->size - 1;

        $this->updateEOF();
    }

    public function getIds(): array
    {
        if ($this->reachedEOF) {
            return [];
        }

        $range = "Sheet1!A{$this->from}:A{$this->to}";
        $response = $this->owner->getService()
            ->spreadsheets_values
            ->get(
                $this->owner->getSpreadsheetId(),
                $range
            );
        $values = $response->getValues();

        $result = array_map(function ($value) {
            return (int)$value[0];
        }, $values);

        return $result;
    }

    public function deleteRows($offsets): void
    {
        if (!count($offsets)) {
            return;
        }

        $requests = array_map(function ($offset) {
            return new Request([
                'deleteDimension' => [
                    'range' => [
                        'sheetId' => 0,
                        'dimension' => 'ROWS',
                        'startIndex' => $this->from + $offset - 1,
                        'endIndex' => $this->from + $offset,
                    ],
                ],
            ]);
        }, $offsets);

        $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
            'requests' => $requests,
        ]);

        $this->owner->getService()
            ->spreadsheets
            ->batchUpdate(
                $this->owner->getSpreadsheetId(),
                $batchUpdateRequest
            );

        $this->to -= count($offsets);
        $this->numRows -= count($offsets);

        $this->updateEOF();
    }

    protected function updateEOF(): void
    {
        $this->reachedEOF = ($this->from > $this->numRows);
        if ($this->to > $this->numRows) {
            $this->to = $this->numRows;
        }
    }
}
