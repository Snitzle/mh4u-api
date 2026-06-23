<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\WeaponResource;
use App\Http\Resources\V1\WeaponSummaryResource;
use App\Models\Weapon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Weapons
 *
 * All 14 weapon types, their stats and upgrade trees.
 */
class WeaponController extends ApiController
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $weapons = QueryBuilder::for(Weapon::class)
            ->allowedFilters(
                AllowedFilter::exact('wtype'),
                AllowedFilter::exact('element'),
                AllowedFilter::exact('final'),
            )
            ->allowedSorts('attack', 'tree_depth', 'id')
            ->defaultSort('id')
            ->with('item')
            ->paginate($this->perPage())
            ->appends($request->query());

        return WeaponSummaryResource::collection($weapons);
    }

    /**
     * @urlParam weapon integer required The weapon ID. Example: 5001
     */
    public function show(Weapon $weapon): WeaponResource
    {
        $weapon->load([
            'item',
            'parent.item',
            'children.item',
            'componentsRequired.componentItem',
        ]);

        if ($weapon->wtype->isHuntingHorn()) {
            $weapon->load('hornMelodies');
        }

        return WeaponResource::make($weapon);
    }

    /**
     * The full upgrade tree this weapon belongs to (root + all descendants),
     * ordered by depth.
     *
     * @urlParam weapon integer required The weapon ID. Example: 5001
     */
    public function tree(Weapon $weapon): AnonymousResourceCollection
    {
        // Weapons of a type are few; load them once and build the tree in memory.
        $all = Weapon::query()
            ->with('item')
            ->where('wtype', $weapon->wtype->value)
            ->get()
            ->keyBy('id');

        // Walk up to the root of this weapon's tree.
        $root = $weapon;

        while ($root->parent_id !== null) {
            $parent = $all->get($root->parent_id);

            if ($parent === null) {
                break;
            }

            $root = $parent;
        }

        // Breadth-first collect the root and every descendant.
        $tree = collect();
        $queue = collect([$root]);

        while ($queue->isNotEmpty()) {
            $node = $queue->shift();

            if ($node === null) {
                continue;
            }

            $tree->push($node);
            $queue = $queue->merge($all->filter(fn (Weapon $candidate): bool => $candidate->parent_id === $node->id));
        }

        return WeaponSummaryResource::collection(
            $tree->sortBy('tree_depth')->values(),
        );
    }
}
