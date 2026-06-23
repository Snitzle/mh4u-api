<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends BaseModel
{
    protected $table = 'skills';

    /** @return BelongsTo<SkillTree, $this> */
    public function skillTree(): BelongsTo
    {
        return $this->belongsTo(SkillTree::class);
    }
}
