<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\Rank;
use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\HuntingRewardResource;
use App\Http\Resources\V1\MonsterResource;
use App\Http\Resources\V1\MonsterSummaryResource;
use App\Models\Monster;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Monsters
 *
 * Large and small monsters, their weakness/damage charts, habitats and drops.
 */
class MonsterController extends ApiController
{
    /**
     * List monsters.
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $monsters = QueryBuilder::for(Monster::class)
            ->allowedFilters('class', AllowedFilter::partial('name'))
            ->allowedSorts('name', 'sort_name', 'class')
            ->defaultSort('sort_name')
            ->paginate($this->perPage())
            ->appends($request->query());

        return MonsterSummaryResource::collection($monsters);
    }

    /**
     * Get a monster with its damage, weaknesses, habitats, rewards and quests.
     *
     * @urlParam monster integer required The monster ID. Example: 1
     */
    public function show(Monster $monster): MonsterResource
    {
        $monster->load([
            'damage',
            'weaknesses',
            'statuses',
            'ailments',
            'habitats.location',
            'huntingRewards.item',
            'quests',
        ]);

        return MonsterResource::make($monster);
    }

    public function huntingRewards(Request $request, Monster $monster): AnonymousResourceCollection
    {
        $query = $monster->huntingRewards()->with('item');

        $rank = Rank::tryFrom((string) $request->query('rank'));

        if ($rank instanceof Rank) {
            $query->ofRank($rank);
        }

        return HuntingRewardResource::collection(
            $query->orderByDesc('percentage')->paginate($this->perPage()),
        );
    }
}
