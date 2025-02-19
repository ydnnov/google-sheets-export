<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Models\Entry;
use App\Services\GenerateEntriesService;

class EntryController extends Controller
{
    public function index()
    {
        return 'entries list';
    }

    public function store(StoreEntryRequest $request)
    {
        return 'create entry';
    }

    public function show(Entry $entry)
    {
        return 'get entry';
    }

    public function update(UpdateEntryRequest $request, Entry $entry)
    {
        return 'update entry';
    }

    public function destroy(Entry $entry)
    {
        return 'destroy entry';
    }

    public function generate(GenerateEntriesService $generator)
    {
        $generator->createEntries(1000);
        return response()->json(['message' => 'Entries created']);
    }
}
