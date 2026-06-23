<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wyporium extends BaseModel
{
    protected $table = 'wyporium';

    /** @return BelongsTo<Item, $this> */
    public function itemIn(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_in_id');
    }

    /** @return BelongsTo<Item, $this> */
    public function itemOut(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_out_id');
    }

    /** @return BelongsTo<Quest, $this> */
    public function unlockQuest(): BelongsTo
    {
        return $this->belongsTo(Quest::class, 'unlock_quest_id');
    }
}
