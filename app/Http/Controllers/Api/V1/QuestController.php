<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\RewardSlot;
use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\QuestResource;
use App\Http\Resources\V1\QuestRewardResource;
use App\Http\Resources\V1\QuestSummaryResource;
use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Quests
 *
 * Caravan, Guild and Event quests with their monsters, rewards and prerequisites.
 */
class QuestController extends ApiController
{
    /**
     * List quests.
     *
     * @queryParam filter[hub] string Filter by hub: Caravan, Guild or Event. Example: Guild
     * @queryParam filter[stars] integer Filter by star rank (1-10). Example: 5
     * @queryParam filter[monster] integer Only quests featuring this monster ID. Example: 24
     * @queryParam sort string Sort by name or stars (prefix with - to reverse). Example: stars
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $quests = QueryBuilder::for(Quest::class)
            ->allowedFilters(
                AllowedFilter::exact('hub'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('stars'),
                AllowedFilter::exact('location_id'),
                AllowedFilter::partial('name'),
                AllowedFilter::scope('monster', 'forMonster'),
            )
            ->allowedSorts('name', 'stars', 'id')
            ->defaultSort('stars')
            ->paginate($this->perPage())
            ->appends($request->query());

        return QuestSummaryResource::collection($quests);
    }

    /**
     * @urlParam quest integer required The quest ID. Example: 1
     */
    public function show(Quest $quest): QuestResource
    {
        $quest->load([
            'location',
            'monsters',
            'prerequisites',
            'rewards.item',
            'supplies.item',
        ]);

        return QuestResource::make($quest);
    }

    public function rewards(Request $request, Quest $quest): AnonymousResourceCollection
    {
        $query = $quest->rewards()->with('item');

        $slot = RewardSlot::tryFrom((string) $request->query('slot'));

        if ($slot instanceof RewardSlot) {
            $query->where('reward_slot', $slot->value);
        }

        return QuestRewardResource::collection(
            $query->orderByDesc('percentage')->paginate($this->perPage()),
        );
    }
}
