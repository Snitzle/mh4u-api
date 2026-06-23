<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Rank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Rank $rank
 */
class HuntingReward extends BaseModel
{
    protected $table = 'hunting_rewards';

    protected function casts(): array
    {
        return [
            'rank' => Rank::class,
        ];
    }

    /** @return BelongsTo<Item, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** @return BelongsTo<Monster, $this> */
    public function monster(): BelongsTo
    {
        return $this->belongsTo(Monster::class);
    }

    /** @param  Builder<HuntingReward>  $query */
    public function scopeOfRank(Builder $query, Rank $rank): void
    {
        $query->where('rank', $rank);
    }
}
