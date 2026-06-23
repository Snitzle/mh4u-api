<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeaponSharpness extends BaseModel
{
    protected $table = 'weapon_sharpness';

    /** @return BelongsTo<Weapon, $this> */
    public function weapon(): BelongsTo
    {
        return $this->belongsTo(Weapon::class);
    }
}
