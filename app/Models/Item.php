<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends BaseModel
{
    protected $table = 'items';

    /** @return HasOne<Weapon, $this> */
    public function weapon(): HasOne
    {
        return $this->hasOne(Weapon::class, 'id', 'id');
    }

    /** @return HasOne<Armor, $this> */
    public function armor(): HasOne
    {
        return $this->hasOne(Armor::class, 'id', 'id');
    }

    /** @return HasOne<Decoration, $this> */
    public function decoration(): HasOne
    {
        return $this->hasOne(Decoration::class, 'id', 'id');
    }

    /**
     * Skill trees this item (armor/charm/decoration) grants points toward.
     *
     * @return BelongsToMany<SkillTree, $this>
     */
    public function skillTrees(): BelongsToMany
    {
        return $this->belongsToMany(SkillTree::class, 'item_skill_tree', 'item_id', 'skill_tree_id')
            ->withPivot('point_value');
    }

    /**
     * Monster carve/capture drops that yield this item.
     *
     * @return HasMany<HuntingReward, $this>
     */
    public function huntingRewards(): HasMany
    {
        return $this->hasMany(HuntingReward::class);
    }

    /**
     * Quest reward lines that yield this item.
     *
     * @return HasMany<QuestReward, $this>
     */
    public function questRewards(): HasMany
    {
        return $this->hasMany(QuestReward::class);
    }

    /**
     * Gathering spots where this item can be collected.
     *
     * @return HasMany<Gathering, $this>
     */
    public function gathering(): HasMany
    {
        return $this->hasMany(Gathering::class);
    }

    /**
     * Recipe lines listing what is required to craft this item.
     *
     * @return HasMany<Component, $this>
     */
    public function componentsRequired(): HasMany
    {
        return $this->hasMany(Component::class, 'created_item_id');
    }

    /**
     * Recipe lines where this item is itself an ingredient.
     *
     * @return HasMany<Component, $this>
     */
    public function usedInComponents(): HasMany
    {
        return $this->hasMany(Component::class, 'component_item_id');
    }

    /**
     * Combinations that produce this item.
     *
     * @return HasMany<Combination, $this>
     */
    public function combinationsProducing(): HasMany
    {
        return $this->hasMany(Combination::class, 'created_item_id');
    }
}
