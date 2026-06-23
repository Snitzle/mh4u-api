<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ArmorSlot;
use App\Enums\Gender;
use App\Enums\HunterType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property ArmorSlot $slot
 * @property Gender $gender
 * @property HunterType $hunter_type
 * @property string|null $icon_name
 */
class Armor extends BaseModel
{
    protected $table = 'armor';

    public $incrementing = false; // primary key is shared with items.id

    protected function casts(): array
    {
        return [
            'slot' => ArmorSlot::class,
            'gender' => Gender::class,
            'hunter_type' => HunterType::class,
        ];
    }

    /**
     * Shared-primary-key link to the base item (name, rarity, price, ...).
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id', 'id');
    }

    /**
     * Skill trees this armor piece grants points toward.
     *
     * @return BelongsToMany<SkillTree, $this, ItemSkillTree>
     */
    public function skillTrees(): BelongsToMany
    {
        return $this->belongsToMany(SkillTree::class, 'item_skill_tree', 'item_id', 'skill_tree_id')
            ->using(ItemSkillTree::class)
            ->withPivot('point_value');
    }

    /**
     * Crafting recipe lines (the armor piece shares its id with its item).
     *
     * @return HasMany<Component, $this>
     */
    public function componentsRequired(): HasMany
    {
        return $this->hasMany(Component::class, 'created_item_id', 'id');
    }
}
