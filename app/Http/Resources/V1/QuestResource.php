<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use App\Models\Quest;
use App\Models\QuestReward;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Quest
 */
class QuestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'goal' => $this->goal,
            'hub' => $this->hub,
            'type' => $this->type,
            'stars' => $this->stars,
            'time_limit' => $this->time_limit,
            'fee' => $this->fee,
            'reward' => $this->reward,
            'hrp' => $this->hrp,
            'sub_goal' => $this->sub_goal,
            'sub_reward' => $this->sub_reward,
            'sub_hrp' => $this->sub_hrp,
            'location' => $this->whenLoaded('location', fn () => LocationSummaryResource::make($this->location)),
            'monsters' => MonsterSummaryResource::collection($this->whenLoaded('monsters')),
            'prerequisites' => QuestSummaryResource::collection($this->whenLoaded('prerequisites')),
            'rewards' => $this->whenLoaded(
                'rewards',
                fn () => $this->rewards
                    ->groupBy(fn (QuestReward $reward): string => $reward->reward_slot->value)
                    ->map(fn ($group) => QuestRewardResource::collection($group)),
            ),
        ];
    }
}
