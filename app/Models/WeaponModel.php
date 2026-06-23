<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeaponModel extends BaseModel
{
    protected $table = 'weapon_models';

    /** @return BelongsTo<Weapon, $this> */
    public function weapon(): BelongsTo
    {
        return $this->belongsTo(Weapon::class);
    }
}
