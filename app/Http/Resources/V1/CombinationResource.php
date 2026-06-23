<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\Combination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Combination
 */
class CombinationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount_made_min' => $this->amount_made_min,
            'amount_made_max' => $this->amount_made_max,
            'percentage' => $this->percentage,
            'created_item' => $this->whenLoaded('createdItem', fn () => ItemSummaryResource::make($this->createdItem)),
            'item_one' => $this->whenLoaded('itemOne', fn () => ItemSummaryResource::make($this->itemOne)),
            'item_two' => $this->whenLoaded('itemTwo', fn () => ItemSummaryResource::make($this->itemTwo)),
        ];
    }
}
