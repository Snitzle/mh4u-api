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
            'ecology' => $this->ecology,
            'icon_url' => IconUrl::monster($this->icon_name),
            'hp' => $this->base_hp === null ? null : [
                'base' => $this->base_hp,
                'low_multiplier' => $this->hp_mult_low,
                'high_multiplier' => $this->hp_mult_high,
                'g_multiplier' => $this->hp_mult_g,
            ],
            'size_class' => $this->size_class,
            'crowns' => $this->crown_king === null && $this->crown_large === null && $this->crown_mini === null ? null : [
                'mini' => $this->crown_mini,
                'large' => $this->crown_large,
                'king' => $this->crown_king,
            ],
            'enraged' => $this->rage_duration === null ? null : [
                'duration' => $this->rage_duration,
                'attack_modifier' => $this->rage_mod_attack,
                'defense_modifier' => $this->rage_mod_defense,
                'speed_modifier' => $this->rage_mod_speed,
            ],
            'limping' => $this->thresholdGroup(['low' => $this->limp_low, 'high' => $this->limp_high, 'high_apex' => $this->limp_high_apex, 'g' => $this->limp_g, 'g_apex' => $this->limp_g_apex]),
            'capture' => $this->thresholdGroup(['low' => $this->cap_low, 'high' => $this->cap_high, 'high_apex' => $this->cap_high_apex, 'g' => $this->cap_g, 'g_apex' => $this->cap_g_apex]),
            'stagger_limits' => MonsterStaggerLimitResource::collection($this->whenLoaded('staggerLimits')),
            'trap_effects' => MonsterTrapEffectResource::collection($this->whenLoaded('trapEffects')),
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
            'sounds' => $this->whenLoaded('sounds', fn () => $this->sounds->pluck('filename')),
        ];
    }

    /**
     * Drop null thresholds (e.g. the Apex-only rows most monsters lack), or
     * return null when the whole group is absent.
     *
     * @param  array<string, int|null>  $values
     * @return array<string, int>|null
     */
    private function thresholdGroup(array $values): ?array
    {
        $present = array_filter($values, fn (?int $v): bool => $v !== null);

        return $present === [] ? null : $present;
    }
}
