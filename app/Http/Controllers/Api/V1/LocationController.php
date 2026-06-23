<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\Rank;
use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\GatheringResource;
use App\Http\Resources\V1\LocationResource;
use App\Http\Resources\V1\LocationSummaryResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Locations
 *
 * Hunting locations, the monsters found there and gatherable items by rank.
 */
class LocationController extends ApiController
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $locations = QueryBuilder::for(Location::class)
            ->allowedSorts('name', 'id')
            ->defaultSort('id')
            ->paginate($this->perPage())
            ->appends($request->query());

        return LocationSummaryResource::collection($locations);
    }

    /**
     * @urlParam location integer required The location ID. Example: 1
     */
    public function show(Location $location): LocationResource
    {
        $location->load([
            'monsters',
            'gathering.item',
        ]);

        return LocationResource::make($location);
    }

    public function gathering(Request $request, Location $location): AnonymousResourceCollection
    {
        $query = $location->gathering()->with('item');

        $rank = Rank::tryFrom((string) $request->query('rank'));

        if ($rank instanceof Rank) {
            $query->ofRank($rank);
        }

        return GatheringResource::collection(
            $query->orderByDesc('percentage')->paginate($this->perPage()),
        );
    }
}
