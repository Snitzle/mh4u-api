<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Http\Resources\Concerns\ResolvesTranslations;
use App\Models\HuntingReward;
use App\Models\Monster;
use App\Support\IconUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Monster
 */
class MonsterResource extends JsonResource
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
            'class' => $this->class,
            'signature_move' => $this->signature_move,
            'trait' => $this->trait,
            'icon_url' => IconUrl::monster($this->icon_name),
            'damage' => MonsterDamageResource::collection($this->whenLoaded('damage')),
            'weaknesses' => MonsterWeaknessResource::collection($this->whenLoaded('weaknesses')),
            'statuses' => MonsterStatusResource::collection($this->whenLoaded('statuses')),
            'ailments' => $this->whenLoaded('ailments', fn () => $this->ailments->pluck('ailment')),
            'habitats' => MonsterHabitatResource::collection($this->whenLoaded('habitats')),
            'hunting_rewards' => $this->whenLoaded(
                'huntingRewards',
                fn () => $this->huntingRewards
                    ->groupBy(fn (HuntingReward $reward): string => $reward->rank->value)
                    ->map(fn ($group) => HuntingRewardResource::collection($group)),
            ),
            'quests' => QuestSummaryResource::collection($this->whenLoaded('quests')),
        ];
    }
}
