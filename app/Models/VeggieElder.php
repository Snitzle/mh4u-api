<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeggieElder extends BaseModel
{
    protected $table = 'veggie_elder';

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<Item, $this> */
    public function offerItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'offer_item_id');
    }

    /** @return BelongsTo<Item, $this> */
    public function receiveItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'receive_item_id');
    }
}
