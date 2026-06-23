<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodCombo extends BaseModel
{
    protected $table = 'food_combos';

    /** @return BelongsTo<FelyneSkill, $this> */
    public function skillOne(): BelongsTo
    {
        return $this->belongsTo(FelyneSkill::class, 'skill1_id');
    }

    /** @return BelongsTo<FelyneSkill, $this> */
    public function skillTwo(): BelongsTo
    {
        return $this->belongsTo(FelyneSkill::class, 'skill2_id');
    }

    /** @return BelongsTo<FelyneSkill, $this> */
    public function skillThree(): BelongsTo
    {
        return $this->belongsTo(FelyneSkill::class, 'skill3_id');
    }
}
