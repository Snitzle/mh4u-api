<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\Wyporium;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Wyporium
 */
class WyporiumResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_in' => $this->whenLoaded('itemIn', fn () => ItemSummaryResource::make($this->itemIn)),
            'item_out' => $this->whenLoaded('itemOut', fn () => ItemSummaryResource::make($this->itemOut)),
            'unlock_quest' => $this->whenLoaded('unlockQuest', fn () => QuestSummaryResource::make($this->unlockQuest)),
        ];
    }
}
