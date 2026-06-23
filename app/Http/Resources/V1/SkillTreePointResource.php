<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\SkillTree;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A skill tree together with the point value an item grants toward it
 * (read from the `item_skill_tree` pivot).
 *
 * @mixin SkillTree
 */
class SkillTreePointResource extends JsonResource
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
            'points' => $this->whenPivotLoaded('item_skill_tree', fn () => $this->pivot?->point_value),
        ];
    }
}
