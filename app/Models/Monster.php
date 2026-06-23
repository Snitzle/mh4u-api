<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Monster extends BaseModel
{
    use Searchable;

    protected $table = 'monsters';

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'name_de' => $this->name_de,
            'name_fr' => $this->name_fr,
            'name_es' => $this->name_es,
            'name_it' => $this->name_it,
            'name_jp' => $this->name_jp,
        ];
    }

    /** @return HasMany<MonsterAilment, $this> */
    public function ailments(): HasMany
    {
        return $this->hasMany(MonsterAilment::class);
    }

    /** @return HasMany<MonsterDamage, $this> */
    public function damage(): HasMany
    {
        return $this->hasMany(MonsterDamage::class);
    }

    /** @return HasMany<MonsterWeakness, $this> */
    public function weaknesses(): HasMany
    {
        return $this->hasMany(MonsterWeakness::class);
    }

    /** @return HasMany<MonsterStatus, $this> */
    public function statuses(): HasMany
    {
        return $this->hasMany(MonsterStatus::class);
    }

    /** @return HasMany<MonsterHabitat, $this> */
    public function habitats(): HasMany
    {
        return $this->hasMany(MonsterHabitat::class);
    }

    /** @return HasMany<HuntingReward, $this> */
    public function huntingRewards(): HasMany
    {
        return $this->hasMany(HuntingReward::class);
    }

    /** @return BelongsToMany<Quest, $this> */
    public function quests(): BelongsToMany
    {
        return $this->belongsToMany(Quest::class, 'monster_quest')
            ->withPivot('unstable');
    }
}
