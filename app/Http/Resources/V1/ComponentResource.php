<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Component
 */
class ComponentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'quantity' => $this->quantity,
            'type' => $this->type,
            'created_item' => $this->whenLoaded('createdItem', fn () => ItemSummaryResource::make($this->createdItem)),
            'component_item' => $this->whenLoaded('componentItem', fn () => ItemSummaryResource::make($this->componentItem)),
        ];
    }
}
