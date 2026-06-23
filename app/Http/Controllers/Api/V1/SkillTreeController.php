<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\SkillTreeResource;
use App\Http\Resources\V1\SkillTreeSummaryResource;
use App\Models\SkillTree;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Skill Trees
 *
 * Skill trees, their skills by point threshold, and the items that grant them.
 */
class SkillTreeController extends ApiController
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $skillTrees = QueryBuilder::for(SkillTree::class)
            ->allowedFilters(AllowedFilter::partial('name'))
            ->allowedSorts('name', 'id')
            ->defaultSort('name')
            ->paginate($this->perPage())
            ->appends($request->query());

        return SkillTreeSummaryResource::collection($skillTrees);
    }

    /**
     * @urlParam skillTree integer required The skill tree ID. Example: 1
     */
    public function show(SkillTree $skillTree): SkillTreeResource
    {
        $skillTree->load([
            'skills',
            'items',
        ]);

        return SkillTreeResource::make($skillTree);
    }
}
