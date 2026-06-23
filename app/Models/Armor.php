<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ArmorSlot;
use App\Enums\Gender;
use App\Enums\HunterType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * @return BelongsToMany<SkillTree, $this>
     */
    public function skillTrees(): BelongsToMany
    {
        return $this->belongsToMany(SkillTree::class, 'item_skill_tree', 'item_id', 'skill_tree_id')
            ->withPivot('point_value');
    }
}
