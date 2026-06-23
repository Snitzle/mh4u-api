<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\IndexRequest;
use App\Http\Resources\V1\HornMelodyResource;
use App\Models\HornMelody;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HornMelodyController extends ApiController
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $melodies = HornMelody::query()
            ->orderBy('id')
            ->paginate($this->perPage())
            ->appends($request->query());

        return HornMelodyResource::collection($melodies);
    }
}
