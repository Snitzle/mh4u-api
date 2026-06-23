<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\Gathering;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Gathering
 */
class GatheringResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'area' => $this->area,
            'site' => $this->site,
            'rank' => $this->rank,
            'quantity' => $this->quantity,
            'percentage' => $this->percentage,
            'item' => $this->whenLoaded('item', fn () => ItemSummaryResource::make($this->item)),
            'location' => $this->whenLoaded('location', fn () => LocationSummaryResource::make($this->location)),
        ];
    }
}
