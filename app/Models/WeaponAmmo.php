<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeaponAmmo extends BaseModel
{
    protected $table = 'weapon_ammo';

    /** @return BelongsTo<Weapon, $this> */
    public function weapon(): BelongsTo
    {
        return $this->belongsTo(Weapon::class);
    }

    /** @return BelongsTo<Item, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
