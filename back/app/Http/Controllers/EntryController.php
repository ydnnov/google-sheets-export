<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Services\GenerateEntriesService;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class EntryController extends Controller
{
    public function index()
    {
        $entries = QueryBuilder::for(Entry::class)
            ->allowedFilters(['status'])
            ->allowedSorts(['id', 'status', 'created_at'])
            ->defaultSort('id')
            ->paginate(request()->query('per_page') ?? 10);

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
        return new EntryResource($entry);
    }

    public function update(UpdateEntryRequest $request, Entry $entry)
    {
        $validated = $request->validated();
        $entry->update($validated);

        return new EntryResource($entry);
    }

    public function destroy(Entry $entry)
    {
        $entry->delete();

        return response()->json(['message' => 'Entry deleted'], Response::HTTP_NO_CONTENT);
    }

    public function generate(GenerateEntriesService $generator)
    {
        $generator->createEntries(1000);
        return response()->json(['message' => 'Entries created']);
    }
}
