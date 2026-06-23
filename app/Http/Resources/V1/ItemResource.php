<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\Item;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Item
 */
class ItemResource extends JsonResource
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
            'sub_type' => $this->sub_type,
            'rarity' => $this->rarity,
            'carry_capacity' => $this->carry_capacity,
            'buy' => $this->buy,
            'sell' => $this->sell,
            'description' => $this->translate($this->resource, 'description'),
            'icon_url' => IconUrl::item($this->icon_name),

            // Equipment detail when this item is a weapon/armor/decoration.
            'weapon' => $this->whenLoaded('weapon', fn () => WeaponSummaryResource::make($this->weapon)),
            'armor' => $this->whenLoaded('armor', fn () => ArmorSummaryResource::make($this->armor)),
            'decoration' => $this->whenLoaded('decoration', fn () => DecorationSummaryResource::make($this->decoration)),

            // Where to get it.
            'monster_rewards' => HuntingRewardResource::collection($this->whenLoaded('huntingRewards')),
            'quest_rewards' => QuestRewardResource::collection($this->whenLoaded('questRewards')),
            'gathering' => GatheringResource::collection($this->whenLoaded('gathering')),
            'combinations' => CombinationResource::collection($this->whenLoaded('combinationsProducing')),

            // How it is crafted, and what it crafts into.
            'components_required' => ComponentResource::collection($this->whenLoaded('componentsRequired')),
            'used_in' => ComponentResource::collection($this->whenLoaded('usedInComponents')),

            // Skills granted (for charms and skill-bearing items).
            'skill_trees' => SkillTreePointResource::collection($this->whenLoaded('skillTrees')),
        ];
    }
}
