<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\QuestReward;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin QuestReward
 */
class QuestRewardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reward_slot' => $this->reward_slot,
            'percentage' => $this->percentage,
            'stack_size' => $this->stack_size,
            'item' => $this->whenLoaded('item', fn () => ItemSummaryResource::make($this->item)),
            'quest' => $this->whenLoaded('quest', fn () => QuestSummaryResource::make($this->quest)),
        ];
    }
}
