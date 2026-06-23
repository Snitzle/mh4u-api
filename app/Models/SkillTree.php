<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read ItemSkillTree|null $pivot
 */
class SkillTree extends BaseModel
{
    protected $table = 'skill_trees';

    /**
     * The individual skills unlocked at point thresholds within this tree.
     *
     * @return HasMany<Skill, $this>
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('required_skill_tree_points');
    }

    /**
     * Items (armor / charms / decorations) that grant points toward this tree.
     *
     * @return BelongsToMany<Item, $this, ItemSkillTree>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_skill_tree', 'skill_tree_id', 'item_id')
            ->using(ItemSkillTree::class)
            ->withPivot('point_value');
    }
}
