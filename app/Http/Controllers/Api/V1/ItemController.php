<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\ComponentResource;
use App\Http\Resources\V1\ItemResource;
use App\Http\Resources\V1\ItemSummaryResource;
use App\Models\Item;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Items
 *
 * Every item: materials, consumables and the base records for equipment.
 * Item detail includes where to get it and what it crafts into.
 */
class ItemController extends ApiController
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $items = QueryBuilder::for(Item::class)
            ->allowedFilters(
                AllowedFilter::exact('type'),
                AllowedFilter::exact('sub_type'),
                AllowedFilter::exact('rarity'),
                AllowedFilter::partial('name'),
            )
            ->allowedSorts('name', 'rarity', 'buy', 'sell', 'id')
            ->defaultSort('name')
            ->paginate($this->perPage())
            ->appends($request->query());

        return ItemSummaryResource::collection($items);
    }

    /**
     * @urlParam item integer required The item ID. Example: 1
     */
    public function show(Item $item): ItemResource
    {
        $item->load([
            'weapon',
            'armor',
            'decoration',
            'huntingRewards.monster',
            'questRewards.quest',
            'gathering.location',
            'combinationsProducing.itemOne',
            'combinationsProducing.itemTwo',
            'componentsRequired.componentItem',
            'usedInComponents.createdItem',
            'skillTrees',
        ]);

        return ItemResource::make($item);
    }

    /**
     * The crafting recipes that consume this item.
     */
    public function components(Item $item): AnonymousResourceCollection
    {
        $components = $item->usedInComponents()
            ->with('createdItem')
            ->paginate($this->perPage());

        return ComponentResource::collection($components);
    }
}
