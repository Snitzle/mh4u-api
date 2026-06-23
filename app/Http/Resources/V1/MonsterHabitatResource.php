<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\MonsterHabitat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MonsterHabitat
 */
class MonsterHabitatResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'start_area' => $this->start_area,
            'move_area' => $this->move_area,
            'rest_area' => $this->rest_area,
            'location' => $this->whenLoaded('location', fn () => LocationSummaryResource::make($this->location)),
        ];
    }
}
