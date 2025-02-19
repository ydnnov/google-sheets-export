<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Services\GenerateEntriesService;
use Spatie\QueryBuilder\QueryBuilder;

class EntryController extends Controller
{
    public function index()
    {
        $entries = QueryBuilder::for(Entry::class)
            ->allowedFilters(['status'])
            ->allowedSorts(['id', 'status', 'created_at'])
            ->paginate();

        return EntryResource::collection($entries);
    }

    public function store(StoreEntryRequest $request)
    {
        $validated = $request->validated();
        $entry = Entry::create($validated);

        return new EntryResource($entry);
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
