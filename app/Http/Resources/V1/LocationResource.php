<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Gathering;
use App\Models\Location;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Location
 */
class LocationResource extends JsonResource
{
    use ResolvesTranslations;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translate($this->resource, 'name'),
            'map_url' => IconUrl::location($this->map),
            'monsters' => MonsterSummaryResource::collection($this->whenLoaded('monsters')),
            'gathering' => $this->whenLoaded(
                'gathering',
                fn () => $this->gathering
                    ->groupBy(fn (Gathering $gathering): string => $gathering->rank->value)
                    ->map(fn ($group) => GatheringResource::collection($group)),
            ),
            'areas' => $this->whenLoaded('areas', fn () => $this->areas->map(fn ($area): array => [
                'name' => $area->area_name,
                'hot_drink' => $area->hot_drink,
                'cool_drink' => $area->cool_drink,
                'torch' => $area->torch,
                'pitfall_trap' => $area->pitfall_trap,
                'shock_trap' => $area->shock_trap,
            ])),
        ];
    }
}
