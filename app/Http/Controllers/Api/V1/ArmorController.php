<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\ArmorResource;
use App\Http\Resources\V1\ArmorSummaryResource;
use App\Models\Armor;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Armor
 *
 * Armor pieces by slot, with defense, resistances and granted skills.
 */
class ArmorController extends ApiController
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $armor = QueryBuilder::for(Armor::class)
            ->allowedFilters(
                AllowedFilter::exact('slot'),
                AllowedFilter::exact('hunter_type'),
                AllowedFilter::exact('gender'),
            )
            ->allowedSorts('defense', 'max_defense', 'id')
            ->defaultSort('id')
            ->with('item')
            ->paginate($this->perPage())
            ->appends($request->query());

        return ArmorSummaryResource::collection($armor);
    }

    /**
     * @urlParam armor integer required The armor ID. Example: 1914
     */
    public function show(Armor $armor): ArmorResource
    {
        $armor->load([
            'item',
            'skillTrees',
            'componentsRequired.componentItem',
        ]);

        return ArmorResource::make($armor);
    }
}
