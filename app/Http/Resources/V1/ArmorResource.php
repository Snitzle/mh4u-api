<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Armor;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Armor
 */
class ArmorResource extends JsonResource
{
    use ResolvesTranslations;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item = $this->item;

        return [
            'id' => $this->id,
            'name' => $item !== null ? $this->translate($item, 'name') : null,
            'slot' => $this->slot,
            'rarity' => $item?->rarity,
            'icon_url' => IconUrl::armor($this->icon_name),
            'defense' => $this->defense,
            'max_defense' => $this->max_defense,
            'resistances' => [
                'fire' => $this->fire_res,
                'water' => $this->water_res,
                'thunder' => $this->thunder_res,
                'ice' => $this->ice_res,
                'dragon' => $this->dragon_res,
            ],
            'gender' => $this->gender,
            'hunter_type' => $this->hunter_type,
            'num_slots' => $this->num_slots,
            'buy' => $item?->buy,
            'armor_set_id' => $this->armorset_id,
            'skill_trees' => SkillTreePointResource::collection($this->whenLoaded('skillTrees')),
            'components' => ComponentResource::collection($this->whenLoaded('componentsRequired')),
            'models' => $this->whenLoaded('models', fn () => $this->models->pluck('filename')),
        ];
    }
}
