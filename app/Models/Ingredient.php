<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends BaseModel
{
    protected $table = 'ingredients';

    /** @return BelongsTo<Quest, $this> */
    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }
}
