<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Rank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gathering extends BaseModel
{
    protected $table = 'gathering';

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

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @param  Builder<Gathering>  $query */
    public function scopeOfRank(Builder $query, Rank $rank): void
    {
        $query->where('rank', $rank);
    }
}
