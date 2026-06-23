<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArmorModel extends BaseModel
{
    protected $table = 'armor_models';

    /** @return BelongsTo<Armor, $this> */
    public function armor(): BelongsTo
    {
        return $this->belongsTo(Armor::class);
    }
}
