<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Decoration extends BaseModel
{
    protected $table = 'decorations';

    public $incrementing = false; // primary key is shared with items.id

    /**
     * Shared-primary-key link to the base item (name, rarity, icon, ...).
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id', 'id');
    }

    /**
     * Skill trees this decoration grants points toward.
     *
     * @return BelongsToMany<SkillTree, $this>
     */
    public function skillTrees(): BelongsToMany
    {
        return $this->belongsToMany(SkillTree::class, 'item_skill_tree', 'item_id', 'skill_tree_id')
            ->withPivot('point_value');
    }
}
