<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot for the item <-> skill tree relationship, carrying the points an item
 * grants toward a skill tree (may be negative).
 *
 * @property int $point_value
 */
class ItemSkillTree extends Pivot
{
    protected $table = 'item_skill_tree';

    public $timestamps = false;
}
