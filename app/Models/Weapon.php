<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WeaponType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property WeaponType $wtype
 * @property bool|null $final
 */
class Weapon extends BaseModel
{
    protected $table = 'weapons';

    public $incrementing = false; // primary key is shared with items.id

    protected function casts(): array
    {
        return [
            'wtype' => WeaponType::class,
            'final' => 'boolean',
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

    /** @return BelongsTo<Weapon, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<Weapon, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * The Hunting Horn melodies playable with this weapon's note set.
     *
     * @return HasMany<HornMelody, $this>
     */
    public function hornMelodies(): HasMany
    {
        return $this->hasMany(HornMelody::class, 'notes', 'horn_notes');
    }

    /**
     * Crafting recipe lines (the weapon shares its id with its item).
     *
     * @return HasMany<Component, $this>
     */
    public function componentsRequired(): HasMany
    {
        return $this->hasMany(Component::class, 'created_item_id', 'id');
    }

    /**
     * Root weapons (the start of an upgrade tree).
     *
     * @param  Builder<Weapon>  $query
     */
    public function scopeTreeRoots(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    /**
     * Final-form weapons.
     *
     * @param  Builder<Weapon>  $query
     */
    public function scopeFinal(Builder $query): void
    {
        $query->where('final', true);
    }
}
