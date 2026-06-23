<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Component extends BaseModel
{
    protected $table = 'components';

    /**
     * The item produced by this recipe line.
     *
     * @return BelongsTo<Item, $this>
     */
    public function createdItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'created_item_id');
    }

    /**
     * The ingredient item consumed by this recipe line.
     *
     * @return BelongsTo<Item, $this>
     */
    public function componentItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'component_item_id');
    }
}
