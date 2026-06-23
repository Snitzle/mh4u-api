<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Combination extends BaseModel
{
    protected $table = 'combinations';

    /** @return BelongsTo<Item, $this> */
    public function createdItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'created_item_id');
    }

    /** @return BelongsTo<Item, $this> */
    public function itemOne(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_1_id');
    }

    /** @return BelongsTo<Item, $this> */
    public function itemTwo(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_2_id');
    }
}
