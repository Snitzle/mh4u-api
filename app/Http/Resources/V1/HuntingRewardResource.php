<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\HuntingReward;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HuntingReward
 */
class HuntingRewardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'condition' => $this->condition,
            'rank' => $this->rank,
            'stack_size' => $this->stack_size,
            'percentage' => $this->percentage,
            'item' => $this->whenLoaded('item', fn () => ItemSummaryResource::make($this->item)),
            'monster' => $this->whenLoaded('monster', fn () => MonsterSummaryResource::make($this->monster)),
        ];
    }
}
