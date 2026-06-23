<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monster extends BaseModel
{
    protected $table = 'monsters';

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
