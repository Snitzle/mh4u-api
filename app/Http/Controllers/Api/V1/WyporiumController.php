<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\WyporiumResource;
use App\Models\Wyporium;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Wyporium
 *
 * Wyporium item-for-item trades and the quests that unlock them.
 */
class WyporiumController extends ApiController
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $trades = Wyporium::query()
            ->with(['itemIn', 'itemOut', 'unlockQuest'])
            ->orderBy('id')
            ->paginate($this->perPage())
            ->appends($request->query());

        return WyporiumResource::collection($trades);
    }
}
