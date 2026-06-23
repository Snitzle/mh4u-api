<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\DecorationResource;
use App\Http\Resources\V1\DecorationSummaryResource;
use App\Models\Decoration;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DecorationController extends ApiController
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $decorations = QueryBuilder::for(Decoration::class)
            ->allowedFilters(AllowedFilter::exact('num_slots'))
            ->allowedSorts('num_slots', 'id')
            ->defaultSort('id')
            ->with('item')
            ->paginate($this->perPage())
            ->appends($request->query());

        return DecorationSummaryResource::collection($decorations);
    }

    public function show(Decoration $decoration): DecorationResource
    {
        $decoration->load(['item', 'skillTrees']);

        return DecorationResource::make($decoration);
    }
}
