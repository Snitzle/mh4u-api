<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends BaseModel
{
    protected $table = 'locations';

    /** @return HasMany<Quest, $this> */
    public function quests(): HasMany
    {
        return $this->hasMany(Quest::class);
    }

    /** @return HasMany<Gathering, $this> */
    public function gathering(): HasMany
    {
        return $this->hasMany(Gathering::class);
    }

    /** @return HasMany<MonsterHabitat, $this> */
    public function habitats(): HasMany
    {
        return $this->hasMany(MonsterHabitat::class);
    }

    /**
     * Monsters that roam this location, with their area/movement pivot data.
     *
     * @return BelongsToMany<Monster, $this>
     */
    public function monsters(): BelongsToMany
    {
        return $this->belongsToMany(Monster::class, 'monster_habitats', 'location_id', 'monster_id')
            ->withPivot('start_area', 'move_area', 'rest_area');
    }
}
