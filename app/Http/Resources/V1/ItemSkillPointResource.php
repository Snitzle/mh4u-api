<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Item;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * An item together with the points it grants toward a skill tree
 * (read from the `item_skill_tree` pivot).
 *
 * @mixin Item
 */
class ItemSkillPointResource extends JsonResource
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
            'type' => $this->type,
            'icon_url' => IconUrl::item($this->icon_name),
            'points' => $this->whenPivotLoaded('item_skill_tree', fn () => $this->pivot?->point_value),
        ];
    }
}
