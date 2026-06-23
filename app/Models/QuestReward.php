<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RewardSlot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property RewardSlot $reward_slot
 */
class QuestReward extends BaseModel
{
    protected $table = 'quest_rewards';

    protected function casts(): array
    {
        return [
            'reward_slot' => RewardSlot::class,
        ];
    }

    /** @return BelongsTo<Quest, $this> */
    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }

    /** @return BelongsTo<Item, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
